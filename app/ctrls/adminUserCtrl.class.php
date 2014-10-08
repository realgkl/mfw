<?php
/**
 * @desc 后台用户管理控制器
 * @author gkl
 * @since 20140729
 */
class adminUserCtrl extends baseCtrl
{	
	/**
	 * @desc 后台登录
	 */
	public function loginAction()
	{		
		if ( $this->request->isPost() === false )
		{
			$this->request->html404();
		}
		$act = new loginAct();
		$res = $act->checkToken();
		if ( $res === false )
		{			
			$act->setLoginError( '会话超时，请重新登录。' );
			$this->request->jump( '/admin/login' );
			mfwCommon::__exit();
		}
		$this->request->session_reset();
		$res = $act->login();
		if ( $res !== 1 )
		{
			if ( $res === -1 )
			{
				$act->setLoginError( '用户名或密码不正确，请重新登录。' );
			}
			else if ( $res === -2 )
			{
				$act->setLoginError( '重试次数过多，账户冻结。' );
			}
			else
			{
				$act->setLoginError( '登录失败。' );
			}
			$this->request->jump( '/admin/login' );
			mfwCommon::__exit();
		}		
		$act->setUserSession();
		$this->request->jump( '/admin' );
		mfwCommon::__exit();
	}
	
	/**
	 * @desc 后台注销
	 */
	public function logoffAction()
	{
		$this->request->session_reset();
		$this->request->jump( '/admin' );
		mfwCommon::__exit();
	}
	
	/**
	 * @desc 后台用户列表
	 * @since 20140731 gkl
	 */
	public function listAction()
	{
		$page = isset( $_GET['page'] ) ? intval( $_GET['page'] ) : 1;
		$limit = 15;
		$this->view->setCss( 'grid.css' );
		$this->__iniAdmin();
		$this->view->assign( 'page', $page );
		$this->view->assign( 'limit', $limit );
		$this->view->assign( 'nav_bar_title', '用户列表' );
		$this->view->assign( 'left_nav_cur', '/admin/user/list' );
	}
	
	/**
	 * @desc 后台获取用户列表数据ajax
	 * @since 20140806 gkl
	 */
	public function listDataAjaxAction()
	{
		if ( $this->request->isAjax() === false )
		{
			$this->request->html404();
		}
		$page = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
		$limit = isset( $_POST['limit'] ) ? intval( $_POST['limit'] ) : 10;
		$orderby = isset( $_POST['orderby'] ) ? trim( $_POST['orderby'] ) : false;
		$sql_orderby = '';
		if ( $orderby !== false && trim( $orderby ) !== '' )
		{
			$sql_orderby = "{$orderby}";
		}
		$model = new adminUserModel();
		$total = $model->getCount();
		if ( $page * $limit > $total )
		{
			$page = ceil( $total / $limit );
		}
		$users = $model->getAllUser($limit, $page, array(), array(), $sql_orderby );
		echo json_encode( array(
			'total'	=> $total,
			'rows' => $users,
			'page'	=> $page,	
		) );
		mfwCommon::__exit();
	}
}
<?php
/**
 * @desc 后台管理控制器
 * @author gkl
 * @since 20140728
 */
class adminCtrl extends mfwWebCtrl
{
	/**
	 * @see mfwWebCtrl::beforeAction()
	 */
	public function beforeAction()
	{
		parent::beforeAction();
		if ( $this->request->getClass() === 'admin' && $this->request->getMethod() === 'login' )
		{
		}
		else if ( $this->request->getClass() === 'sys' )
		{
		}
		else
		{
			$act = new loginAct();
			$is_login = $act->isLogin();
			if ( $is_login === false )
			{
				$this->request->render( 'admin', 'login' );
				mfwCommon::__exit();
			}
		}
	}
	
	/**
	 * @desc 登录页
	 */
	public function loginAction()
	{
		$token = md5(microtime( true ) );
		$act = new loginAct();
		$act->setToken( $token );
		$this->view->assign( 'token', $token );
		$this->view->assign( 'error', $act->getLoginError() );
		$this->view->setTitle( 'dinner管理后台' );
		$this->view->setKeywords( 'dinner管理后台' );
		$this->view->setCss( array(
				'main.css',
				'login.css',
				'jqueryui/jquery-ui.min.css',
		) );
		$this->view->setJs( array(
				'jquery-1.11.1.min.js',
				'jquery-ui.min.js',
				'jquery.messager.custom.js',
		) );
	}
	
	/**
	 * @desc 欢迎页
	 */
	public function welcomeAction()
	{
		$this->view->setTitle( 'dinner管理后台' );
		$this->view->setKeywords( 'dinner管理后台' );
	}
}
<?php
/**
 * @desc 后台用户管理控制器
 * @author gkl
 * @since 20140729
 */
class adminUserCtrl extends mfwWebCtrl
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
}
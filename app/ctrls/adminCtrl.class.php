<?php
/**
 * @desc 后台管理控制器
 * @author gkl
 * @since 20140728
 */
class adminCtrl extends baseCtrl
{
	/**
	 * @see mfwWebCtrl::beforeAction()
	 */
	public function beforeAction()
	{
		parent::beforeAction();
		$this->__iniAdmin();
	}
	
	/**
	 * @desc 登录页
	 */
	public function loginAction()
	{
		$this->view->setLayout( '/public/layout' );
		$token = md5(microtime( true ) );
		$act = new loginAct();
		$act->setToken( $token );
		$this->view->assign( 'token', $token );
		$this->view->assign( 'error', $act->getLoginError() );
		$this->view->setTitle( 'dinner管理后台' );
		$this->view->setKeywords( 'dinner管理后台' );
		$this->view->setCss( array(
			'login.css',
		) );
	}
	
	/**
	 * @desc 欢迎页
	 */
	public function welcomeAction()
	{
		$this->view->setTitle( 'dinner管理后台' );
		$this->view->setKeywords( 'dinner管理后台' );
		$this->view->assign( 'nav_bar_title', '管理后台' );
	}
}
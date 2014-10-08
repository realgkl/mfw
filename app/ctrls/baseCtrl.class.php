<?php
/**
 * @desc 基类控制器
 * @author gkl
 * @since 20140731
 */
class baseCtrl extends mfwWebCtrl
{
	/**
	 * @see mfwWebCtrl::beforeAction()
	 */
	public function beforeAction()
	{
		parent::beforeAction();
		$this->view->setJs( array(
			'jquery-1.11.1.min.js',
			'jquery-ui.min.js',
			'jquery.custom.js',
		) );
		$this->view->setCss( array(
			'jqueryui/jquery-ui.min.css',
		) )	;	
	}
	
	/**
	 * @desc 后台管理控制器操作
	 */
	protected function __iniAdmin()
	{
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
		$this->view->setCss( array(
				'main.css',
				'header.css',
				'left_nav.css',
		) );
		$this->view->setLayout( 'public/admin_layout' );
	}
}
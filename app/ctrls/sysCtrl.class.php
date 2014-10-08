<?php
/**
 * @desc 系统控制器
 * @author gkl
 * @since 20140716 gkl
 *
 */
class sysCtrl extends mfwWebCtrl
{
	public function html404Action()
	{
		self::contentType();
		self::code(404);
		$this->view->setCss( array(
				'main.css',
				'404.css',
		) );
		$this->view->setJs( array(
				'jquery-1.11.1.min.js',
				'jquery.lavalamp.js',
				'rapheal-min.js',
				'404.js',
		) );
		$this->view->setLayout( '/public/layout' );
		$this->view->setView( 'public/404' );
	}
}
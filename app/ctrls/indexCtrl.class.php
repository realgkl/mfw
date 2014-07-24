<?php
/**
 * @desc 首页控制器
 * @author gkl
 * @since 20140709 gkl
 *
 */
class indexCtrl extends mfwWebCtrl
{
	public function html404()
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
		$this->view->setView( 'public/404' );
	}
}
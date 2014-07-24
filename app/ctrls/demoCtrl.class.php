<?php
/**
 * @desc demo控制器
 * @author gkl
 * @since 20140709 gkl
 *
 */
class demoCtrl extends mfwWebCtrl
{
	public function indexAction()
	{
		var_dump( 'Hello World!' );
		echo "<h1>Hello World!</h1>";
		$this->view->setTitle( 'Hello World!' );
		$this->view->setKeywords( '123' );
		$this->view->setCss( 'main.css' );
		$this->view->setJs( 'jquery-1.11.1.min.js' );
		// 不显示模版
		// $this->view->noView();
		$this->view->setView( 'public/demo' );
		$model = new demoModel();
		$res = $model->test();
		var_dump( $res );
	}
}

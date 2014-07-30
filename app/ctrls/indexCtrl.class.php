<?php
/**
 * @desc 首页控制器
 * @author gkl
 * @since 20140709 gkl
 *
 */
class indexCtrl extends mfwWebCtrl
{
	/**
	 * @desc 首页
	 */
	public function indexAction()
	{
		var_dump( '1' );
		$this->view->noView();
	}
}
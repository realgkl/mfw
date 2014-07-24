<?php
/**
 * @desc demo model
 * @author gkl
 * @since 20140722
 */
class demoModel extends mfwDbModelMysql
{
	public function test()
	{
		$sql = 'show tables like ' .mfwConst::DB_OCC . '0';
		$params = array(
			'%admin%',
		);
		return $this->getRow( $sql, $params );
	}
}
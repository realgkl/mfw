<?php
/**
 * @desc 登录模块
 * @author gkl
 * @since 20140724
 */
class loginModel extends mfwDbModelMysql
{
	public function login( $user )
	{
		$sql = "
				select
					f_id, f_name, f_login_id, f_disable_flag, f_unfrozen_time, f_try_num, f_pass
				from t_admin_user
				where
					f_login_id = " . mfwConst::DB_OCC . "0 and 
					f_disable_flag = " . mfwConst::DB_OCC . "1
		";
		$params = array(
			$user,
			0,
		);
		return $this->getRow( $sql, $params );
	}
	
	public function loginSucc( $data )
	{
		$params = array();
		$sql = $this->__parse_update_sql( $data, 't_admin_user', 'f_id', $params );
		if ( $sql === false )
		{
			return false;
		}
		$res = $this->exec( $sql, $params );
		if ( $res !== false )
		{
			return true;
		}
		return false;
	}
	
	/**
	 * @desc 登录失败操作<br/>
	 * 重试次数+1<br/>
	 * 当重试次数=3冻结账户
	 * @param string $f_id 用户id
	 * @param integer $try_num 重试次数
	 * @param integer $frozen 冻结时间（秒）
	 * @return boolean
	 */
	public function loginFalid( $f_id, $try_num, $frozen )
	{
		$this->begin();
		$sql = '
			update `t_admin_user`
			set
				`f_try_num` = `f_try_num` + 1 
			where
				`f_id` = ' . mfwConst::DB_OCC . '0 and
				`f_try_num` < ' . mfwConst::DB_OCC . '1
		';
		$params = array(
			$f_id,
			$try_num,
		);
		$res = $this->exec( $sql, $params );
		if ( $res !== false )
		{
			$sql = '
					update `t_admin_user`
					set
						`f_unfrozen_time` = ' . mfwConst::DB_OCC . '0,
						`f_try_num` = ' . mfwConst::DB_OCC . '1
					where
						`f_id` = ' . mfwConst::DB_OCC . '2 and
						`f_try_num` >= ' . mfwConst::DB_OCC . '3
			';
			$params = array(
				date( 'Y-m-d H:i:s', time() +$frozen ),
				0,
				$f_id,
				$try_num,
			);
			$res = $this->exec( $sql, $params );
			if ( $res !== false )
			{
				return $this->commit();
			}
		}
		$this->rollback();
		return false;
	}	
}
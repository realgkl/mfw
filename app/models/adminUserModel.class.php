<?php
/**
 * @desc 后台用户模块
 * @author gkl
 * @since 20140724
 */
class adminUserModel extends mfwDbModelMysql
{
		public function getCount( $where = array(), $oper = array() )
		{
			$sql ="
				select count(*) as count from t_admin_user
			";
			$params = array();
			$sql_where_part = array();
			$inc = 0;
			if ( !empty( $where ) )
			{
				foreach ( $where as $field => $value )
				{
					$cur_oper = isset( $oper[$inc] ) ? $oper[$inc] : '=';
					if ( !in_array( $cur_oper, array( 'is null', 'is not null' ) ) )
					{
						$sql_where_part[] = "`{$field}`{$cur_oper}" . mfwConst::DB_OCC.$inc;
						$params[] = $value;
					}
					else
					{
						$sql_where_part[] = "`{$field}`{$cur_oper}";
					}
					$inc++;
				}
			}
			unset( $inc );
			$sql .= implode( ' and ', $sql_where_part );
			$res = $this->getRow( $sql, $params );
			if ( $res !== false )
			{
				return intval( $res['count'] );
			}
			return 0;
		}
	
		public function getAllUser( $limit, $page, $where = array(), $oper = array(), $orderby = '' )
		{
			if ( $limit <= 0 )
			{
				return false;
			}
			$sql = "
				select f_id, f_name, f_login_id, f_disable_flag, f_unfrozen_time
				from t_admin_user
			";
			$params = array();
			$sql_where_part = array();
			$inc = 0;
			if ( !empty( $where ) )
			{
				foreach ( $where as $field => $value )
				{
					$cur_oper = isset( $oper[$inc] ) ? $oper[$inc] : '=';
					if ( !in_array( $cur_oper, array( 'is null', 'is not null' ) ) )
					{
						$sql_where_part[] = "`{$field}`{$cur_oper}" . mfwConst::DB_OCC.$inc;
						$params[] = $value;
					}
					else
					{
						$sql_where_part[] = "`{$field}`{$cur_oper}";
					}					
					$inc++;
				}
			}
			unset( $inc );
			$sql .= implode( ' and ', $sql_where_part );
			if ( $page > 1 )
			{
				$begin = ( $page - 1 ) * $limit;
			}
			else
			{
				$begin = 0;
			}
			if ( $orderby !== '' )
			{
				$sql .= "\norder by {$orderby}";
			}
			$sql .= "\nlimit {$begin},{$limit}";
			return $this->getAll( $sql, $params );
		}
}
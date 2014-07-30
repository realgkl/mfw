<?php
/**
 * @desc db common 函数类
 */
class mfwDbCommon
{
	/**
	 * @desc 读取数据时，如果是字符类型进行反转义
	 */	
	protected static function __getValueFromDb( $value )
	{
		if ( is_string( $value ) )
		{
			// 去除字符串两边空格
			$value = trim( $value );
			// 如果是html把html实体转换成字符，单双引号都转换(不转换中文字)
			$value = htmlspecialchars_decode( $value, ENT_QUOTES );
			// 去除转义符
			$value = stripslashes( $value );
			return $value;
		}
		return $value;
	}
	
	/**
	 * @desc 写入数据时，如果是字符类型进行转义
	 */
	protected static function __setValueToDb( $value )
	{
		if ( is_string( $value ) )
		{
			// 去除字符串两边空格
			$value = trim( $value );
			// 如果开启魔术引号, 去除反斜杠
			if ( @get_magic_quotes_gpc() )
			{
				$value = stripslashes( $value );
			}
			// 加斜杠转义
			$value = addslashes( $value );
			// 如果是html把字符转换成html实体，单双引号都转换(不转换中文字)
			$value = htmlspecialchars( $value, ENT_QUOTES );
			// 转义通配符
			// $value = str_replace( '%', '\%', $value );
			// $value = str_replace( '_', '\_', $value );
			return $value;
		}
		return $value;
	}
	
	/**
	 * @desc 写入数据时，对于数组进行转义，防止非法字符和SQL注入
	 */
	public static function filter_params ( $params )
	{
		if ( is_array( $params) && !empty( $params ) )
		{
			foreach ( $params as &$v )
			{
				$v = self::__setValueToDb( $v );
			}
		}
		else if ( is_string( $params ) )
		{
			$params = self::__setValueToDb( $params );
		}
		return $params;
	}
}

/**
 * @desc db model 接口
 * @author gkl
 * @since 20130724
 */
interface mfwDbModelIntf
{
	/**
	 * @desc 获取所有数据
	 * @param string $sql sql语句
	 * @param array $params 搜索条件参数
	 * @param boolean $need_filter 是否需要过滤参数
	 * @param boolean $err_die 发生错误是否中断
	 * @param string $err_no 错误号（回调参数）
	 * @param string $err_msg 错误信息（回调参数）
	 * @return boolean|array
	 */
	public function getAll( $sql, $params = array(), $need_filter = true, $err_die = true, &$err_no ='', &$err_msg = '' );
	
	/**
	 * @desc 获取一行数据
	 * @param string $sql sql语句
	 * @param array $params 搜索条件参数
	 * @param boolean $need_filter 是否需要过滤参数
	 * @param boolean $err_die 发生错误是否中断
	 * @param string $err_no 错误号（回调参数）
	 * @param string $err_msg 错误信息（回调参数）
	 * @return boolean|array
	 */
	public function getRow( $sql, $params = array(), $need_filter = true, $err_die = true, &$err_no ='', &$err_msg = '' );
	
	/**
	 * @desc 执行数据[update,delete,insert]
	 * @param string $sql sql语句
	 * @param array $params 搜索条件参数
	 * @param boolean $need_filter 是否需要过滤参数
	 * @param boolean $err_die 发生错误是否中断
	 * @param string $err_no 错误号（回调参数）
	 * @param string $err_msg 错误信息（回调参数）
	 * @return boolean
	 */
	public function exec( $sql, $params = array(), $need_filter = true, $err_die = true, &$err_no ='', &$err_msg = '' );
	
	/**
	 * @desc 开启事务
	 * @return boolean
	 */
	public function begin();
	
	/**
	 * @desc 事务回滚
	 * @return boolean
	 */
	public function rollback();
	
	/**
	 * @desc 提交事务
	 * @return boolean
	 */
	public function commit();
}
/**
 * @desc db model 基类
 * @author gkl
 * @since 20140717
 */
class mfwDbModelBase
{
	/**
	 * @desc 事务开启计数
	 * @var integer
	 */
	protected static $trans_num = 0;
	
	/**
	 * @desc 是否显示执行时间
	 * @var boolean
	 */
	protected $show_time = false;
	
	/**
	 * @desc conn链接对象
	 * @var mfwConnBase
	 */
	protected $conn = null;
	
	/**
	 * @desc 生成有绑定参数的sql
	 */
	protected function __createSqlByParams( $sql, $params )
	{
		if ( !empty( $params ) )
		{
			$occ_no = 0;
			foreach ( $params as $v )
			{
				$occ = mfwConst::DB_OCC . $occ_no;
				$sql = preg_replace( '/' . $occ . '/', "'{$v}'", $sql, 1 );
				$occ_no++;
			}
		}
		return $sql;
	}
	
	/**
	 * @desc 抛出异常
	 */
	protected function __error( $sql = '', $err_die = false, &$err_no = '', &$err_msg = '' )
	{
		$err_arr = $this->conn->getConn()->errorInfo();
		$err_no = $err_arr[1];
		$err_msg = $err_arr[2];
		$msg = "\n[$err_arr[0]]ErrorNo {$err_no} : {$err_msg}";
		if ( $sql != '' )
		{
			$msg .= "\n{$sql}";
		}
		if ( $err_die === true )
		{
			mfwCommon::__die( $msg );
		}
		else
		{
			throw new mfwException( $msg );
		}
	}
	
	/**
	 * @desc 是否存在{表}
	 */
	protected function __exists_table( $table_name )
	{
		return false;
	}
	
	/**
	 * @desc 用占位符绑定参数
	 * @param PDOStatement $query
	 * @param array $params 参数
	 */
	protected function __useOccBindParams( $query, $params )
	{
		if ( !empty( $params ) )
		{
			foreach ( $params as $k => $value )
			{
				if ( is_string( $value ) && strlen( $value ) > 1000 )
				{
					$query->bindParam( mfwConst::DB_OCC.$k, $value, PDO::PARAM_STR, strlen( $value ) );
				}
				else
				{
					$query->bindValue( mfwConst::DB_OCC.$k, $value );
				}
			}
		}
		return true;
	}
	
	/**
	 * @desc 显示sql 执行时间
	 * @param string $sql sql语句
	 * @param float $begin 开始时间
	 */
	protected function __showSqlTime( $sql, $begin )
	{
		$diff = mfwCommon::diff( $begin );
		$msg = $sql . ' | ' . $diff . ' s';
		mfwCommon::__echo( $msg, '<br/>' );
	}
	
	/**
	 * @desc 构造函数
	 */
	public function __construct ()
	{
		$this->show_time = false;
		if ( mfwGlobal::defined( 'SHOW_SQL_TIME' ) )
		{
			$this->show_time = mfwGlobal::getVar( 'SHOW_SQL_TIME' );
		}
	}
	
	/**
	 * @desc 判断是否存在{表}
	 */
	public function exists_table( $table_name )
	{
		return $this->__exists_table( $table_name );
	}
}

/**
 * @desc mysql model
 * @since 20140722 gkl
 */
class mfwDbModelMysql extends mfwDbModelBase implements mfwDbModelIntf
{
	/**
	 * @desc 构造函数
	 */
	public function __construct()
	{
		parent::__construct();
		$this->conn = new mfwConnMysql();
	}
	
	/**
	 * @see mfwDbModelBase::__exists_table()
	 */
	protected function __exists_table( $table_name )
	{
		$sql = "
			show tables like '{$table_name}'
		";
		$res = $this->getRow( $sql );
		if ( $res !== false  )
		{
			return true;
		}
		return false;
	}
	
	/**
	* @desc 拼insert sql语句
	* @param array $datas 要插入的数组
	* @param string $table_name 表名
	* @param array $params 返回的参数数组
	* @return boolean|string 成功返回sql语句，失败返回false
	*/
	protected function __parse_insert_sql ( $datas, $table_name, &$params )
	{
		$params = array();
		if ( empty( $datas ) )
		{
			return false;
		}
		$sql = "insert into `{$table_name}`\n";
		$fields = array_keys( $datas[0] );
		if ( empty( $fields ) )
			return false;
			foreach ( $fields as &$v )
			{
				$v = "`{$v}`";
			}
			$sql .= '(' . implode( ',', $fields ) . ') values ';
			foreach ( $datas as $data )
			{
				$sql .= "\n(" . implode( ',', array_fill( 0, count( $data ), '?' ) ) . "),";
					foreach ( $data as $v )
						{
							$params[] = $v;
			}
		}
		if ( substr( $sql, strlen( $sql ) - 1, 1 ) == ',' )
		{
			$sql = substr( $sql, 0, strlen( $sql ) - 1 );
		}
		return $sql;
	}
	
	/**
	 * @desc 拼update sql语句
	 * @param array $data 单条数据
	 * @param string $table_name 表名
	 * @param string $key_field 定位主键或唯一索引
	 * @param array $params 返回的参数数组
	 * @return boolean|string 成功返回sql失败返回false
	 */
	protected function __parse_update_sql ( $data, $table_name, $key_field, &$params )
	{
		$params = array();
		if ( empty( $data ) )
		{
			return false;
		}
		$fields = array_keys( $data );
		if ( empty( $fields ) )
		{
				return false;
		}
		$key_index_arr = array();
		$key_field_arr = array();
		if ( is_array( $key_field ) )
		{
			$key_field_arr = $key_field;
		}
		else
		{
			$key_field_arr[] = $key_field;
		}
		// 取得主键字段的序号
		foreach ( $fields as $k => &$v )
		{
			$v = strtolower( $v );
			if ( in_array( $v, $key_field_arr ) )
			{
				array_splice( $fields, $k, 1 );
				$key_index_arr[] = $k;
			}
		}
		// 主键字段为空返回失败
		if ( empty( $key_index_arr ) )
		{
			return false;
		}
		$sql = "update `{$table_name}` set ";
		$inc = 0;
		$sql_update_part = array();
		foreach ( $data as $k => &$v )
		{
		// 非定位主键或唯一索引
			if ( !in_array( $k, $key_field_arr ) )
			{
				$sql_update_part[] = "`{$k}` = " . mfwConst::DB_OCC . $inc;
				$params[] = $v;
				$inc++;
			}
		}
		$sql .= implode( ',', $sql_update_part );
		unset( $sql_update_part ); 
		$sql_where_part = array();
		foreach ( $key_field_arr as $k => $v )
		{
			$sql_where_part[] = "`{$v}` = " . mfwConst::DB_OCC . $inc;;
			$params[] = $data[$v];
			$inc++;
		}
		$sql .= " where " . implode( ' and ', $sql_where_part );
		unset( $sql_where_part );
		unset( $inc );
		return $sql;
	}
	
	/**
	 * @see mfwDbModelIntf::getAll()
	 */
	public function getAll( $sql, $params = array(), $need_filter = true, $err_die = false, &$err_no ='', &$err_msg = '' )
	{
		if ( $this->show_time )
		{
			$begin = mfwCommon::diff();
		}
		if ( $need_filter === true )
		{
			$params = mfwDbCommon::filter_params( $params );
		}
		$query = $this->conn->getConn()->prepare( $sql );
		$this->__useOccBindParams( $query, $params );
		$res = $query->execute();
		if ( $res )
		{
			if ( $this->show_time )
			{
				$sql = $this->__createSqlByParams( $sql, $params );
				$this->__showSqlTime( $sql, $begin );
			}
			return $query->fetchAll();
		}
		else
		{
			$this->rollback();
			$sql = $this->__createSqlByParams( $sql, $params );
			$this->__error( $sql, $err_die, $err_no, $err_msg );
		}
		return false;
	}

	/**
	 * @see mfwDbModelIntf::getRow()
	 */
	public function getRow( $sql, $params = array(), $need_filter = true, $err_die = false, &$err_no ='', &$err_msg = '' )
	{
		if ( $this->show_time )
		{
			$begin = mfwCommon::diff();
		}
		if ( $need_filter === true )
		{
			$params = mfwDbCommon::filter_params( $params );
		}
		$query = $this->conn->getConn()->prepare( $sql );
		$this->__useOccBindParams( $query, $params );
		$res = $query->execute();
		if ( $res )
		{
			if ( $this->show_time )
			{
				$sql = $this->__createSqlByParams( $sql, $params );
				$this->__showSqlTime( $sql, $begin );
			}
			return $query->fetch();
		}
		else
		{
			$this->rollback();
			$sql = $this->__createSqlByParams( $sql, $params );
			$this->__error( $sql, $err_die, $err_no, $err_msg );
		}
		return false;
	}

	/**
	 * @see mfwDbModelIntf::exec()
	 */
	public function exec( $sql, $params = array(), $need_filter = true, $err_die = false, &$err_no ='', &$err_msg = '' )
	{
		if ( $this->show_time )
		{
			$begin = mfwCommon::diff();
		}
		if ( $need_filter === true )
		{
			$params = mfwDbCommon::filter_params( $params );
		}
		$this->conn->getConn()->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
		$query = $this->conn->getConn()->prepare( $sql );
		$this->__useOccBindParams( $query, $params );
		$res = $query->execute();
		if ( $res )
		{
			if ( $this->show_time )
			{
				$sql = $this->__createSqlByParams( $sql, $params );
				$this->__showSqlTime( $sql, $begin );
			}
			return $query->rowCount();
		}
		else
		{
			$this->rollback();
			$sql = $this->__createSqlByParams( $sql, $params );
			$this->__error( $sql, $err_die, $err_no, $err_msg );
		}
		return false;
	}
	
	/**
	 * @see mfwDbModelIntf::begin()
	 */
	public function begin()
	{
		if ( self::$trans_num == 0 )
		{
			$res = $this->conn->getConn()->beginTransaction();
			if ( !$res )
			{
				$this->__error();
				return false;
			}
			self::$trans_num = 1;
		}
		return true;
	}
	
	/**
	 * @see mfwDbModelIntf::rollback()
	 */
	public function rollback ()
	{
		if ( self::$trans_num > 0 )
		{
			$res = $this->conn->getConn()->rollBack();
			self::$trans_num = 0;
			if ( !$res )
			{
				$this->__error();
				return false;
			}
		}
		return true;
	}
	 
	/**
	 * @see mfwDbModelIntf::commit()
	 */
	public function commit ()
	{
		if ( self::$trans_num > 0 )
		{
			$res = $this->conn->getConn()->commit();
			if ( !$res )
			{
				$this->rollback();
				$this->__error();
				return false;
			}
			self::$trans_num = 0;
		}
		return true;
	}
					/**
					 */
			/* public function recordExists( $table, $key_filed, $value )
			{
				if ( is_array( $key_filed ) && is_array( $value ) )
				{
					if ( count( $key_filed ) != count( $value ) )
					{
						return false;
					}
					$sql = "
					select 1 from `$table` where 1
					";
					$params = array();
					foreach ( $key_filed as $k => $v )
						{
						$sql .= " and `$v` = ?";
						$params[] = $value[$k];
			}
						}
						else
						{
						$sql = "
						select 1 from `$table` where `$key_filed` = ?
						";
						$params = array( $value );
			}
			$res = $this->getRow( $sql, $params );
	if ( $res === false )
	{
		return false;
	}
	return true;
} */
}
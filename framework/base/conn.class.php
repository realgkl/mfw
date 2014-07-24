<?php
/**
 * @desc 数据库链接抽象类
 * @author gkl
 * @since 20140722
 */
abstract class mfwConnAbs
{
	/**
	 * @desc 支持的数据库
	 */
	protected $support_db = array(
			mfwConst::DB_OCI,
			mfwConst::DB_MYSQL,
	);
}

/**
 * @desc 链接类基类
 */
class mfwConnBase extends mfwConnAbs
{
	/**
	 * @desc 检查是否支持数据库
	 */
	private function __checkSupportDB( $type )
	{
		if ( !in_array( $type, $this->support_db ) )
		{
			return false;
		}
		return true;
	}
	
	/**
	 * @desc 获取mysql数据库DSN
	 */
	private function __getMysqlDSN( $host, $db  )
	{
		return "mysql:host={$host};dbname={$db}";
	}
	
	/**
	* @desc 获取oracle数据库DSN
	*/
	private function __getOracleDSN( $host, $db, $charset )
	{
		return "oci:dbname={$host}/{$db};charset={$charset}";
	}
	
	/**
	 * @desc 构造函数
	 */
	public function __construct( $type, $host, $db, $user, $pass, $charset='utf-8', $errmode = PDO::ERRMODE_EXCEPTION )
	{
		$this->__checkSupportDB( $type );
		$options 																							= array();
		$options[PDO::ATTR_DEFAULT_FETCH_MODE]							= PDO::FETCH_ASSOC;		
		switch ( $type )
		{
			case mfwConst::DB_MYSQL:
				$dsn = $this->__getMysqlDSN( $host, $db );
				$options[PDO::MYSQL_ATTR_INIT_COMMAND]				= "set names {$charset}";
				$options[PDO::MYSQL_ATTR_USE_BUFFERED_QUERY]	= true;
				$options[PDO::ATTR_AUTOCOMMIT]									= true;
				break;
			case mfwConst::DB_OCI:
				$dsn = $this->__getOracleDSN( $host, $db, $charset );
				$options[PDO::ATTR_AUTOCOMMIT]									= true;
				break;
		}
		$this->conn = new PDO( $dsn, $user, $pass, $options );
		$this->connected = true;
	}
	
	/**
	 * @desc 析构函数
	 */
	public function __destruct()
	{
		unset( $this->conn );
		$this->connected = false;
	}
	
	/**
	 * @desc 设置options
	 */
	public function setOptions( $key, $value )
	{
		if ( $conn !== false )
		{
			return $this->conn->setAttribute( $key, $value );
		}
		return false;
	}
	
	/**
	 * @desc 返回pdo链接类
	 * @return PDO pdo链接类
	 */
	public function getConn()
	{
		return $this->conn;
	}
}

/**
 * @desc mysql 链接类
 * @author gkl
 * @since 20140722
 */
class mfwConnMysql extends mfwConnBase
{
	/**
	 * @desc 构造函数
	 */
	public function __construct()
	{
		$db_type = mfwGlobal::defined( 'DB_TYPE' ) ? mfwGlobal::getVar( 'DB_TYPE' ) : false;
		$db_host = mfwGlobal::defined( 'DB_HOST' ) ? mfwGlobal::getVar( 'DB_HOST' ) : false;
		$db_name = mfwGlobal::defined( 'DB_NAME' ) ? mfwGlobal::getVar( 'DB_NAME' ) : false;
		$db_user = mfwGlobal::defined( 'DB_USER' ) ? mfwGlobal::getVar( 'DB_USER' ) : false;
		$db_pass = mfwGlobal::defined( 'DB_PASS' ) ? mfwGlobal::getVar( 'DB_PASS' ) : false;
		$db_charset = mfwGlobal::defined( 'DB_CHARSET' ) ? mfwGlobal::getVar( 'DB_CHARSET' ) : 'utf-8';
		if ( $db_type === false || $db_type !== mfwConst::DB_MYSQL  || $db_host === false || $db_name === false || $db_user === false || $db_pass === false )
		{
			throw new mfwException( mfwConst::ERR_FW_CONN_PARAMS_ILLEAGEL );
		}
		parent::__construct( mfwConst::DB_MYSQL, $db_host, $db_name,
				$db_user, $db_pass,
				$db_charset, PDO::ERRMODE_EXCEPTION );
	}
}
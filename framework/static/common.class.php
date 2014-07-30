<?php
/**
 * @desc mfw 公用函数类
 * @author gkl
 * @since 20140709
 */
class mfwCommon
{
	/**
	 * @desc 全局model数组
	 * @var ArrayObject
	 */
	public static $models_arr = array();
	
	/**
	 * @desc 开启XHPROF插件
	 */
	protected static function openXHPROF()
	{
		if ( class_exists( 'profbaseXHPROF' ) )
		{
			//profbaseXHPROF::init();
			//register_shutdown_function( array( 'baseXHPROF', 'free' ) );
		}		
	}
	
	/**
	 * @desc 封装die
	 */
	public static function __die( $msg = '' )
	{
		self::__echo( $msg );
		self::__exit();
	}
	
	/**
	 * @desc 封装echo
	 */
	public static function __echo ( $msg = '', $enter = false, $charset = 'utf-8//IGNORE' )
	{
		if ( $enter !== false )
		{
			$msg .= "{$enter}";
		}
		$msg = iconv( 'utf-8', $charset, $msg );
		echo $msg;
	}
	
	/**
	 * @desc 执行后台服务
	 */
	public static function doServer ( $server_name )
	{
		$class = self::checkClass( $server_name, ACT_PATH );
		$obj = self::newClass( $class );
		$obj->name = $server_name;
		$obj->init();
	}
	
	/**
	 * @desc 生成唯一md5
	 */
	public static function uniqueMD5()
	{
		mt_srand( microtime( true ) * 10000 );
		$random = mt_rand();
		return md5( $random );
	}
	
	/**
	 * @desc 中断
	 */
	public static function __exit()
	{
		die;
	}
	
	/**
	 * @desc 加载类扩展
	 */
	public static function includeClass( $class_name, $class_file )
	{
		if ( !class_exists( $class_name, false ) )
		{
			include $class_file;
		}
		if ( !class_exists( $class_name, false ) )
		{
			return false;
		}
		return true;
	}
	
	/**
	 * @desc 加载model类
	 */
	public static function m( $modelName, $noExistsExit = true )
	{
		if(empty( $modelName ) )
		{
			return false;
		}
		if ( !isset( self::$models_arr[$modelName] ) )
		{
			$model_file = MODEL_PATH.$modelName.'.model.php';
			if ( !is_file( $model_file ) )
			{
				if ( $noExistsExit === true )
				{
					self::__die('错误，文件不存在');
				}
				else
				{
					return false;
				}
			}
			$model_full_name = $modelName . 'Model';
			$include_res = self::includeClass( $model_full_name, $model_file );
			if( $include_res === false )
			{
				if ( $noExistsExit === true )
				{
					self::__die('错误，类不存在');
				}
				else
				{
					return false;
				}
			}
			self::$models_arr[$modelName] = self::newClass( $model_full_name );
		}
		return self::$models_arr[$modelName];
	}
	
	/**
	 * @desc 初始化model数组
	 */
	public static function iniModels()
	{
		self::$models_arr = array();
	}
	
	/**
	 * @desc 首字母大写
	 * @since 20140709 gkl
	 */
	public static function firstUpper( $value )
	{
		if ( is_string( $value ) && trim( $value ) !== '' )
		{
			return strtoupper( $value[0] ) . substr( $value, 1 );
		}
		return '';
	}
	
	/**
	 * @desc 获取微妙差值
	 * @param integer $t 比较的微妙值
	 */
	public static function diff( $t = 0 )
	{
		if ( $t == 0 )
		{
			return microtime( true );
		}
		else
		{
			return microtime( true ) - $t;
		}
	}
	
	/**
	 * @desc 检查日期，并统一用“-”分隔符
	 * @param string $date 日期字符串
	 * @param string $delimiter 要替换的分隔符
	 */
	public static function checkDate ( &$date, $delimiter = '-' )
	{
		if ( $date === false )
		{
			return true;
		}
		if ( $delimiter !== '-' )
		{
			$date = str_replace( $delimiter, '-', $date );
		}
		$match = preg_match( '/[^0-9\-]/', $date );
		if ( $match === 1 )
		{
			return false;
		}
		$date_arr = explode( $delimiter, $date );
		if ( count( $date_arr ) != 3 )
		{
			return false;
		}
		$y	= intval( $date_arr[0] );
		$m	= intval( $date_arr[1] );
		$d	= intval( $date_arr[2] );
		if ( $m > 12 || $m < 1 || $d < 1 || $d > 31)
		{
			return false;
		}
		return true;
	}
	
	/**
	 * @desc获取ip
	 */
	public static function getIp()
	{
		$result = false;
		if ( isset( $_SERVER ) )
		{
			if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) )
			{
				$ips = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
				// 取X-FORWARDED-FOR中第一个非unknown的有效ip
				foreach ( $ips as $ip )
				{
					$ip = trim( $ip );
					if ( $ip !== 'unknown' )
					{
						$result = $ip;
						break;
					}
				}
			}
			else if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) )
			{
				$result = $_SERVER['HTTP_CLIENT_IP'];
			}
			else if ( isset( $_SERVER['REMOTE_ADDR'] ) )
			{
				$result = $_SERVER['REMOTE_ADDR'];
			}
		}
		else
		{
			$ip = getenv( 'HTTP_X_FORWARDED_FOR' );
			$ip = $ip === false ? getenv( 'HTTP_CLIENT_IP' ) : $ip;
			$ip = $ip === false ? getenv( 'REMOTE_ADDR' ) : $ip;
			$result = $ip;
		}
		$result = $result === false ? '0.0.0.0' : $result;
		return $result;
	}
	
	/**
	 * @desc 获取本身域名
	 * @since 20140724 gkl
	 */
	public static function getHost()
	{
		if ( self::isWeb() === true )
		{
			$res = parse_url( $_SERVER['HTTP_HOST'], PHP_URL_HOST );
			$res = is_null( $res ) ? $_SERVER['HTTP_HOST'] : $res;
			return $res;
		}
		return '';
	}
	
	/**
	 * @desc 判断是否web请求
	 * @since 20140724 gkl
	 */
	public static function isWeb()
	{
		if ( isset( $_SERVER['SERVER_PROTOCOL'] ) && strpos( $_SERVER['SERVER_PROTOCOL'], 'HTTP' ) !== false )
		{
			return true;
		}
		return false;
	}
	
	/**
	 * @desc 判断是否HTTPS
	 * @since 20140724 gkl
	 */
	public static function isHTTPS()
	{
		return isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on';
	}
	
	/**
	 * @desc 注册shutdown函数
	 * @param string|array $value 函数名称或包含对象、方法的数组
	 * @param array $params 参数
	 * @since 20140728 gkl
	 */
	public static function registerShutDown( $value, $params = array() )
	{
		if ( is_string( $value ) && function_exists( $value ) )
		{
			$new_params = array(
				$value,
			);
			$new_params = array_merge( $new_params, $params );
			call_user_func_array( 'register_shutdown_function', $new_params );
		}
		else if ( is_array( $value) && isset( $value[0] ) && isset( $value[1] ) )
		{
			if ( ( is_string( $value[0] ) && class_exists( $value[0] ) || is_object( $value[0] ) ) && is_string( $value[1] ) && method_exists( $value[0],  $value[1] ) )
			{
				$new_params = array(
					$value,
				);
				$new_params = array_merge( $new_params, $params );
				call_user_func_array( 'register_shutdown_function', $new_params );
			}
		}
	}
}
<?php
/**
 * @desc mfw 核心类
 * @author gkl
 * @since 20140709
 */
class mfwCore
{
	/**
	 * @desc 配置文件队列
	 * @var array
	 */
	private $config_arr;
	
	/**
	 * @desc 自动加载路径队列
	 * @var array
	 */
	private $autoload_dir_arr;
	
	/**
	 * @desc View模板文件路径
	 * @var array
	 */
	private $view_dir;
	
	/**
	 * @desc 数据库链接参数
	 * @var array
	 */
	private $conn_arr;
	
	/**
	 * @desc 会话对象
	 * @var mfwSession
	 */
	private $session;
	
	/**
	 * @desc 设置自动加载路径
	 * @param string/array $value 路径或数组
	 */
	protected function __setAutoloadDir( $value )
	{
		if ( is_string( $value ) )
		{
			$this->autoload_dir_arr[] = $value;
		}
		else if ( is_array( $value ) )
		{
			$this->autoload_dir_arr = array_merge( $this->autoload_dir_arr, $value );
		}
		else
		{
			throw new mfwException( mfwConst::ERR_FW_CORE_SET_PARAMS_ILLEAGEL );
		}
	}
	
	/**
	 * @desc 设置View模板文件路径
	 */
	protected function __setViewDir( $value )
	{
		if ( is_string( $value ) && trim( $value ) !== '' )
		{
			$this->view_dir = $value;
		}
		else
		{
			throw new mfwException( mfwConst::ERR_FW_CORE_SET_PARAMS_ILLEAGEL );
		}
	}
	
	/**
	 * @desc 设置配置文件
	 */
	protected function __setConfig( $value )
	{
		if ( is_string( $value ) )
		{
			$this->config_arr[] = $value;
		}
		else if ( is_array( $value ) )
		{
			$this->config_arr = array_merge( $this->config_arr, $value );
		}
		else
		{
			throw new mfwException( mfwConst::ERR_FW_CORE_SET_PARAMS_ILLEAGEL );
		}
	}
	
	/**
	 * @desc 设置会话
	 * @param mfwSession $session 会话对象
	 */
	protected function __setSession( $session )
	{
		$this->session = $session;
	}
	
	/**
	 * @desc 自动加载初始化
	 */
	protected function __initAutoload()
	{
		mfwAutoloader::init( $this->autoload_dir_arr );
	}
	
	/**
	 * @desc 配置初始化
	 */
	protected function __initConfig()
	{
		mfwConfig::init( $this->config_arr );
	}
	
	/**
	 * @desc 构造函数
	 * @since 20140709 gkl 创建
	 */
	public function __construct()
	{
		$this->config_arr = array();
		$this->autoload_dir_arr = array();
		$this->view_dir = '';
		$this->session = null;
	}
	
	/**
	 * @desc 析构函数
	 */
	public function __destruct()
	{
		if ( !is_null( $this->session ) )
		{
			unset( $this->session );
		}
	}
	
	/**
	 * @desc 设置属性
	 * @param string $name 属性名
	 * @param unknown $value 属性值
	 */
	public function set( $name, $value )
	{
		$set_add = '__set';
		$set_name = mfwCommon::firstUpper( $name );
		$method_name = $set_add . $set_name;
		if ( !method_exists( $this, $method_name ) )
		{
			throw new mfwException( mfwConst::ERR_FW_CORE_NO_METHOD );
		}
		return call_user_func_array( array( $this, $method_name ), array( $value ) );
	}
	
	/**
	 * @desc 初始化
	 * @param string $name 属性名
	 */
	public function init( $name )
	{
		$set_add = '__init';
		$set_name = mfwCommon::firstUpper( $name );
		$method_name = $set_add . $set_name;
		if ( !method_exists( $this, $method_name ) )
		{
			throw new mfwException( mfwConst::ERR_FW_CORE_NO_METHOD );
		}
		return call_user_func_array( array( $this, $method_name ), array() );
	}
	
	/**
	 * @desc 网页模式启动
	 * @since 20140709 gkl
	 */
	public function execWeb()
	{
		$view = new mfwView( $this->view_dir );
		$rd = new mfwRequestDispatcher();
		$request = $rd->getWebRequest();
		$request->setView( $view );		
		$request->setSession( $this->session );
		$request->session_start();
		$res = $request->send();
		unset( $view );
		unset( $request );
		unset( $rd );
	}
}
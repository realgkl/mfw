<?php
/**
 * @desc mfw 请求类
 */
class mfwReuqest
{
	/**
	 * @desc 请求调用的类
	 * @var string
	 */
	protected $class;
	
	/**
	 * @desc 请求调用的方法
	 */
	protected $method;
	
	/**
	 * @desc 获取控制器完整类名
	 * @param string $class_name 类名前缀
	 * @param string $class_type 类名类型:Ctrl控制器;Model模型;View模版
	 * @return string 类名
	 */
	protected function __getClass( $class_name, $class_type = 'Ctrl' )
	{
		$class_full_name	 = $class_name . $class_type;
		return $class_full_name;
	}
	
	/**
	 * @desc 生成控制器类对象
	 * @param string $classname 类名
	 * @return mfwCtrl 控制器基类
	 */
	protected function __newClass( $classname )
	{
		return new $classname( $this );
	}
	
	/**
	 * @desc 构造函数
	 */
	public function __construct( $class, $method )
	{
		$this->class = $class;
		$this->method = $method;
	}
	
	/**
	 * @desc 获取类名
	 */
	public function getClass()
	{
		return $this->class;
	}
	
	/**
	 * @desc 获取方法名
	 */
	public function getMethod()
	{
		return $this->method;
	}
}

/**
 * @desc mfw Web请求类
 * @author gkl
 * @since 20140709
 */
class mfwWebReuqest extends mfwReuqest
{
	/**
	 * @desc 模板对象
	 * @var mfwView
	 */
	protected $view;
	
	/**
	 * @desc 会话对象
	 * @var mfwSession
	 */
	protected $session;
	
	/**
	 * @desc 获取404页面
	 */
	protected function __render404()
	{
		$act = mfwGlobal::defined( '404_ACTION' ) ? mfwGlobal::getVar( '404_ACTION' ) : 'index';
		$method = mfwGlobal::defined( '404_FUNC' ) ? mfwGlobal::getVar( '404_FUNC' ) : 'html404';
		return $this->render( $act, $method );
	}
	
	/**
	 * @desc 输出404错误
	 */
	protected function __html404()
	{
		$res = $this->__render404();
		if ( $res === false )
		{
			mfwWebCtrl::contentType();
			mfwWebCtrl::code(404);
			mfwCommon::__echo( mfwConst::NOTICE_HTML404 );
		}
		mfwCommon::__exit();
	}
	
	/**
	 * @desc 生成控制器类对象
	 * @param string $classname 类名
	 * @return mfwCtrl|false 控制器基类
	 */
	protected function __newClass( $classname )
	{
		try
		{
			$obj = parent::__newClass( $classname ); 
		}
		catch ( mfwException $e )
		{
			$obj = false;
		}
		return $obj;
	}
	
	/**
	 * @desc 发送请求
	 */
	protected function __send( $class, $method )
	{
		$class			= $this->__getClass( $class, 'Ctrl' );
		$obj			= $this->__newClass( $class );
		$method	.= mfwConst::WEB_REQUEST_METHOD_EXT;
		if ( $obj === false )
		{
			return false;
		}
		if ( method_exists( $obj, 'beforeAction' ) )
		{
			call_user_func_array( array( $obj, 'beforeAction' ), array() );
		}
		if ( !method_exists( $obj, $method ) )
		{
			return false;
		}
		call_user_func_array( array( $obj, $method ), array() );
		if ( method_exists( $obj, 'afterAction' ) )
		{
			call_user_func_array( array( $obj, 'afterAction' ), array() );
		}
		unset( $obj );
		return true;
	}
	
	/**
	 * @desc 构造函数
	 */
	public function __construct( $class, $method )
	{
		parent::__construct( $class, $method );
		$this->view = null;
	}
	
	/**
	 * @desc 开启会话
	 * @since 20140724 gkl
	 */
	public function session_start()
	{
		if ( !is_null( $this->session ) )
		{
			$this->session->start();
		}
	}
	
	/**
	 * @desc 会话复位
	 * @since 20140724 gkl
	 */
	public function session_reset()
	{
		if ( !is_null( $this->session ) )
		{
			$this->session->reset();
		}
	}
	
	/**
	 * @desc 发送请求
	 * @return boolean
	 */
	public function send()
	{
		ob_start();
		$res = $this->__send( $this->class, $this->method );
		$ctrl_content = ob_get_clean();
		if ( $this->view->getView() === '' )
		{
			$view_name = $this->getClass() . '/' . $this->getMethod();
			$this->view->setView( $view_name );
			unset( $view_name );
		}
		if ( $res === true )
		{
			$this->view->setContent( $ctrl_content );
			$this->view->display();
		}
		else
		{
			$this->html404();
		}
		return $res;
	}
	
	/**
	 * @desc 内部跳转
	 * @return boolean
	 */
	public function render( $class = 'index', $method = 'index' )
	{
		$this->class = $class;
		$this->method = $method;
		return $this->send();
	}
	
	/**
	 * @desc 外部跳转(url跳转)
	 */
	public function jump( $url )
	{
		header( 'location: ' . $url  );
		mfwCommon::__exit();
	}
	
	/**
	 * @desc 输出404错误
	 * @return void
	 */
	public function html404()
	{
		$this->__html404();
	}
	
	/**
	 * @desc 设置View对象
	 * @param mfwView $view
	 */
	public function setView( $view )
	{
		if ( get_class( $view  ) === 'mfwView' )
		{
			$this->view = $view;
		}
		else
		{
			throw new mfwException( mfwConst::ERR_FW_REQUEST_SETVIEW_PARAMS_ILLEAGEL );
		}
	}
	
	/**
	 * @desc 设置会话对象
	 * @param mfwSession $session
	 */
	public function setSession( $session )
	{
		$this->session = $session;
	}
	
	/**
	 * @desc 获取view对象
	 * @return mfwView|null
	 */
	public function getView()
	{
		return $this->view;
	}
	
	/**
	 * @desc 获取会话对象
	 * @return mfwSession
	 * @since 20140728
	 */
	public function getSession()
	{
		return $this->session;
	}
	
	/**
	 * @desc 是否为post请求
	 * @return boolean
	 * @since 20140730 gkl
	 */
	public function isPost()
	{
		$request_method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( $_SERVER['REQUEST_METHOD'] ) : false;
		return $request_method === mfwConst::REQUEST_METHOD_POST;
	}
	
	/**
	 * @desc 判断是否https
	 * @return boolean
	 * @since 20140806 gkl
	 */
	public function isHttps()
	{
		return mfwCommon::isHTTPS();
	}
	
	/**
	 * @desc 判断是否ajax
	 * @return boolean
	 * @since 20140806 gkl
	 */
	public function isAjax()
	{
		return mfwCommon::isAjax();
	}
}

/**
 * @desc mfw 请求调度类
 * @author gkl
 * @since 20140709
 */
class mfwRequestDispatcher
{
	/**
	 * @desc 请求对象
	 * @var mfwReuqest
	 */
	protected $request;
	
	/**
	 * @desc 请求对象类别
	 * @var string
	 */
	protected $type;
	
	/**
	 * @desc 构造函数
	 * @param mfwView $view 模板对象
	 * @since 20140709 gkl
	 */
	public function __construct( $type = mfwConst::REQUEST_TYPE_WEB )
	{
		$this->request = null;
		$this->view = null;
		$this->type = $type;
		switch ( $this->type )
		{
			case mfwConst::REQUEST_TYPE_WEB :
				$class					= isset( $_GET['act'] ) ? $_GET['act'] : ( mfwGlobal::defined( 'DEFAULT_ACTION' ) ? mfwGlobal::getVar( 'DEFAULT_ACTION' ) : 'index' );
				$method				= isset( $_GET['st'] ) ? $_GET['st'] : ( mfwGlobal::defined( 'DEFAULT_FUNC' ) ? mfwGlobal::getVar( 'DEFAULT_FUNC' ) : 'index' );
				$this->request	= new mfwWebReuqest( $class, $method );
				break;
			default :
				throw new mfwException(  mfwConst::ERR_FW_REQUEST_DISPATCHER_TYPE_UNKNOWN );
				break;
		}
	}
	
	/**
	 * @desc 获取网页请求对象
	 * @return mfwWebReuqest
	 */
	public function getWebRequest()
	{
		switch ( $this->type )
		{
			case mfwConst::REQUEST_TYPE_WEB :
				return $this->request;
				break;
			default :
				throw new mfwException(  mfwConst::ERR_FW_REQUEST_DISPATCHER_TYPE_UNKNOWN );
				break;
		}
	}
}
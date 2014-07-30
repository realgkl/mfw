<?php
/**
 * @desc view模板类
 * @author gkl
 * @since 20131122
 */
class mfwView
{
	
	/**
	 * @desc 布局后缀
	 */
	protected static $ext;
	
	/**
	 * @desc 布局文件
	 */
	protected $layout;
	
	/**
	 * @desc 是否显示模板
	 * @var boolean
	 */
	protected $show_view;
	
	/**
	 * @desc 是否显示布局
	 * @var boolean
	 */
	protected $show_layout;
	
	/**
	 * @desc html标题
	 */
	protected static $title = '';
	
	/**
	 * @desc meta描述
	 */
	protected static $description = '';
	
	/**
	 * @desc meta关键字
	 */
	protected static $keywords = '';
	
	/**
	 * @desc css数组
	 * @var array
	 */
	protected static $css_arr = array();
	
	/**
	 * @desc js数组
	 * @var array
	 */
	protected static $js_arr = array();
	
	/**
	 * @desc content内容
	 * @var string
	 */
	protected static $content = '';
	
	/**
	 * @desc 模板路径
	 * @var string
	 */
	protected static $view_dir;
	
	/**
	 * @desc 模板文件名
	 * @var string
	 */
	protected $view;
	
	/**
	 * @desc view参数
	 * @var array
	 */
	protected $view_vars = array();
	
	/**
	 * @desc 从view文件中获取内容
	 */
	protected static function __loadFile( $file_name, $params )
	{
		// 声明变量
		if ( !empty( $params ) )
		{
			extract( $params );
		}
		// 获取view
		if ( file_exists( $file_name ) )
		{
			ob_start();
			ob_implicit_flush( false );
			include $file_name;
			return ob_get_clean();
		}
		return false;
	}
	
	/**
	 * @desc 获取显示层
	 */
	protected function __loadView( $view, $params )
	{
		$view = self::$view_dir . $view . self::$ext;
		$context = self::__loadFile( $view, $params );
		if ( $context === false )
		{
			throw new mfwException( mfwConst::ERR_FW_VIEW_FILE_UNFOUND . "，文件名： {$view}" );
		}
		return $context;
	}
	
	/**
	 * @desc 获取布局
	 */
	protected function __loadLayout( $params )
	{
		$view = self::$view_dir . $this->layout . self::$ext;
		$context = self::__loadFile( $view, $params );
		if ( $context === false )
		{
			throw new mfwException( mfwConst::ERR_FW_VIEW_FILE_UNFOUND . "，文件名： {$view}" );
		}
		return $context;
	}
	
	/**
	 * @desc 显示内容
	 */
	protected function __show( $context )
	{
		mfwCommon::__echo ( $context );
	}
	
	/**
	 * @desc 获取css版本号
	 */
	protected static function __getCssVersion()
	{
		$version = mfwGlobal::getVar( 'cssVersion' );
		return $version === false? time() : $version;
	}
	
	/**
	 * @desc 获取js版本号
	 */
	protected static function __getJsVersion()
	{
		$version = mfwGlobal::getVar( 'jsVersion' );
		return $version === false? time() : $version;
	}
	
	/**
	 * @desc 获取img版本号
	 */
	protected static function __getImgVersion()
	{
		$version = mfwGlobal::getVar( 'imgVersion' );
		return $version === false? time() : $version;
	}
	
	/**
	 * @desc 获取自动分配CDN
	 */
	protected static function __getCdn()
	{
		$cdn = mfwGlobal::getVar( 'cdn' );
		return $cdn === false ? '/' : $cdn;
	}
	
	/**
	 * @desc 构造函数
	 * @since 20140709 gkl
	 */
	public function __construct( $view_dir = '', $layout = false, $showLayout = true )
	{
		self::$view_dir		= mfwGlobal::defined( 'VIEW_DIR' ) ? mfwGlobal::getVar('VIEW_DIR') : '';
		self::$ext				= mfwGlobal::defined( 'VIEW_EXT' )  ? mfwGlobal::getVar('VIEW_EXT') : mfwConst::VIEW_EXT;
		$this->layout		= mfwGlobal::defined( 'VIEW_LAYOUT' ) ? mfwGlobal::getVar('VIEW_LAYOUT') : 'public/layout';
		if ( $layout !== false )
		{
			$this->layout = $layout;
		}
		if ( is_string( $view_dir ) && trim( $view_dir ) !== '' )
		{
			self::$view_dir = $view_dir;
		}
		if ( !is_string( self::$view_dir ) || trim( self::$view_dir ) === '' )
		{
			throw new mfwException( mfwConst::ERR_FW_VIEW_DIR_ILLEAGEL );
		}
		$this->show_layout = $showLayout;
		$this->css_version = 1;
		$this->js_version = 1;
		$this->img_version = 1;
		$this->show_view = true;
		$this->view = '';
		$this->view_vars = array();
	}
	
	/**
	 * @desc 显示view层内容
	 */
	public function display()
	{
		if ( $this->show_view === true )
		{
			if ( $this->show_layout === true )
			{
				self::$content .= $this->__loadView( $this->view, $this->view_vars );
				$content = $this->__loadLayout( $this->view_vars );
			}
			else
			{
				$content = $this->__loadView( $this->view, $this->view_vars );
			}
			$this->__show( $content );
		}
		else
		{
			$this->__show( self::$content );
		}
	}
	
	/**
	 * @desc 读取View层部分
	 */
	public function loadPart( $view, $params = array() )
	{
		$file_name = self::$view_dir . $view . self::$ext;
		$content = $this->__loadFile( $file_name, $params );
		if ( $content !== false )
		{
			$this->__show( $content );
		}
		else
		{
			throw new mfwException( mfwConst::ERR_FW_VIEW_FILE_UNFOUND );
		}
	}
	
	/**
	 * @desc 获取是否显示模板
	 */
	public function getShowView()
	{
		return $this->show_view;
	}
	
	/**
	 * @desc 不显示模板
	 */
	public function noView()
	{
		$this->show_view = false;
	}
	
	/**
	 * @desc 获取模版文件
	 * @return string
	 * @since 20140710 gkl
	 */
	public function getView()
	{
		return $this->view;
	}
	
	/**
	 * @desc 设置模板文件
	 * @param string $view
	 */
	public function setView( $view )
	{
		$this->view = $view;
	}
	
	/**
	 * @desc 设置css调用
	 * @param string|array $value css调用link
	 */
	public function setCss( $value )
	{
		if ( is_string( $value ) && trim( $value ) !== '' )
		{
			self::$css_arr[$value] = 1;
		}
		else if ( is_array( $value ) && !empty( $value ) && trim( $value[0] ) !== '' )
		{
			foreach ( $value as $v )
			{
				self::$css_arr[$v] = 1;
			}
		}
		else
		{
			throw new mfwException( mfwConst::ERR_FW_CORE_SET_PARAMS_ILLEAGEL );
		}
	}
	
	/**
	 * @desc 设置js调用
	 * @param string|array $value js调用link
	 */
	public function setJs( $value )
	{
		if ( is_string( $value ) && trim( $value ) !== '' )
		{
			self::$js_arr[$value] = 1;
		}
		else if ( is_array( $value ) && !empty( $value ) && trim( $value[0] ) !== '' )
		{
			foreach ( $value as $v )
			{
				self::$js_arr[$v] = 1;
			}
		}
		else
		{
			throw new mfwException( mfwConst::ERR_FW_CORE_SET_PARAMS_ILLEAGEL );
		}
	}
	
	/**
	 * @desc 设置变量值
	 * @param string $name 变量名
	 * @param unknow $value 值
	 */
	public function assign( $name, $value )
	{
		$this->view_vars[$name] = $value;
	}
	
	/**
	 * @desc 获取并输出css版本号
	 */
	public static function css()
	{
		if ( !empty( self::$css_arr ) )
		{
			foreach ( self::$css_arr as $css => $v )
			{
				mfwCommon::__echo( "\t<link href=\"" . self::__getCdn() . "css/{$css}?v=" . self::__getCssVersion() . "\" rel=\"stylesheet\">\n" );
			}  
		}
	}
	
	/**
	 * @desc 获取并输出js版本号
	 */
	public static function js()
	{
		if ( !empty( self::$js_arr ) )
		{
			foreach ( self::$js_arr as $js => $v )
			{
				mfwCommon::__echo( "\t<script type=\"text/javascript\" src=\"" . self::__getCdn() . "js/{$js}?v=" . self::__getJsVersion() . "\"></script>\n" );
			}  
		}
	}
	
	/**
	 * @desc 输出View层$title
	 */
	public static function title()
	{
		$title = self::$title === '' ? mfwGlobal::getVar( 'title' ) : self::$title;
		$title = $title === '' ? false : $title;
		if ( $title !== false )
		{
			mfwCommon::__echo( "\t" . mfwHtml::title( $title ) );
		}
	}
	
	/**
	 * @desc 输出View层$description
	 */
	public static function description()
	{
		$description = self::$description === '' ? mfwGlobal::getVar( 'description' ) : self::$description;
		$description = $description === '' ? false : $description;
		if ( $description !== false )
		{
			mfwCommon::__echo( "\t" . mfwHtml::meta( 'description', $description ) );
		}
	}
	
	/**
	 * @desc 输出View层$keywords
	 */
	public static function keywords()
	{
		$keywords = self::$keywords === '' ? mfwGlobal::getVar( 'keywords' ) : self::$keywords;
		$keywords = $keywords === '' ? false : $keywords;
		if ( $keywords !== false )
		{
			mfwCommon::__echo( "\t" . mfwHtml::meta( 'keywords', $keywords ) );
		}
	}
	
	/**
	 * @desc 设置布局$title
	 */
	public static function setTitle( $value )
	{
		if ( is_string( $value ) && trim( $value ) !== '' )
		{
			self::$title = $value;
		}
	}
	
	/**
	 * @desc 设置布局$description
	 */
	public static function setDescription( $value )
	{
		if ( is_string( $value ) && trim( $value ) !== '' )
		{
			self::$description = $value;
		}
	}
	
	/**
	 * @desc 设置布局$keywords
	 */
	public static function setKeywords( $value )
	{
		if ( is_string( $value ) && trim( $value ) !== '' )
		{
			self::$keywords = $value;
		}
	}
	
	/**
	 * @desc 设置内容
	 */
	public static function setContent( $value )
	{
		if ( is_string( $value ) )
		{
			self::$content .= $value;
		}
		else
		{
			throw new mfwException( mfwConst::ERR_FW_CORE_SET_PARAMS_ILLEAGEL );
		}
	}
	
	/**
	 * @desc 显示内容
	 */
	public static function content()
	{
		if ( self::$content !== '' )
		{
			mfwCommon::__echo( self::$content );
		}
	}
	
	/**
	 * @desc 输出cdn
	 */
	public static function cdn()
	{
		mfwCommon::__echo( self::__getCdn() );
	}
	
	/**
	 * @desc 输出图片
	 */
	public static function img( $id, $url )
	{
		$url = self::__getCdn() . 'img/' . $url . '?v=' . self::__getImgVersion();
		mfwCommon::__echo( mfwHtml::img( $id, $url ) );
	}
	
	/**
	 * @desc 载入部分模版
	 */
	public static function includePart( $view, $params = array() )
	{
		$view = self::$view_dir . $view . self::$ext;
		$context = self::__loadFile( $view, $params );
		if ( $context === false )
		{
			throw new mfwException( mfwConst::ERR_FW_VIEW_FILE_UNFOUND . "，文件名： {$view}" );
		}
		mfwCommon::__echo( $context );
	}
}
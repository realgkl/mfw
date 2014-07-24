<?php
/**
 * @desc 控制器基类
 * @author gkl
 * @since 20140709
 */
class mfwCtrl
{
	/**
	 * @desc 请求实例
	 * @var mfwReuqest
	 */
	protected $request;
	
	/**
	 * @desc 构造函数
	 * @param mfwReuqest $request 请求实例
	 */
	public function __construct( $request )
	{
		$this->request = $request;
	}
	
	/**
	 * @desc 控制器after函数
	 */
	public function afterAction ()
	{
	}
	
	/**
	 * @desc 控制器before函数
	 */
	public function beforeAction()
	{
	}
}

/**
 * @desc web控制器
 */
class mfwWebCtrl extends mfwCtrl
{
	/**
	 * @desc view对象
	 * @var mfwView
	 */
	protected $view;
	
	/**
	 * @desc 请求实例
	 * @var mfwWebReuqest
	 */
	protected $request;
	
	public function beforeAction()
	{
		parent::beforeAction();
		$this->view = $this->request->getView();
	}
	
	public static function contentType( $content_type = 'text/html', $charset = 'utf-8' )
	{
		header( "Content-type: {$content_type}; charset={$charset};" );
	}
	
	public static function code( $code )
	{
		switch ( $code )
		{
			case 404:
				header( "HTTP/1.1 {$code}" );
				break;
			default:
				break;
		}
	}
}
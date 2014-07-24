<?php
/**
 * @desc autoloader 类
 * @author  gkl
 * @since 20131211
 */
class mfwAutoloader
{	
	/**
	 * @desc 路径数组
	 * @var array
	 */
	private $dir_arr;
	
	/**
	 * @desc 对象实例
	 */
	public static $loader;
	
    /**
     * @desc 构造函数
     * @param array $dir_arr 路径数组
     */
    public function __construct( $dir_arr )
    {
    	if ( !is_array( $dir_arr ) || empty( $dir_arr ) || !is_string( trim( $dir_arr[0] ) ) || trim( $dir_arr[0] ) === '' )
    	{
    		throw new mfwException( mfwConst::ERR_FW_AL_DIR_ILLEAGEL );
    	}
    	$this->dir_arr = $dir_arr;
        spl_autoload_register(array(
            $this,
            'import'
        ));
    }
    
    /**
     * @desc 初始化
     * @param array $dir_arr 路径数组
     * @return autoloaderBase
     */
    public static function init( $dir_arr ) {
        // 静态化自调用
        if ( is_null( self::$loader ) )
        {
            self::$loader = new self( $dir_arr );
        }
        return self::$loader;
    }
    
    /**
     * 固定路径的class 类文件 以.class.php 结尾
     */
    protected function import( $className )
    {
    	$error = true;
    	foreach ( $this->dir_arr as $dir )
    	{
	    	$file_name =  $dir . $className . mfwConst::AUTOLOADER_CLASSEXT;
	    	if ( file_exists( $file_name ) )
			{
				$error = false;
				break;
			}
    	}
		if ( $error === true )
		{
			$this->err_fn( $className, $file_name );
		}
		else
		{
			include $file_name;	
		}
    }
    /**
     * @desc 文件出错提示
     */
    protected function err_fn( $className, $full_name )
    {
    	throw new mfwException( "类 {$className} 文件加载失败: 路径 {$full_name}" );
    }
}

<?php
/**
 * @desc mfw 配置类
 * @author gkl
 * @since 20140710
 */
class mfwConfig
{
	/**
	 * @desc 路径数组
	 * @var array
	 */
	protected $dir_arr;
	
	/**
	 * @desc 对象实例
	 */
	public static $loader;
	
	/**
	 * @desc 构造函数
	 */
	public function __construct( $dir_arr )
	{
		if ( !is_array( $dir_arr ) || empty( $dir_arr ) || !is_string( trim( $dir_arr[0] ) ) || trim( $dir_arr[0] ) === '' )
    	{
    		throw new mfwException( mfwConst::ERR_FW_CONFIG_DIR_ILLEAGEL );
    	}
    	$this->dir_arr = $dir_arr;
        foreach ( $this->dir_arr as $dir )
        {
        	include_once $dir;
        }
	}
	
	/**
     * @desc 初始化
     * @param array $dir_arr 路径数组
     * @return mfwConfig
     */
    public static function init( $dir_arr )
    {
        // 静态化自调用
        if ( is_null( self::$loader ) )
        {
            self::$loader = new self( $dir_arr );
        }
        return self::$loader;
    }
}
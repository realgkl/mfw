<?php
/**
 * @desc memcache mfw框架 插件类
 * @author gkl
 * @since 20140724
 */
class mfwMemcache
{
	/**
	 * @desc memcached 对象
	 * @var Memcache
	 */
	protected static $mem;
	
	/**
	 * @desc 服务器配置
	 * @var array
	 */
	protected static $servers = array();
	
	/**
	 * @desc 设置服务器配置
	 */
	public static function addServer( $host, $port )
	{
		self::$servers[] = array(
			'host'	=> $host,
			'port'	=> $port,
		);
	}
	
	/**
	 * @desc 初始化对象
	 * @return Memcache
	 */
	public static function init()
	{
		if ( !is_null( self::$mem ) )
		{
			unset( self::$mem );
		}
		self::$mem = new Memcache();
		if ( !empty( self::$servers ) )
		{
			foreach ( self::$servers as $server )
			self::$mem->addserver( $server['host'], $server['port'] );
		}
		return self::$mem;
	}

}
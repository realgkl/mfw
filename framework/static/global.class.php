<?php
/**
 * @desc mfw 全局类
 */
class mfwGlobal
{
	/**
	 * @desc 全局变量数组
	 * @var array
	 */
	protected static $variable_arr;
	
	/**
	 * @desc 设置全局变量
	 * @param string $name
	 * @param unknown $value
	 */
	public static function setVar( $name, $value )
	{
		self::$variable_arr[$name] = $value;
	}
	
	/**
	 * @desc 获取全局变量
	 * @param string $name
	 * @return unknown|boolean
	 */
	public static function getVar( $name )
	{
		if ( isset( self::$variable_arr[$name] ) )
		{
			return self::$variable_arr[$name];
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @desc 是否定义过全局变量
	 * @param string $name
	 * @return boolean
	 */
	public static function defined( $name )
	{
		return isset( self::$variable_arr[$name] );
	}
}
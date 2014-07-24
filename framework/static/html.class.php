<?php
/**
 * @desc mfw HTML标签助手类
 */
class mfwHtml
{
	/**
	 * @desc meta标签
	 * @param string $name
	 * @param string $value
	 * @return string
	 */
	public static function meta( $name, $value )
	{
		return "<meta name=\"{$name}\" content=\"{$value}\" />";
	}
	
	/**
	 * @desc title标签
	 * @param string $value
	 * @return string
	 */
	public static function title( $value )
	{
		return "<title>{$value}</title>";
	}
	
	/**
	 * @desc img标签
	 * @parma string $id
	 * @param string $url
	 */
	public static function img( $id, $url )
	{
		return "<img id=\"{$id}\" src=\"{$url}\" />";
	}
}
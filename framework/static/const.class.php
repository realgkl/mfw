<?php
/**
 * @desc 框架常量类
 * @author gkl
 * @since 20140709
 */
class mfwConst
{
	/**
	 * @desc 错误常量
	 */
	const ERR_FW_CORE_NO_METHOD = '框架核心类的方法不存在';
	/**
	 * @desc 错误常量
	 */
	const ERR_FW_CORE_SET_PARAMS_ILLEAGEL = '框架核心类设置函数参数非法';
	/**
	 * @desc 错误常量
	 */
	const ERR_FW_AL_DIR_ILLEAGEL = '框架自动加载类路径非法';
	/**
	 * @desc 错误常量
	 */
	const ERR_FW_CONFIG_DIR_ILLEAGEL = '框架系统配置类路径非法';
	/**
	 * @desc 错误常量
	 */
	const ERR_FW_REQUEST_DISPATCHER_TYPE_UNKNOWN = '框架请求调度类的类型不存在';
	/**
	 * @desc 错误常量
	 */
	const ERR_FW_VIEW_FILE_UNFOUND = 'View模板文件找不到';
	/**
	 * @desc 错误常量
	 */
	const ERR_FW_VIEW_DIR_ILLEAGEL = 'View模板路径非法';
	/**
	 * @desc 错误常量
	 */
	const ERR_FW_REQUEST_SETVIEW_PARAMS_ILLEAGEL = '框架请求类设置模板对象参数非法';
	/**
	 * @desc 错误常量
	 */
	const ERR_FW_CONN_PARAMS_ILLEAGEL = '框架数据库链接配置参数非法';
	/**
	 * @desc 请求方法常量
	 */
	const REQUEST_METHOD_POST = 'POST';
	/**
	 * @desc 请求方法常量
	 */
	const REQUEST_METHOD_GET = 'GET';
	/**
	 * @desc 请求类别
	 */
	const REQUEST_TYPE_WEB = 'web';
	/**
	 * @desc 请求类别
	 */
	const REQUEST_TYPE_SERVER = 'server';
	/**
	 * @desc 自动加载文件扩展名
	 */
	const AUTOLOADER_CLASSEXT = '.class.php';
	/**
	 * @desc web请求的方法名后缀
	 */
	const WEB_REQUEST_METHOD_EXT = 'Action';
	/**
	 * @desc 404提示 
	 */
	const NOTICE_HTML404 = '你所浏览的页面不存在';
	/**
	 * @desc 模板文件后缀
	 */
	const VIEW_EXT = '.view.php';
	/**
	 * @desc oracle数据库
	 */
	const DB_OCI = 'oci';
	/**
	 * @desc mysql数据库
	 */
	const DB_MYSQL = 'mysql';
	/**
	 * @desc sql占有符
	 */
	const DB_OCC = ':FIELD_';
}
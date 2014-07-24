<?php
try
{
	// 设置时区
	date_default_timezone_set( 'PRC' );
	// 引用框架
	include __DIR__ . '/../framework/init.php';
	// 创建核心
	$core = new mfwCore();
	// 启动自动加载
	$core->set( 'autoloadDir', array(
		__DIR__ . '/../app/ctrls/',
		__DIR__ . '/../app/models/',
	) );
	$core->init( 'autoload' );
	
	$core->set( 'viewDir', __DIR__ . '/../app/views/' );
	
	$core->set( 'config', array(
		__DIR__ . '/../app/config/inc.php',
		__DIR__ . '/../app/config/db.php',
	) );
	$core->init( 'config' );
	
	// 启动web
	$core->execWeb();
	unset( $core );
}
catch ( mfwExcetion $e )
{
	echo $e->getMessage();
}

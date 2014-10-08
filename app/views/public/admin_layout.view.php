<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php mfwView::title(); ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php mfwView::description(); ?>
<?php mfwView::keywords(); ?>
<?php mfwView::css(); ?>
<?php mfwView::js(); ?>
</head>
<body>
<?php mfwView::includePart( 'public/header' ); ?>
<div id="container">
	<div id="left">
<?php mfwView::includePart( 'public/left_nav', array( 'left_nav_cur' => isset( $left_nav_cur ) ? $left_nav_cur : ''  ) ); ?>
	</div>
	<div id="center">
		<div id="context-container">
			<div class="wrapper">
<?php mfwView::includePart( 'public/nav_bar', array( 'nav_bar_title' => isset( $nav_bar_title ) ? $nav_bar_title : ''  ) ); ?>
<?php mfwView::content(); ?>
			</div>
		</div>
	</div>
	<div id="clear"></div>
</div>	
</body>
</html>
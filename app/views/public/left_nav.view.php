<nav class="left">
	<ul>
		<li id="/admin/user/list">
			<a href="/admin/user/list" title="用户列表">用户列表</a>
		</li>
		<li class="hr"></li>
		<li id="/admin/shop/list">
			<a href="/admin/shop/list" title="商户列表">商户列表</a>
		</li>
		<li id="/admin/goods/list">
			<a href="/admin/goods/list" title="商品列表">商品列表</a>
		</li>
		<div id="clear"></div>
	</ul>
</nav>
<script>
<!--
var cur_menu = String('<?php echo $left_nav_cur; ?>');
if ( cur_menu !== '' )
{
	var id = String('#' + cur_menu);
	var id = id.replace(/\//g, '\\/');
	var menu_li = $('nav.left').find(id);
	if (typeof(menu_li.get(0)) !== 'undefined')
	{
		if (!menu_li.hasClass('current'))
		{
			menu_li.addClass('current');
		}
	}
}
//-->
</script>
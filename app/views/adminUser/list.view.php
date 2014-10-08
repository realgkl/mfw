<div id="grid" class="ui-widget-content"></div>
<script>
<!--
$('#grid').mygrid({
	url: '/admin/user/listDataAjax',
	columns: {
		'用户id': {
			field: 'f_id',
			width: '100px'
		},
		'登录id': {
			field: 'f_login_id',
			width: '100px'
		},
		'登录名': {
			field: 'f_name',
			width: '100px'
		},
		'解冻时间': {
			field: 'f_unfrozen_time',
			width: '120px'
		},
		'是否禁用': {
			field: 'f_disable_flag',
			width: '100px'
		}
	},
	onFilterRowData: function( index, data )
	{
		data.f_disable_flag = '否';
		if ( data.f_disable_flag === 1 )
		{
			data.f_disable_flag = '是';
		}
		return data;
	},
	limit: <?php echo $limit ?>,
	page: <?php echo $page; ?>,
	buttons: {
		'添加用户': {			
			icon: 'ui-icon-plusthick',
			click: function()
			{
				alert('添加用户');
			}
		},
		'编辑用户': {
			icon: 'ui-icon-pencil',
			click: function()
			{
				alert( '编辑用户' );
			}
		},
		'禁用用户': {
			icon: 'ui-icon-cancel',
			click: function()
			{
				alert( '禁用用户' );
			}
		}
	}
});
//-->
</script>
/**
 * @desc jquery 自定义表格
 */
$.fn.extend({
	mygrid: function()
	{		
		var __self = this;
		var args = arguments;
		this.options = {
			buttons: {},
		};
		this.divs = {
			container: null, // 外壳
			toolbar: null, // 按钮工具栏
			header: null, // 标题头
			footer: null, // 页脚
			body: null // 数据栏
		};
		this.init = function()
		{
			__self.divs.container = __self;
			__self.divs.toolbar = document.createElement( 'div' );
			$( __self.divs.toolbar ).addClass( 'ui-widget-header toolbar' );
			for ( var i = 0; i < __self.options.buttons.length; i++ )
			{
				var button = __self.options.buttons[i];
				var button_name = '';
				if ( typeof( button['name'] ) !== 'undefined' )
				{
					 button_name = button['name'];
				}
				var button_icon = null;
				if ( typeof( button['icon'] ) !== 'undefined' )
				{
					button_icon = button['icon'];
				}
				var button_click = null;
				if ( typeof( button['click'] ) !== 'undefined' )
				{
					button_click = button['click'];
				}
			}
			
		};
		this.setOption = function( key, value )
		{
			if ( typeof( __self.options[key] ) !== 'undefined' )
			{
				__self.options[key] = value;
			}
		};
		this.show = function()
		{
			
		};

		if ( args.length > 1 )
		{
			var key = args[0];
			var value = args[1];
			this.setOption( key, value );
		}
		else
		{
			if ( args.length > 0 )
			{
				var arg = args[0];
				if ( typeof( arg ) === 'String' )
				{
					this.arg();
				}
				else
				{
					for ( option in arg )
					{
						if ( typeof( this.options[option] ) !== 'undefined' )
						{
							this.options[option] = arg[option];
						}
					}
				}
			}
			this.init();
		}
	}
});
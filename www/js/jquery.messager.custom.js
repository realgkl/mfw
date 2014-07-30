/**
 * @desc jquery 扩展函数
 * @author gkl
 * @since 20140730
 */
$.extend({
	/**
	 * @desc 弹出框类
	 * @since 20140730
	 */
	messager: 
	{
		__widget_id: 'messager-ui-widget',
		__widget: null,
		__callback: null,
		__callback_2: null,
		__dialog: null,
		alert: function()
		{
			var args = arguments;
			var msg = $.common.__getArg(args, 0);
			var callback = $.common.__getArg(args, 1);
			if ($.common.__isNull(msg))
			{
				msg = '';
			}			
			if (!$.common.__isNull(callback) && typeof(callback) === 'function')
			{				
				this.__callback = callback;
			}
			this.__iniWidget(1, '警告', msg );
			this.__showDialog();
		},
		prompt: function()
		{
			var args = arguments;
			var msg = $.common.__getArg(args, 0);
			var callback = $.common.__getArg(args, 1);
			if ($.common.__isNull(msg))
			{
				msg = '';
			}			
			if (!$.common.__isNull(callback) && typeof(callback) === 'function')
			{				
				this.__callback = callback;
			}
			this.__iniWidget(2, '提示', msg );
			this.__showDialog();
		},
		confirm: function()
		{
			var args = arguments;
			var msg = $.common.__getArg(args, 0);
			var ok_callback = $.common.__getArg(args, 1);
			var cancel_callback = $.common.__getArg(args, 2);
			if ($.common.__isNull(msg))
			{
				msg = '';
			}			
			if (!$.common.__isNull(ok_callback) && typeof(ok_callback) === 'function')
			{				
				this.__callback = ok_callback;
			}
			if (!$.common.__isNull(cancel_callback) && typeof(cancel_callback) === 'function')
			{				
				this.__callback_2 = cancel_callback;
			}
			this.__iniWidget(3, '询问', msg );
			this.__showDialog();
		},
		
		/**
		 * @desc 初始化容器
		 * @param integer type 1:alert;2:propt;3:confirm
		 * @param string title 标题
		 * @param string msg 信息
		 * @return HtmlElement
		 */
		__iniWidget: function( type, title, msg )
		{
			if ( $.common.__isNull ( this.__widget ) === false )
			{
				this.__freeWidget();
			}
			if (typeof(document.body) === 'undefined')
			{
				return;
			}
			var div = document.createElement('div');			
			$(div).addClass('ui-widget');
			$(div).attr('id', this.__widget_id);
			$(div).attr('title', title);
			var html = '';
			switch (type)
			{
				case 1:
					html = '<div class="ui-dialog-content ui-widget-content" style="width: auto; min-height: 50px; max-height: none; height: auto;">' +					
						'<p><span class="ui-icon ui-icon-alert" style="float: left; margin:0 7px 50px 0;"></span>';
					html += msg + '</p></div>';
					break;
				case 2:
					html = '<div class="ui-dialog-content ui-widget-content" style="width: auto; min-height: 50px; max-height: none; height: auto;">' +					
					'<p><span class="ui-icon ui-icon-info" style="float: left; margin:0 7px 50px 0;"></span>';
					html += msg + '</p></div>';
					break;
				case 3:
					html = '<div class="ui-dialog-content ui-widget-content" style="width: auto; min-height: 50px; max-height: none; height: auto;">' +					
					'<p><span class="ui-icon ui-icon-help" style="float: left; margin:0 7px 50px 0;"></span>';
					html += msg + '</p></div>';					
					break;
			}
			$(div).html(html);
			$(document.body).append(div);			
			var __self = this;			
			__self.__dialog = $(div).dialog({
					modal: true,
					resizable: false,
					autoOpen: false,
					buttons: {
						"确定": function(){
							__self.__dialog.dialog('close');
							__self.__return = true;
						}
					},
					close: function(){
						__self.__freeWidget();
						if (!$.common.__isNull(__self.__callback))
						{
							__self.__callback();							
						}
					}
			});
			switch (type)
			{
				case 3:
					__self.__dialog = $(div).dialog({
						buttons: {
							"确定": function(){
								__self.__dialog.dialog('close');
								if (!$.common.__isNull(__self.__callback))
								{
									__self.__callback();							
								}
							},
							"取消": function(){
								__self.__dialog.dialog('close');
								if (!$.common.__isNull(__self.__callback_2))
								{
									__self.__callback_2();							
								}
							}
						},
						close: function(){
							__self.__freeWidget();							
						}
					});
					break;
			}
			this.__widget = div;			
		},
		
		/**
		 * @desc 释放容器
		 */
		__freeWidget: function()
		{
			if (typeof(document.body) === 'undefined')
			{
				return;
			}
			var widget_div = document.getElementById(this.__widget_id);
			if (typeof(widget_div) === 'undefined')
			{
				return;
			}
			$(widget_div).remove();
			this.__widget = null;
		},
		
		/**
		 * @desc 显示弹出框
		 */
		__showDialog: function()
		{
			if (typeof(document.body) === 'undefined')
			{
				return;
			}
			var widget_div = $(document.body).find('#'+this.__widget_id).get(0);
			if (typeof(widget_div) === 'undefined')
			{
				return;
			}
			$(widget_div).dialog('open');
		}
	},
	/**
	 * @desc 通用类
	 * @since 20140730
	 */
	common: 
	{
		/**
		 * @desc 获取参数
		 * @param array args 参数数组
		 * @param integer index 数组序号
		 * @return null|unknown
		 */
		__getArg: function( args, index )
		{
			var res = null;
			if ( typeof( args[index] ) !== 'undefined' )
			{
				res = args[index];
			}
			return res;
		},
		/**
		 * @desc 判断是否为null值
		 * @param unknown value 值
		 * @return boolean
		 */
		__isNull: function( value )
		{
			if ( !value && typeof(value) === 'object')
			{
				return true;
			}
			return false;
		}
	}
});
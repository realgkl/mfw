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
/**
 * @desc jquery 自定义表格
 */
$.fn.extend({
	mygrid: function()
	{
		var __self = this;		
		var args = arguments;
		this.is_init = false; // 是否初始化
		this.datas = new Array(); // 数据
		this.options = {
			buttons: {},
			columns: {},
			url: null,
			method: null,
			limit: 1,
			page: 1,
			orderby: null,
			onFilterRowData: null,
			onData: null,
			onError: null
		};
		this.vars = {
			page: 0,
			totalpage: 0,
			totalcount: 0
		},
		this.columns = new Array();
		this.divs = {
			container: null, // 外壳
			toolbar: null, // 按钮工具栏
			table: null, // 表格
			header: null, // 标题头
			footer: null, // 页脚
			body: null, // 数据栏
			loading: null // 读取中图标
		};
		this.init = function()
		{
			// 表格元素初始化
			__self.divs.container = __self;
			__self.divs.toolbar = document.createElement( 'div' );
			$( __self.divs.toolbar ).addClass( 'ui-widget-header toolbar' );
			for ( var button_name in __self.options.buttons )
			{
				var button_option = __self.options.buttons[button_name];
				if ( typeof( button_option['name'] ) !== 'undefined' )
				{
					 button_name = button_option['name'];
				}
				var button_icon = null;
				if ( typeof( button_option['icon'] ) !== 'undefined' )
				{
					button_icon = button_option['icon'];
				}
				var button_click = null;
				if ( typeof( button_option['click'] ) !== 'undefined' )
				{
					button_click = button_option['click'];
				}
				var button = document.createElement( 'button' );
				$( button ).html( button_name );
				if ( !$.common.__isNull( button_icon ) )
				{
					$( button ).button( { icons: { primary: button_icon } } );
				}
				if ( !$.common.__isNull( button_click ) && typeof( button_click ) === 'function' )
				{
					$( button ).click(function(){
						button_click();
					});
				}
				$( __self.divs.toolbar ).append( button );
			}
			$( __self.divs.container ).append( __self.divs.toolbar );
			__self.divs.table = document.createElement( 'table' );
			$( __self.divs.container ).append( __self.divs.table );
			for ( column_name in __self.options.columns )
			{
				var column_obj = {};
				column_obj.name = column_name;
				column_obj.width = null;
				if ( typeof( __self.options.columns[column_name]['width'] ) !== 'undefined' )
				{
					column_obj.width = __self.options.columns[column_name]['width'];					
				}
				if ( typeof( __self.options.columns[column_name]['field'] ) !== 'undefined' )
				{
					column_obj.field = __self.options.columns[column_name]['field'];
				}
				__self.columns.push( column_obj );
			}
			if ( __self.columns.length > 0 )
			{
				var thead = document.createElement( 'thead' );
				var tr = document.createElement( 'tr' );
				$( thead )
					.addClass( 'ui-state-focus' )
					.append( tr )
				;
				for ( var i = 0; i <= __self.columns.length; i++ )
				{
					var th = document.createElement( 'th' );
					if ( typeof( __self.columns[i] ) !== 'undefined' )
					{
						var column_obj = __self.columns[i];
						$( th ).html( column_obj['name'] );
						if ( !$.common.__isNull( column_obj['width'] ) )
						{
							$( th ).css( 'width', column_obj['width'] );
						}
					}
					else
					{
						$( th ).addClass( 'last' );
					}
					$( tr ).append( th );
				}
				$( __self.divs.table ).append( thead );
				__self.divs.header = thead;
				
				var tbody = document.createElement( 'tbody' );
				$( __self.divs.table ).append( tbody );
				__self.divs.body = tbody;
				
				var tfoot = document.createElement( 'tfoot' );
				var tr = document.createElement( 'tr' );				
				var td = document.createElement( 'td' );				
				$( tfoot ).append( tr );
				$( tr ).append( td );				
				$( td )
					.attr( 'colspan', Number(__self.columns.length ) + 1 )
					.addClass( 'text-align-right' )
				;
				$( td ).addClass( 'last' );
				/*
				var td = document.createElement( 'td' );
				$( tr ).append( td );
				*/
				$( __self.divs.table ).append( tfoot );
				__self.divs.footer = tfoot;
			}
			// 变量初始化
			if ( $.common.__isNull( __self.options.method ) )
			{
				__self.options.method = 'post';
			}
			__self.getData( __self.options.page );
		};
		this.drawLoading = function()
		{
			if ( $.common.__isNull( __self.divs.loading ) )
			{
				$( __self.divs.body ).html( '' );
				var tr = document.createElement( 'tr' );
				var td = document.createElement( 'td' );				
				$( td )
					.attr({
						'colspan': Number( __self.columns.length + 1 ),					
					})					
					.addClass( 'last loading' )
				;
				$( tr )
					.css( 'height', '200px' )
					.append( td )
				;
				__self.divs.loading = tr;
				$( __self.divs.body ).append( __self.divs.loading );
			}
		};
		this.dropLoading = function()
		{
			if ( !$.common.__isNull( __self.divs.loading ) )
			{
				$( __self.divs.loading ).remove();
				__self.divs.loading = null;
			}
		}
		this.setOption = function( key, value )
		{
			if ( typeof( __self.options[key] ) !== 'undefined' )
			{
				__self.options[key] = value;
			}
		};
		this.first = function()
		{
			__self.options.page = 1;
		}
		this.drawFoot = function()
		{			
			var div = document.createElement( 'div' );			
			var page_tool = document.createElement( 'div' );
			var clear = document.createElement( 'div' );
			var page = __self.vars.page;
			var totalpage = __self.vars.totalpage;
			var totalcount = __self.vars.totalcount;					
			// 页数统计
			$( div )
				.addClass( 'total' )						
				.html( '当前第 <span class="red">' + page + '</span> 页, 一共 <span class="red">' + totalpage + '</span> 页 <span class="red">' + totalcount + '</span> 行' )
			;
			// clear
			$( clear ).addClass( 'clear' );
			// 翻页功能键
			$( page_tool ).addClass( 'ui-widget-header page_toolbar' );
			//	首页
			var btn = document.createElement( 'button' );
			$( btn )
				.button( { text: false, icons: { primary: 'ui-icon-seek-start' } } )
				.click(function(){
					__self.getData( 1 );
				});
			;
			$( page_tool ).append( btn );
			//	前页
			var btn = document.createElement( 'button' );
			$( btn )
				.button( { text: false, icons: { primary: 'ui-icon-seek-prev' } } )
				.click(function(){
					var page = Number( __self.vars.page - 1 );
					if ( page <= 0 )
					{
						page = 1;
					}
					__self.getData( page );
				});
			;
			$( page_tool ).append( btn );
			// 后页
			var btn = document.createElement( 'button' );
			$( btn )
				.button( { text: false, icons: { primary: 'ui-icon-seek-next' } } )
				.click(function(){
					var page = Number( __self.vars.page + 1 );
					if ( page > __self.vars.totalpage )
					{
						page = __self.vars.totalpage;
					}
					__self.getData( page );
				});
			;
			$( page_tool ).append( btn );
			// 尾页
			var btn = document.createElement( 'button' );
			$( btn )
				.button( { text: false, icons: { primary: 'ui-icon-seek-end' } } )
				.click(function(){
					var page = __self.vars.totalpage;
					__self.getData( page );
				});
			;
			$( page_tool ).append( btn );
			// 页数填框
			var input = document.createElement( 'input' );
			$( input )
				.attr( {
					'type': 'text'					
				} )
				.val( __self.vars.page )
				.keydown( function( event ){
					// 只能填数字
					if ( event.which < 8 || ( event.which > 8 && event.which < 13 ) ||
						( event.which > 13 && event.which < 37 ) || ( event.which > 40 && event.which < 46 ) ||
						( event.which > 46 && event.which < 48 ) || ( event.which > 57 && event.which < 96 ) ||
						( event.which > 105 && event.which < 116 ) || event.which > 116 )
					{
						return false;
					}
					// 回车事件
					if ( event.which == 13 )
					{
						var page = parseInt( $( this ).val() );
						__self.vars.page = page;
						__self.getData( page );
						return true;
					}
				} )
			;
			
			$( page_tool ).append( input );
			// 刷新
			var btn = document.createElement( 'button' );
			$( btn )
				.button( { text: false, icons: { primary: 'ui-icon-refresh'} } )
				.click( function(){
					var input = $( this ).parent().find( 'input' );
					if ( typeof( $( input ).get(0) !== 'undefined' ) )
					{
						__self.vars.page = parseInt( $( input ).val() );
					}
					var page = __self.vars.page;
					__self.getData( page );
				} );
			$( page_tool ).append( btn );
			
			$( __self.divs.footer ).find('td')
				.append( page_tool )
				.append( div )
				.append( clear )
			;
		};
		this.drawRow = function( index, data )
		{			
			var tr = document.createElement( 'tr' );
			for ( var i = 0; i <= __self.columns.length; i++ )
			{
				var td = document.createElement( 'td' );
				if ( i === __self.columns.length )
				// 最后一格
				{
					$( td ).addClass( 'last' );
				}
				else
				// 字段格
				{					
					if ( $.common.__isNull( data ) )
					{						
						$( td ).html( '&nbsp;' );
					}
					else
					{
						if ( !$.common.__isNull( __self.options.onFilterRowData ) && typeof( __self.options.onFilterRowData ) === 'function' )
						{
							data = __self.options.onFilterRowData( index, data );
						}
						var column = __self.columns[i];
						var column_field = null;
						var column_data = null;
						if ( typeof( column.field ) !== 'undefined' )
						{
							column_field = column.field;
							if ( typeof( data[column_field] ) !== 'undefined' )
							{
								column_data = data[column_field];
							}
						}
						if ( $.common.__isNull( column_data ) )
						{
							column_data = '&nbsp;';
						}
						$( td ).html( column_data );
					}
				}
				$( tr ).append( td );
			}
			$( __self.divs.body ).append( tr );
		};
		this.emptyBodyAndFoot = function()
		{
			$( __self.divs.body ).html( '' );
			$( __self.divs.footer ).find( 'td' ).html( '' );
		};
		this.getDataSucc = function( datas )
		{
			var rows = datas.rows;
			__self.vars.page = datas.page;
			__self.vars.totalcount = datas.total;
			__self.vars.totalpage =Math.ceil( Number( __self.vars.totalcount ) / Number( __self.options.limit ) );
			if ( !$.common.__isNull( __self.options.onData ) && typeof( __self.options.onData ) === 'function' )
			{
				__self.options.onData( rows );
			}
			__self.emptyBodyAndFoot();
			for ( var i = 0; i < __self.options.limit; i++ )
			{
				var row = null;
				if ( typeof( rows[i] ) !== 'undefined' )
				{
					row = rows[i];
				}
				__self.drawRow( i, row );
			}
			__self.drawFoot();
		};
		this.getData = function( page )
		{
			if ( !$.common.__isNull( __self.options.url ) )
			{
				$.ajax({
					url: __self.options.url,
					method: __self.options.method,
					timeout: 3000,
					data: {
						page: page,
						limit: __self.options.limit,
						orderby: __self.options.orderby,
					},
					dataType: 'json',
					success: function( datas )
					{
						__self.getDataSucc( datas );
					},
					error: function()
					{
						if ( !$.common.__isNull( __self.options.onError ) && typeof( __self.options.onError ) === 'function' )
						{
							__self.options.onError();
						}
					},
					beforeSend: function()
					{
						__self.drawLoading();
					},
					complete: function()
					{
						__self.dropLoading();
					}
				})
			}
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
			if ( this.is_init === false )
			{
				this.init();
				this.is_init = true;
			}
		}
	}
});
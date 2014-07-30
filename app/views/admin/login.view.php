<div class="wrap">
	<h1></h1>
	<form method="post" name="login" action="/admin/user/login" autoComplete="off" onsubmit="return checkForm();">
		<div class="login">
			<ul>
<?php if ( $error !== false ) { ?>
				<li>
					<li style="text-align: center;color: #ff6a00;font-weight: bold;display:block;" id="error"><?php echo $error; ?></li>
				</li>
<?php } ?>
				<li>
					<input class="input" id="user_name" required name ="user_name" type="text" placeholder="帐号" title="帐号" />					
				</li>
				<li>
					<input class="input" id="user_pass" required name ="user_pass" type="password" placeholder="密码" title="密码" />
				</li>
			</ul>
			<button type="submit" name="submit" class="btn">登录</button>
			<input type="hidden" name="token" value="<?php echo $token; ?>"/>
		</div>
	</form>
</div>
<script>
<!--
function checkForm()
{
	var _controller = 'index';
	var _username = _username_div.val();
	var _reg = /^[0-9|a-z|A-Z]+$/;
	_username = _username.replace(/(^\s*)|(\s*$)/g, "");
	if (!_reg.test(_username)) {
		$('input[name="user_name"]').focus();
		$.messager.alert('用户名须由小写英文字母和数字构成。');
		return false;
	}
	if (_username.length < 1)
	{
		$.messager.alert('帐号名必须输入。');
		$('input[name="user_name"]').focus();
		return false;
	}
	return false;
	var _password = $('input[name="user_pass"]').val();
	if (_password.length < 1)
	{
		$('input[name="user_pass"]').focus();
		$.messager.alert('密码必须输入。');
		return false;
	}
}

// 失去光标去除用户名两端空格并返回
$("#user_name").blur(function() {
	var v = $.trim($(this).val());
	$(this).attr("value", v);
});
//-->
</script>
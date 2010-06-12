<div class="admin-login border-gray">
	<img src="/framework/admin/imgs/login_icon.gif" class="fleft"/>
	<div class="fleft" style="width:280px;height:140px;padding:20px;">
		<form action="/page/login" method="post">
			username : <input type="text" style="width:200px" value="admin" name="username"/><br/><br/>
			password : <input type="password" style="width:200px" value="admin" name="password"/><br/><br/>
			remember me : <input type="checkbox" checked="checked" name="remember" value="1"/><br/><br/>
			<a class="button fright" href="#" 
				onclick="admin.login(this);this.blur();"><span>Login</span></a> 
			<script type="text/javascript">
				document.getElementsByTagName('input')[0].focus();
			</script>
		</form>
	</div>
</div>
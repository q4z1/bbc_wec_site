<?php
/**
 * template_default_html_login_login
 */
?>
<div class="row">
	<div class="login col-md-6 col-md-offset-3 form-group">
		<form name="login_form" id="login_form" action="<?=cfg::$web_root . 'main/signin/'?>" method="post">
		<input type="hidden" name="passhash" id="passhash">
		<fieldset>
			<legend>Login:</legend>
			<label for="username">Username:</label><br>
			<input type="text" name="username" id="username" class="form-control" placeholder="Username"><br>
			<label for="password">Password:</label><br>
			<input type="password" name="password" id="password" class="form-control" placeholder="Password"><br>
			<button class="btn btn-primary pull-right" type="submit" name="signin" id="signin">Login</button>
		</fieldset>
		</form>
	</div>
</div>
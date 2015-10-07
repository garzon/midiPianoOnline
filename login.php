<?php
//813626073@qq.com
require_once('./init.php');

Visitor::logout();

$pageTitle = 'Sign In/Sign Up';
require_once(ROOT . '/mixins/header.php');
mixin_header($pageTitle, 'login', [], $data->extra_msg, $data->extra_msg_type);
?>
<body class="body-index">
<div class="container main-block">
	<div class="col-sm-5 box block-page">
		<h2 class="col-sm-offset-5">Login</h2>
		<form method="post" class="form-horizontal">
			<div class="form-group">
				<label for="name" class="col-sm-5 control-label">Email or Username</label>
				<div class="col-sm-6">
					<input class="form-control" id="name" name='name' placeholder="Email or Username">
				</div>
			</div>
			<div class="form-group">
				<label for="pwd" class="col-sm-5 control-label">Password</label>
				<div class="col-sm-6">
					<input name='pwd' type="password" class="form-control" id="pwd" placeholder="Password">
				</div>
			</div>
			<input type='hidden' name='type' value="login">
			<div class="form-group">
				<div class="col-sm-offset-5 col-sm-6">
					<button type="submit" class="btn btn-default">Sign In</button>
					<br />
					<a class="pull-right" href="<?= DOMAIN ?>/forget_password.php">Forget password?</a>
				</div>
			</div>
		</form>
	</div>
	<div class="col-sm-offset-1 col-sm-5 box block-page">
		<h2 class="col-sm-offset-5">Sign Up</h2>
		<form method="post" class="form-horizontal">
			<div class="form-group">
				<label for="name" class="col-sm-5 control-label">Username</label>
				<div class="col-sm-6">
					<input class="form-control" id="name" name='name' placeholder="Username">
				</div>
			</div>
			<div class="form-group">
				<label for="email" class="col-sm-5 control-label">Email</label>
				<div class="col-sm-6">
					<input name='email' placeholder="Email" type="email" class="form-control" id="email">
				</div>
			</div>
			<div class="form-group">
				<label for="pwd" class="col-sm-5 control-label">Password</label>
				<div class="col-sm-6">
					<input name='pwd' type="password" class="form-control" id="pwd" placeholder="Password">
				</div>
			</div>
			<? /*
			<div class="form-group">
				<label for="invitation_code" class="col-sm-5 control-label">邀请码</label>
				<div class="col-sm-6">
					<input name='invitation_code' type="password" class="form-control" id="invitation_code" placeholder="邀请码">
				</div>
			</div> */ ?>
			<input type='hidden' name='type' value="register">
			<div class="form-group">
				<div class="col-sm-offset-5 col-sm-6">
					<button type="submit" class="btn btn-default">Sign Up</button>
				</div>
			</div>
		</form>
	</div>
</div>
</body>


<?php
	require_once(ROOT . '/mixins/footer.php');
	mixin_footer();
?>
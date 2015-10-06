<?php
//813626073@qq.com
require_once('./init.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$user = current(User::find(['email' => $_POST['email']]));
	if ($user) {
		Email::sendMail($_POST['email'], 'CTF CIRCLE 重置密码', '<a href="http://10.131.1.19/ctfcircle/reset_password.php?id=' . $user->id . '&token=' . urlencode(Visitor::generate_token_for_user($user)) . '">点此重置密码</a>');
		die('<p class="col-sm-offset-3">验证邮件已发送！</p>');
	} else { ?>
		<script>alert('邮箱不存在！')</script>
	<? }
}

$pageTitle = '忘记密码';
require_once('./header.php');
?>
<body class="body-index">
<div class="container main-block">
	<div class="col-md-5 box block-page">
		<h2>找回密码</h2>
		<form method="post" class="form-horizontal">
			<div class="form-group">
				<label for="email" class="col-sm-3 control-label">邮箱</label>
				<div class="col-sm-8">
					<input class="form-control" id="email" name='email' placeholder="请输入您的电子邮箱地址">
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-6">
					<button type="submit" class="btn btn-default">发送验证邮件</button>
				</div>
			</div>
		</form>
	</div>
</div>
</body>


<?php
require_once(ROOT . '/mixins/footer.php');
mixin_footer(true);
?>
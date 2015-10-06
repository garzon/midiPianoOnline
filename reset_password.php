<?php
//813626073@qq.com
require_once('./init.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$user = User::fetch(postInt('userid', 0));
	if($user && Visitor::verify_token($user, strval($_POST['token']))) {
		$user->password = Visitor::hash_password($_POST['pwd']);
		$user->save();
		?>
		<script>
			alert('重置密码成功！');
			window.location.href = '<?= DOMAIN ?>/index.php';
		</script>
		<?
		exit;
	}
}

$pageTitle = '重置密码';
require_once('./header.php');
?>
<body class="body-index">
<div class="container main-block">
<div class="col-md-5 box block-page">
	<h2>重置密码</h2>
	<form method="post" class="form-horizontal">
		<div class="form-group">
			<label for="pwd" class="col-sm-3 control-label">新密码</label>
			<div class="col-sm-8">
				<input class="form-control" id="pwd" name='pwd' placeholder="请输入您的新密码" type="password">
			</div>
		</div>
		<input name="userid" value="<?= intval($_GET['id']) ?>" type="hidden">
		<input name="token" value="<?= htmlentities($_GET['token'], ENT_QUOTES) ?>" type="hidden">
		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-6">
				<button type="submit" class="btn btn-default">重置密码</button>
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
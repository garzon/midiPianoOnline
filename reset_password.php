<?php
//813626073@qq.com
require_once('./init.php');

$pageTitle = '重置密码';
require_once(ROOT . '/mixins/header.php');
mixin_header($pageTitle, 'view', [], $data->extra_msg, $data->extra_msg_type);
?>

<body class="body-index">
<div class="container main-block">
<div class="col-md-5 box block-page">
	<h2>Reset password</h2>
	<form method="post" class="form-horizontal">
		<div class="form-group">
			<label for="pwd" class="col-sm-3 control-label">New password</label>
			<div class="col-sm-8">
				<input class="form-control" id="pwd" name='pwd' placeholder="Your new password" type="password">
			</div>
		</div>
		<input name="userid" value="<?= intval($_GET['id']) ?>" type="hidden">
		<input name="token" value="<?= htmlentities($_GET['token'], ENT_QUOTES) ?>" type="hidden">
		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-6">
				<button type="submit" class="btn btn-default">Submit</button>
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
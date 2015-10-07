<?php
//813626073@qq.com
require_once('./init.php');

$pageTitle = 'Forget password?';
require_once(ROOT . '/mixins/header.php');
mixin_header($pageTitle, 'index', [], $data->extra_msg, $data->extra_msg_type);
?>
<body class="body-index">
<div class="container main-block">
	<div class="col-md-5 box block-page">
		<h2>To reset your password</h2>
		<form method="post" class="form-horizontal">
			<div class="form-group">
				<label for="email" class="col-sm-3 control-label">Email</label>
				<div class="col-sm-8">
					<input class="form-control" id="email" name='email' placeholder="Your email address of the account">
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-6">
					<button type="submit" class="btn btn-default">Send mail</button>
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
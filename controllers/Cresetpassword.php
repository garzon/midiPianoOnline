<?php
// 610204679@qq.com

class Cresetpassword extends BaseController {
	public function __construct() {
		parent::__construct();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$user = User::fetch(Util::postInt('userid', 0));
			if($user && Visitor::verify_token($user, strval($_POST['token']))) {
				$user->password = Visitor::hash_password(Util::post('pwd'));
				$user->save();
				?>
				<script>
					alert('Successfully reset your password!');
					window.location.href = '<?= DOMAIN ?>/index.php';
				</script>
				<?
			}
		}
	}
}
<?php
// 610204679@qq.com

class Cforgetpassword extends BaseController {
	public function __construct() {
		parent::__construct();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$user = current(User::find(['email' => Util::post('email')]));
			if ($user) {
				Email::sendMail(Util::post('email'), 'Reset your password @ Midi Piano Online', '<a href="' . DOMAIN .  '/reset_password.php?id=' . $user->id . '&token=' . urlencode(Visitor::generate_token_for_user($user)) . '">Click this to reset your password</a>');
				self::setExtraMsg('The email has been sent! Please check.</p>', self::EXTRA_MSG_SUCCESS);
			} else { ?>
				<script>alert('The account doesn\'t exist.')</script>
			<? }
		}
	}
}
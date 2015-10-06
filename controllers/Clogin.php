<?php
// 610204679@qq.com

class Clogin extends BaseController {
	public function __construct() {
		parent::__construct();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($_POST['type'] == 'login') {
				$result = Visitor::login(Util::post('name',''), Util::post('pwd',''));
				if ($result != 'success') self::setExtraMsg($result, self::EXTRA_MSG_DANGER);
				else throw new RedirectException(DOMAIN . '/index.php');
			}
			if ($_POST['type'] == 'register') {
				$result = Visitor::register(Util::post('name',''), Util::post('email',''), Util::post('pwd',''), Util::post('invitation_code',''));
				if ($result != 'success') self::setExtraMsg($result, self::EXTRA_MSG_DANGER);
				else throw new RedirectException(DOMAIN . '/index.php');
			}
		}
	}
}
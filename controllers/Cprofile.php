<?php
// 610204679@qq.com

class Cprofile extends NeedLoginController {
	public function __construct() {
		parent::__construct();

		$visitor = Visitor::user();
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$acceptedProps = ['location', 'introduction'];
			foreach ($_POST as $key => $value) {
				if(!in_array($key, $acceptedProps)) continue;
				$visitor->$key = $value;
			}
			$visitor->save();
			throw new RedirectException(DOMAIN . '/user.php?id=' . $visitor->id);
		}
	}
}
<?php
// 610204679@qq.com

class NeedLoginController extends BaseController {
	public function __construct() {
		parent::__construct();

		if(!Visitor::user()) {
			throw new RedirectException(ROOT . '/login.php');
		}
	}
}
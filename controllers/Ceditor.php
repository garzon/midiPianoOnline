<?php
// 610204679@qq.com

class Ceditor extends BaseController {
	public function __construct() {
		parent::__construct();

		$this->data->midiUrl = DOMAIN . '/api/getMidi.php?id=' . Util::getInt('id', 0);
	}
}
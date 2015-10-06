<?php
// 610204679@qq.com

class BaseController {
	protected static $contentItem = [];

	const EXTRA_MSG_WARNING = 'alert-warning';
	const EXTRA_MSG_SUCCESS = 'alert-success';
	const EXTRA_MSG_DANGER  = 'alert-danger';
	const EXTRA_MSG_DEFAULT = 'alert-default';

	public function __construct() {
		$this->data = new \stdClass();
		$this->data->extra_msg = '';
		$this->data->extra_msg_type = '';
	}

	public function getViewData() {
		return $this->filterContent($this->data);
	}

	protected function filterContent(&$data) {
		foreach(static::$contentItem as $key) {
			if(isset($data->$key)) $data->$key = htmlentities($data->key, ENT_QUOTES);
		}
		return $data;
	}

	protected function setExtraMsg($msg, $type = self::EXTRA_MSG_DEFAULT) {
		$this->data->extra_msg = $msg;
		$this->data->extra_msg_type = $type;
	}

	protected function redirectToIndex() {
		throw new RedirectException(DOMAIN . '/index.php');
	}
}
<?php
// 610204679@qq.com

class Cuser extends BaseController {
	public function __construct() {
		parent::__construct();

		$visitor = Visitor::user();

		$user = User::fetch(Util::getInt('id', $visitor ? $visitor->id : 0));
		if (!$user) {
			self::redirectToIndex();
		}

		$query = ['userId' => $user->id];

		if ($user->id == $visitor->id) {
			$this->data->nav_tab = 2;
			$query['isHidden'] = '*';
		}

		$midis = MidiFile::find(Util::buildQueryFromFilters($query), ['limit' => 100,  'sort' => ['createdTime' => -1]]);

		$this->data->midis = $midis;
		$this->data->user = $user;
		$this->data->visitor = $visitor;
	}
}
<?php
// 610204679@qq.com

class Cview extends BaseController {
	public function __construct() {
		parent::__construct();

		$midi = MidiFile::fetch(Util::getInt('id', 0));
		if (!$midi) {
			self::redirectToIndex();
		}

		$visitor = Visitor::user();

		if (!$midi->checkHiddenPermission($visitor)) {
			self::redirectToIndex();
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST' && $visitor) {
			if (Util::post('post_type', '') == 'manager_control') {
				if(Visitor::isSuperman()) {
					$midi->delete();
					self::redirectToIndex();
				}
			} else {
				$comment = new MidiComment();
				$comment->content = Util::post('content', '');
				$comment->userId = $visitor->id;
				$comment->midiId = $midi->id;
				$comment->save(true);
				self::setExtraMsg("Successfully submitted!", self::EXTRA_MSG_SUCCESS);
			}
		}

		$comments = MidiComment::find(['midiId' => $midi->id], ['sort' => ['createdTime' => 1]]);

		if($visitor)
			$midi->viewedBy($visitor);

		$user = User::fetch($midi->userId);

		$this->data->comments = $comments;
		$this->data->user = $user;
		$this->data->visitor = $visitor;
		$this->data->midi = $midi;
		$this->data->viewCount = count($midi->viewList);
	}
}
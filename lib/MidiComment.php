<?php
// 610204679@qq.com

class MidiComment extends MongoContentData {
	public $midiId;
	public $userId;
	public $content;
	public $agreeCount;
	public $disagreeCount;

	public function save($isUpdate = false) {
		if (!$this->id) {
			$this->agreeCount = 0;
			$this->disagreeCount = 0;
		}
		parent::save($isUpdate);
	}

	public static function checkVisitorCommented($id) {
		$key = 'user-comment' . $id;
		$userid = Visitor::user()->id;
		return (new RedisSet($key))->isMember($userid);
	}

	public static function addVisitorComment($id) {
		$key = 'user-comment' . $id;
		$userid = Visitor::user()->id;
		return (new RedisSet($key))->add($userid);
	}
}
<?php
// 610204679@qq.com

class User extends MongoContentData {
	public $name;
	protected $score;
	public $email;
	public $hash;
	public $lastLoginTime;
	public $isSuperman;
	public $uploadedCounter;
	public $location;
	public $introduction;

	public function save($isUpdate = false) {
		if(!$this->id) {
			$this->score = 0;
			$this->uploadedCounter = 0;
			$this->location = '';
			$this->introduction = '';
		}
		parent::save($isUpdate);
	}

	public function getScore() {
		return $this->score;
	}

	public function _addScore($score) {
		$this->score += $score;
		$this->save();
	}
}
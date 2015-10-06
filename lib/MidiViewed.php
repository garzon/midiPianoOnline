<?php
// 610204679@qq.com

class MidiViewed extends MongoData {
	public $midiId;
	public $userId;
	public $createdTime;

	public function save() {
		if (!$this->id) {
			$this->createdTime = time();
		}
		parent::save();
	}
}
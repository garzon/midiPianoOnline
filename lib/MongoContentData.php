<?php
// 610204679@qq.com

class MongoContentData extends MongoData {
	public $createdTime;
	public $updatedTime;

	public function save($isUpdate = false) {
		$time = time();
		if(!$this->id) {
			$this->createdTime = $time;
		}
		if($isUpdate) $this->updatedTime = $time;
		parent::save();
	}
}
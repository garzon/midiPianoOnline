<?php
// 610204679@qq.com

class MidiFile extends MongoContentData {
	public $name;
	public $userId;
	public $introduction;
	public $isForkedFromId;
	public $originId;
	public $price;
	public $realPath;
	public $isHidden;
	public $category;
	public $viewList;

	public function save($isUpdate = false) {
		if(!$this->id && !$this->originId) $this->originId = null;
		if(!$this->id) {
			$this->viewList = [];
		}
		parent::save($isUpdate);
	}

	public static function find(array $query, array $opt = []) {
		if(!isset($query['isHidden']))
			$query['isHidden'] = false;
		else unset($query['isHidden']);
		return parent::find($query, $opt);
	}

	public function fork(User $user) {
		$ret = new static();
		$ret->name = 'Duplicate - ' . $this->name;
		$ret->userId = $user->id;
		$ret->introduction = $this->introduction;
		$ret->isForkedFromId = $this->id;
		$ret->originId = $this->originId ?: $this->id;
		$ret->price = 0;
		$ret->isHidden = false;
		$ret->category = $this->category;
		$ret->realPath = Attachment::copyFile($this->realPath);

		$ret->save();
		return $ret;
	}

	public function delete() {
		if($this->realPath && strpos($this->realPath, 'mid') && file_exists($this->realPath)) {
			Attachment::deleteFile($this->realPath);
		}
		parent::delete();
	}

	public function setMidiFile($field_name) {
		if($this->realPath && strpos($this->realPath, 'mid') && file_exists($this->realPath)) {
			Attachment::deleteFile($this->realPath);
		}
		$this->realPath = Attachment::generateFilepath('mid');
		move_uploaded_file($_FILES[$field_name]['tmp_name'], $this->realPath);
		return $this;
	}

	public function checkHiddenPermission($visitor) {
		if ($visitor->isSuperman) return true;
		if (!$this->isHidden) return true;   // 非隐藏所有人可见
		if (!$visitor) return false;         // 隐藏至少需登录
		if ($visitor->id == $this->userId) return true;  // 主人可见
		return false;
	}

	public function viewedBy(User $visitor) {
		if(in_array($visitor->id, $this->viewList)) return;
		$this->viewList []= $visitor->id;
		$this->save();
		$viewObj = new MidiViewed();
		$viewObj->userId = $visitor->id;
		$viewObj->midiId = $this->id;
		$viewObj->save();
	}
}
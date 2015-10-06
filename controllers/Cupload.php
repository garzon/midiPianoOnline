<?php
// 610204679@qq.com

class Cupload extends NeedLoginController {
	public function __construct() {
		parent::__construct();
		$visitor = Visitor::user();

		$this->data->pageTitle = 'Upload MIDI';

		$editMidiFlag = Util::get('editMidiId', 0);
		if ($editMidiFlag) {
			$midi = MidiFile::fetch($editMidiFlag);
			if (!$midi || $midi->userId != $visitor->id) $editMidiFlag = 0;
			else $this->data->pageTitle = 'Edit MIDI Info';
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$extra_msg_type = 'alert-danger';

			$user = Visitor::user();
			if (!$editMidiFlag) $midi = new MidiFile();
			$midi->name = Util::post('name', '');
			$midi->category = Util::post('category', '');
			$midi->price = Util::postInt('price', 0);
			$midi->content = Util::post('comment', '');
			if ($midi->price > 500 || $midi->price < 0) {
				$extra_msg = "Please input a valid price(0~500).";
				self::setExtraMsg($extra_msg, $extra_msg_type);
				return;
			}

			if(!$editMidiFlag) $midi->userId = $user->id;

			if(isset($_FILES['midiData']['name']) && $_FILES['midiData']['name']) {
				$midi->setMidiFile('midiData');
			} else {
				if(!$editMidiFlag) {
					$extra_msg = "Please select a midi file to upload.";
					self::setExtraMsg($extra_msg, $extra_msg_type);
					return;
				}
			}

			$midi->save(true);
			throw new RedirectException(DOMAIN . "/view.php?id={$midi->id}");
		}


		$default_category = null;
		$default_name = null;
		$default_comment = null;
		$default_price = null;

		if ($editMidiFlag) {
			$default_category = $midi->category;
			$default_name = $midi->name;
			$default_comment = $midi->introduction;
			$default_price = $midi->price;
		}

		$this->data->default_category = $default_category;
		$this->data->default_name = $default_name;
		$this->data->default_comment = $default_comment;
		$this->data->default_price = $default_price;
		$this->data->editMidiFlag = $editMidiFlag;
	}
}
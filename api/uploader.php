<?php

require_once('../init.php');

$visitor = Visitor::user();
if(!$visitor) {
	die("alert('Please login');");
}

if(isset($_POST['data'])) {
	$postData = hex2bin($_POST['data']);
	if(strlen($postData) > 1000000) die('alert("Midi file too large.");');
	$id = Util::getInt('id', 0);
	$midi = MidiFile::fetch($id);
	if(!$id || !$midi) {
		// create
		$id = 1;
		$midi = MidiFile::fetch($id)->fork($visitor);
		$midi->name = 'noname';
		$midi->isForkedFromId = false;
		$midi->introduction = '';
		$midi->category = 'others';
		$midi->save();
		die("alert('successfully saved!');window.location.href='" . DOMAIN . "/editor.php?id={$midi->id}';");
	} else {
		if($midi->userId == $visitor->id) {
			// is owner, modify the file
			$f = fopen($midi->realPath, "wb");
			fwrite($f, $postData);
			fclose($f);
			die('alert("Successfully saved!");');
		} else {
			// fork
			$midi = $midi->fork($visitor);
			$f = fopen($midi->realPath, "wb");
			fwrite($f, $postData);
			fclose($f);
			die("alert('Successfully forked to your repository!');window.location.href='" . DOMAIN . "/editor.php?id={$midi->id}';");
		}
	}
}

die('alert("error");');

?>
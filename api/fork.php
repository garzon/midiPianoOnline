<?php
// 610204679@qq.com

require_once('../init.php');

function setFailMsg($msg) {
	$msg = ['msg' => $msg];
	die(json_encode($msg));
}

function setSuccId($id) {
	$id = ['id' => $id];
	die(json_encode($id));
}

$visitor = Visitor::user();
if(!$visitor) setFailMsg("Please login.");

$id = Util::postInt('id', 0);
$midi = MidiFile::fetch($id);

if(!$midi) {
	setFailMsg("MIDI File Not Found.");
} else {
	$newMidi = $midi->fork($visitor);
	setSuccId($newMidi->id);
}

?>
<?php
// 610204679@qq.com

require_once('../init.php');

$id = getInt('id', 0);
$midi = MidiFile::fetch($id);

if(!$midi) {
	$midi = MidiFile::fetch(1);
}

if(file_exists($midi->realPath))
	$data = file_get_contents($midi->realPath);
else
	$data = 'error';

echo $data;

?>
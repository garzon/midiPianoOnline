<?php
// 610204679@qq.com

require_once('../init.php');

$visitor = Visitor::user();
if(!$visitor) die("Please login.");

$id = Util::getInt('id', 0);
$midi = MidiFile::fetch($id);

if(!$midi) {
	die("Midi not found.");
}

if(!file_exists($midi->realPath))
	die("error.");

header("Content-Type: application/force-download");
header("Content-Disposition: attachment; filename={$midi->name}_" . date('Y-m-d-H:i', time()) . '.mid');
header("Content-Transfer-Encoding: binary");
readfile($midi->realPath);

?>
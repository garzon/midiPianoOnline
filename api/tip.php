<?php
// 610204679@qq.com

require_once('../init.php');

if(!Visitor::user()) die('Please login.');

$id = Util::postInt('id');
$midi = MidiFile::fetch($id);
if(!$midi) die('MidiFile not found');

$midi->tipBy(Visitor::user());

echo 'succ';
<?php
// 610204679@qq.com

require_once('../init.php');

if(!Visitor::user()) die('Please login.');

$id = Util::postInt('id');
if(!MidiComment::fetch($id)) die('comment not found');
if (MidiComment::addVisitorComment($id)) {
	$comment = MidiComment::fetch($id);
	$comment->agreeCount += 1;
	$comment->save();
}

echo 'succ';
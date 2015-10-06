<?php

$global_timer = microtime();

define('ROOT', dirname(__FILE__));
define('DOMAIN', "//{$_SERVER['HTTP_HOST']}/midiPianoOnline");

date_default_timezone_set('Asia/Hong_Kong');

spl_autoload_register(function($class_name) {
	$file = ROOT . '/lib/' . $class_name . '.php';
	if (file_exists($file)) require_once($file);
});

require_once(ROOT . '/secret.inc.php');

$clsName = 'C' . str_replace(['php', 'midiPianoOnline'], '', preg_replace('/[^0-9A-Za-z]*/', '', $_SERVER['SCRIPT_NAME']));
$file = ROOT . '/controllers/' . $clsName . '.php';

if(file_exists($file)) {
	require_once($file);
	try {
		$data = (new $clsName())->getViewData();
	} catch(RedirectException $e) {
		Util::redirect($e->getMessage());
	}
}

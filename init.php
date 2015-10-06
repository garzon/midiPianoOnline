<?php

define('ROOT', dirname(__FILE__));
define('DOMAIN', "//{$_SERVER['HTTP_HOST']}/midiPianoOnline");

date_default_timezone_set('Asia/Hong_Kong');

spl_autoload_register(function ($class_name) {
	$file = ROOT . '/lib/' . $class_name . '.php';
	if (file_exists($file)) require_once($file);
});

require_once(ROOT . '/util.php');

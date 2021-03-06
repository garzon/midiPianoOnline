<?php

function mixin_header($pageTitle, $nav_tab, $cssPathArr = [], $extra_msg = '', $extra_msg_type = '', $extra_msg_exit = false) {
	?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $pageTitle ?></title>
	<script src="<?= DOMAIN ?>/bower_components/jquery/dist/jquery.min.js"></script>
	<script src="<?= DOMAIN ?>/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="<?= DOMAIN ?>/bower_components/angular/angular.min.js"></script>
	<script src="<?= DOMAIN ?>/bower_components/jquery-ui/jquery-ui.min.js"></script>
	<script src="<?= DOMAIN ?>/js/util.js"></script>

	<script src="<?= DOMAIN ?>/bower_components/webcomponentsjs/webcomponents.min.js"></script>
	<script src="<?= DOMAIN ?>/bower_components/angular-ui/build/angular-ui.min.js"></script>

	<link rel="import" href="<?= DOMAIN ?>/bower_components/polymer/polymer.html">

	<link rel="import" href="<?= DOMAIN ?>/bower_components/iron-flex-layout/iron-flex-layout.html">

	<link rel="import" href="<?= DOMAIN ?>/bower_components/paper-styles/color.html">
	<link rel="import" href="<?= DOMAIN ?>/bower_components/paper-styles/typography.html">
	<link rel="import" href="<?= DOMAIN ?>/bower_components/paper-styles/shadow.html">

	<link rel="import" href="<?= DOMAIN ?>/bower_components/iron-flex-layout/classes/iron-flex-layout.html">
	<link rel="import" href="<?= DOMAIN ?>/bower_components/paper-toolbar/paper-toolbar.html">
	<link rel="import" href="<?= DOMAIN ?>/bower_components/paper-tabs/paper-tabs.html">
	<link rel="import" href="<?= DOMAIN ?>/bower_components/paper-tabs/paper-tab.html">

	<link rel="import" href="<?= DOMAIN ?>/bower_components/paper-item/paper-item.html">
	<link rel="import" href="<?= DOMAIN ?>/bower_components/iron-collapse/iron-collapse.html">
	<link rel="import" href="<?= DOMAIN ?>/bower_components/paper-menu/paper-menu.html">
	<link rel="import" href="<?= DOMAIN ?>/bower_components/paper-menu/paper-submenu.html">

	<link rel="import" href="<?= DOMAIN ?>/bower_components/x-webmidi/x-webmidirequestaccess.html">
	<link rel="import" href="<?= DOMAIN ?>/bower_components/x-webmidi/extras/wm-pckeyboard/wm-pckeyboard.html">

	<link rel="stylesheet" href="<?= DOMAIN ?>/bower_components/jquery-ui/themes/smoothness/jquery-ui.min.css">
	<link rel="stylesheet" href="<?= DOMAIN ?>/bower_components/angular-ui/build/angular-ui.min.css">
	<link rel="stylesheet" href="<?= DOMAIN ?>/bower_components/bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= DOMAIN ?>/css/base.css">
	<? foreach ($cssPathArr as $cssFile) { ?>
		<link rel="stylesheet" href="<?= DOMAIN ?>/css/<?= $cssFile ?>">
	<? } ?>

	<style is="custom-style">
		.horizontal-section-container {
		@apply(--layout-horizontal);
		@apply(--layout-center-justified);
		@apply(--layout-wrap);
		}

		.horizontal-section {
			background-color: white;
			padding: 24px;
			margin-right: 24px;
			min-width: 200px;
		@apply(--shadow-elevation-2dp);
		}

		:root {
			--paper-tabs-selection-bar-color: rgb(43, 162, 226);
		}
	</style>

	<script src="<?= DOMAIN ?>/bower_components/requirejs/require.js"></script>
	<script>
		require.config({
			paths : {
				"MidiController" : ["<?= DOMAIN ?>/js/midicontroller"],
				"MidiData" : ["<?= DOMAIN ?>/js/mididata"],
				"MidiView" : ["<?= DOMAIN ?>/js/midiview"],
				"WebAudioChannel" : ["<?= DOMAIN ?>/js/webaudiochannel"],
				"WebAudioController" : ["<?= DOMAIN ?>/js/webaudiocontroller"],
				"WebAudioInstructmentNode" : ["<?= DOMAIN ?>/js/webaudioinstructmentnode"],
				"WebAudioMuyuNode" : ["<?= DOMAIN ?>/js/webaudiomuyunode"],
				"WebAudioPianoNode" : ["<?= DOMAIN ?>/js/webaudiopianonode"],
				"WebAudioSynth" : ["<?= DOMAIN ?>/js/webaudiosynth"],
				"WebAudioViolinNode" : ["<?= DOMAIN ?>/js/webaudioviolinnode"],
				"WebAudioHornNode": ["<?= DOMAIN ?>/js/webaudiohornnode"],
				"WebAudioFluidPianoNode": ["<?= DOMAIN ?>/js/webaudiofluidpianonode"],
				"WebMidiInstructmentNode": ["<?= DOMAIN ?>/js/webmidiinstructmentnode"],
				"OutputStream": ["<?= DOMAIN ?>/js/outputstream"],
				"MidiEvent": ["<?= DOMAIN ?>/js/midievent"],
				"jasmid-Stream": ["<?= DOMAIN ?>/js/jasmid/stream"],
				"jasmid-MidiFile": ["<?= DOMAIN ?>/js/jasmid/midifile"]
			}
		});
	</script>
</head>

<body <?= $nav_tab == 'player' ? "class='editor-body'" : "body-index" ?> >
	<x-webmidirequestaccess input="true" output="true"></x-webmidirequestaccess>

	<div class="row">
		<?
			//if($nav_tab != 'player') {
				require_once(ROOT . '/mixins/navbar.php');
				mixin_navbar($nav_tab);
			//} else {
			//	require_once(ROOT . '/mixins/player_navbar.php');
			//	mixin_player_navbar();
			//}
		?>
	</div>

	<?
	if ($extra_msg) {
		?>
		<div class="container alert <?= $extra_msg_type ?: '' ?>" style="margin-top: 150px">
			<?= $extra_msg ?>
		</div>
		<?
		if ($extra_msg_exit) exit;
	}
}

<?php

require_once('init.php');

require(ROOT . '/mixins/header.php');
mixin_header('Midi Piano Online', 'player', ['midikeyboard.css']);
?>

<div class="piano-keyboard piano-keyboard-bottom">

</div>

<script>
	$document.ready(function() {
		$("html").css({
			overflowX: 'hidden',
			overflowY: 'hidden'
		});
		var $progressBar = $("#playerProgressBar");
		$progressBar.slider();
		require(['MidiController', 'WebAudioChannel', 'MidiView', 'MidiData'],
			function(MidiController, WebAudioChannel, MidiView, MidiData) {
				var $keyboard = $(".piano-keyboard");
				var keyboardObj = MidiView($keyboard);
				keyboardObj.render();
				var controller = new MidiController(keyboardObj, WebAudioChannel);
				$(window).resize(function() {
					controller.pause();
				});
				$(window).resize(debouncer(function() {
					controller.play();
				}));
				controller.$this.on('evt_load', function() {
					$progressBar.slider('option', {
						min: 0,
						max: this.totalTicks,
						value: 0
					});
				});
				controller.$this.on('evt_play:before', function() {
					$progressBar.slider('option', {
						value: this.tick
					});
				});
				$progressBar.on('slidestart', function() {
					controller.pause();
				});
				$progressBar.on('slide', function() {
					var tick = $progressBar.slider('option', 'value');
					controller.sliding(tick);
				});
				$progressBar.on('slidestop', function() {
					var tick = $progressBar.slider('option', 'value');
					controller.setCursor(tick);
					controller.play();
				});
				MidiData.loadRemoteMidi('/midiPianoOnline/attachments/aLIEz.mid', function(midiDataObj) {
					controller.load(midiDataObj);
					controller.play();
				});
			}
		);
	});
</script>

<?php
require(ROOT . '/mixins/footer.php');
mixin_footer(['jasmid/midifile.js', 'jasmid/stream.js']);
?>
<?php

require_once('init.php');

require(ROOT . '/mixins/header.php');
mixin_header('Midi Piano Online', 'player', ['midikeyboard.css']);
?>

<div class="piano-keyboard piano-keyboard-bottom">

</div>

<script>
	var app = angular.module('playerProgressBar', []);
	app.controller('playerProgressBarController', function($scope) {
		$document.ready(function() {
			require(['MidiController', 'WebAudioChannel', 'MidiView', 'MidiData'],
				function(MidiController, WebAudioChannel, MidiView, MidiData) {
					$("html").css({
						overflowX: 'hidden',
						overflowY: 'hidden'
					});
					var $keyboard = $(".piano-keyboard");
					$scope.keyboardObj = MidiView($keyboard);
					$scope.keyboardObj.render();
					$scope.controller = new MidiController($scope.keyboardObj, WebAudioChannel);
					MidiData.loadRemoteMidi('/midiPianoOnline/attachments/aLIEz.mid', function(midiDataObj) {
						$scope.controller.load(midiDataObj);
						$scope.controller.play();
						// var replayerObj = Replayer(midiFileObj, Synth(44100));
						// var audio = AudioPlayer(replayerObj);
					});
				}
			);
		});
	});
</script>

<?php
require(ROOT . '/mixins/footer.php');
mixin_footer(['jasmid/midifile.js', 'jasmid/stream.js']);
?>
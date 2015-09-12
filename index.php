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
			$("html").css({
				overflowX: 'hidden',
				overflowY: 'hidden'
			});
			var $keyboard = $(".piano-keyboard");
			$scope.keyboardObj = MidiKeyBoard($keyboard);
			$scope.keyboardObj.render();
			$scope.controller = new MidiNoteBarController($scope.keyboardObj, WebAudioChannel);
			MidiData.loadRemoteMidi('/midiPianoOnline/attachments/aLIEz.mid', function(midiDataObj) {
				$scope.controller.load(midiDataObj);
				$scope.controller.play();
				// var replayerObj = Replayer(midiFileObj, Synth(44100));
				// var audio = AudioPlayer(replayerObj);
			});
		});
	});
</script>

<?php
require(ROOT . '/mixins/footer.php');
mixin_footer(['midikeyboard.js', 'mididata.js', 'midinotebarcontroller.js', 'webaudiosynth.js', 'webaudiopianonode.js',
	'webaudiocontroller.js', 'webaudiochannel.js', 'jasmid/midifile.js', 'jasmid/stream.js']);
?>
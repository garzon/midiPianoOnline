<?php

require_once('init.php');

require(ROOT . '/mixins/header.php');
mixin_header('Midi Piano Online', 0, ['midikeyboard.css']);
?>

<div class="piano-keyboard piano-keyboard-bottom">

</div>

<script>
	$document.ready(function() {
		var $keyboard = $(".piano-keyboard");
		var keyboardObj = MidiKeyBoard($keyboard);
		keyboardObj.render();
		loadRemoteBinary('/midiPianoOnline/attachments/aLIEz.mid', function(raw_data) {
			var midiFileObj = MidiFileReader(raw_data);
			var controller = new MidiNoteBarController(MidiChannel);
			controller.open(midiFileObj);
			controller.play();
			// var replayerObj = Replayer(midiFileObj, Synth(44100));
			// var audio = AudioPlayer(replayerObj);
		});
	});
</script>

<?php
require(ROOT . '/mixins/footer.php');
mixin_footer(['midikeyboard.js', 'midifilereader.js', 'midinotebarcontroller.js', 'midioutput.js', 'midichannel.js', 'jasmid/midifile.js', 'jasmid/stream.js']);
?>
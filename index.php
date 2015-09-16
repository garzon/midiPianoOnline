<?php

require_once('init.php');

require(ROOT . '/mixins/header.php');
mixin_header('Midi Piano Online', 'player', ['midikeyboard.css']);
?>


<div class="dialog" data-title="test">
	<div>MIDI Input:<x-webmidiinput id="x-webmidi-input" autoreselect="true"></x-webmidiinput></div>
	<div>MIDI Output:<x-webmidioutput id="x-webmidi-output" autoreselect="true"></x-webmidioutput></div>
</div>

<div class="piano-keyboard piano-keyboard-bottom"></div>

<script>
	$document.ready(function() {
		$("html").css({
			overflowX: 'hidden',
			overflowY: 'hidden'
		});
		var $progressBar = $("#playerProgressBar");
		$progressBar.slider();
		require(['MidiController', 'WebAudioInstructmentNode', 'WebMidiInstructmentNode', 'MidiView', 'MidiData', 'jasmid-MidiFile', 'jasmid-Stream'],
			function(MidiController, WebAudioInstructmentNode, WebMidiInstructmentNode, MidiView, MidiData, MidiFile, Stream) {
				var $keyboard = $(".piano-keyboard");
				var keyboardObj = MidiView($keyboard);
				keyboardObj.render();
				var controller = new MidiController(keyboardObj);
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
					//var data = midiDataObj.save();
					//$.post('/midiPianoOnline/uploader.php', {data: hexEncode(data)}, function() {});
					controller.load(midiDataObj);
					controller.play();
				});
				window.addEventListener('midiin-event:x-webmidi-input', function(e) {
					var data = Stream(asciiArray2Binary([0x60].concat(Array.from(e.detail.data))));
					var event = MidiFile().readEvent(data);
					controller.handleEvent(event, true);
				});
				var midi_output = $("#x-webmidi-output").get(0);
				WebMidiInstructmentNode.midi_output = midi_output;
				window.addEventListener('midioutput-updated:x-webmidi-output', function(event) {
					console.log(event);
					if(event.target.outputIdx != "false") {
						// have chosen a MIDI Output Device
						console.log('midi output');
						controller.setInstructmentSet(WebMidiInstructmentNode.instructmentSet);
					} else {
						console.log('audio output');
						controller.setInstructmentSet(WebAudioInstructmentNode.instructmentSet);
					}
				});
				controller.setInstructmentSet(WebAudioInstructmentNode.instructmentSet);
			}
		);
	});
</script>

<?php
require(ROOT . '/mixins/footer.php');
mixin_footer([]);
?>
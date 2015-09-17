<?php

require_once('init.php');

require(ROOT . '/mixins/header.php');
mixin_header('Midi Piano Online', 'player', ['midikeyboard.css']);
?>


<div class="dialog" data-title="Devices">
	<div>MIDI Input:<x-webmidiinput id="x-webmidi-input" autoreselect="true"></x-webmidiinput></div>
	<div>MIDI Output:<x-webmidioutput id="x-webmidi-output" autoreselect="true"></x-webmidioutput></div>
</div>

<div class="dialog" data-title="Controller" ng-app="player" ng-controller="playerController">
	<div class="{{loading ? 'hidden' : ''}}">
		<span class="controller-button controller-button-backward" ng-click="backwardOnClick()">
			<i class="glyphicon glyphicon-step-backward"></i>
		</span>
		<span class="controller-button {{playing ? 'controller-button-pause' : 'controller-button-play'}}" ng-click="playOnClick()">
			<i class="glyphicon glyphicon-play"></i>
		</span>
		<span class="controller-button controller-button-record {{recording ? 'red' : ''}}" ng-click="recordOnClick()">
			<i class="glyphicon glyphicon-record"></i>
		</span>
		<span class="controller-button controller-button-upload" ng-click="uploadOnClick()">
			<i class="glyphicon glyphicon-floppy-open"></i>
		</span>
		<div class="progress">
			<div id="playerProgressBar"></div>
		</div>
	</div>
	<div class="{{loading ? '' : 'hidden'}}">
		<p>Loading...</p>
	</div>
</div>

<div class="piano-keyboard piano-keyboard-bottom"></div>

<script>
	var playerApp = angular.module('player', []);
	playerApp.controller('playerController', function($scope) {
		require(['MidiController', 'WebAudioInstructmentNode', 'WebMidiInstructmentNode', 'MidiView', 'MidiData', 'jasmid-MidiFile', 'jasmid-Stream', 'OutputStream'],
		function(MidiController, WebAudioInstructmentNode, WebMidiInstructmentNode, MidiView, MidiData, MidiFile, Stream, OutputStream) {
			var $keyboard = $(".piano-keyboard");
			var keyboardObj = MidiView($keyboard);
			var controller = new MidiController(keyboardObj);

			$scope.loading = true;
			$scope.playing = false;
			$scope.recording = false;

			$scope.playOnClick = function() {
				if($scope.playing) {
					$scope.playing = false;
					controller.pause();
				} else {
					$scope.playing = true;
					controller.play();
				}
			};

			$scope.recordOnClick = function() {
				if(!$scope.recording) {
					$scope.recording = true;
					controller.record();
				} else {
					$scope.recording = false;
					controller.stopRecord();
				}
			};

			$scope.backwardOnClick = function() {
				controller.resetCursor();
			};

			$scope.uploadOnClick = function() {};

			$document.ready(function() {
				$("html").css({
					overflowX: 'hidden',
					overflowY: 'hidden'
				});

				var $progressBar = $("#playerProgressBar");
				$progressBar.slider();
				keyboardObj.render();

				MidiData.loadRemoteMidi('/midiPianoOnline/attachments/aLIEz.mid', function(midiDataObj) {
					controller.load(midiDataObj);
					$scope.loading = false;
				});
				controller.setInstructmentSet(WebAudioInstructmentNode.instructmentSet);

				$(window).resize(function() {
					controller.pause();
				}).resize(debouncer(function() {
					controller.play();
				}));

				controller.$this.on('evt_load', function() {
					$progressBar.slider('option', {
						min: 0,
						max: this.totalTicks,
						value: 0
					});
				}).on('evt_play:before', function() {
					$progressBar.slider('option', {
						value: this.tick
					});
				});

				$progressBar.on('slidestart', function() {
					controller.pause();
				}).on('slide', function() {
					var tick = $progressBar.slider('option', 'value');
					controller.sliding(tick);
				}).on('slidestop', function() {
					var tick = $progressBar.slider('option', 'value');
					controller.setCursor(tick);
					controller.play();
				});

				keyboardObj.$this.on('MidiView:mousedown', function(e, note) {
					var data = Stream(asciiArray2Binary([0x60, 0x90+controller.currentChannel, note, 0x80]));
					var event = MidiFile().readEvent(data);
					controller.handleEvent(event, true);
				}).on('MidiView:mouseup', function(e, note) {
					var data = Stream(asciiArray2Binary([0x60, 0x80+controller.currentChannel, note, 0x80]));
					var event = MidiFile().readEvent(data);
					controller.handleEvent(event, true);
				});

				window.addEventListener('midiin-event:x-webmidi-input', function(e) {
					var data = Stream(asciiArray2Binary([0x60].concat(Array.from(e.detail.data))));
					var event = MidiFile().readEvent(data);
					controller.handleEvent(event, true);
				});
				WebMidiInstructmentNode.midi_output = $("#x-webmidi-output").get(0);
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
				$scope.uploadOnClick = function() {
					var data = controller.getRaw();
					$.post('/midiPianoOnline/uploader.php', {data: hexEncode(data)}, function() {
						alert('succcessfully uploaded!')
					});
				};
			});
		});
	});
</script>

<?php
require(ROOT . '/mixins/footer.php');
mixin_footer([]);
?>
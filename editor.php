<?php

require_once('init.php');

require(ROOT . '/mixins/header.php');
mixin_header('Midi Piano Online', 'player', ['midikeyboard.css']);
?>


<div class="dialog" data-title="Devices">
	<wm-pckeyboard id="pckeyboard"></wm-pckeyboard>
	<div>MIDI Input:<x-webmidiinput id="x-webmidi-input" additionalid="pckeyboard" autoselect="PC-Keyboard" autoreselect="true"></x-webmidiinput></div>
	<div>MIDI Output:<x-webmidioutput id="x-webmidi-output" autoreselect="true"></x-webmidioutput></div>
</div>

<div class="dialog" data-title="Controller" ng-app="player" ng-controller="playerController">
	<div class="{{loading ? 'hidden' : ''}}">
		<span class="controller-button controller-button-backward" ng-click="backwardOnClick()">
			<i class="glyphicon glyphicon-step-backward"></i>
		</span>
		<span class="controller-button" ng-class="playing ? 'controller-button-pause' : 'controller-button-play'" ng-click="playOnClick()">
			<i class="glyphicon" ng-class="playing ? 'glyphicon-pause' : 'glyphicon-play'"></i>
		</span>
		<span class="controller-button controller-button-record"  ng-class="controller.recording ? 'red' : ''" ng-click="recordOnClick()">
			<i class="glyphicon glyphicon-record"></i>
		</span>
		<span class="controller-button controller-button-upload" ng-click="uploadOnClick()">
			<i class="glyphicon glyphicon-floppy-open"></i>
		</span>
		<span class="controller-button controller-button-new" ng-click="newOnClick()">
			<i class="glyphicon glyphicon-file"></i>
		</span>
		<span>
			{{msToTimeString(controller.time)}}/{{msToTimeString(controller.totalTime)}}
		</span>
		<div class="progress">
			<div id="playerProgressBar"></div>
		</div>
		<div>
			<a href="#" class="inline-block btn" ng-class="controller.mode == 'playing' ? 'btn-success' : 'btn-primary'" ng-click="switchMode()">
				{{controller.mode == 'playing' ? 'Play Mode' : 'Edit Mode'}}
			</a>
			<span ng-show="controller.mode == 'playing'">
				Score: {{controller.score}}
			</span>
			<span>
				Tempo: {{controller.beatsPerMinute > 256 ? 0 : round(controller.beatsPerMinute)}}
			</span>
		</div>
	</div>
	<div class="{{loading ? '' : 'hidden'}}">
		<p>Loading... Please wait.</p>
	</div>
</div>

<div class="piano-bar-container"></div>
<div class="piano-keyboard piano-keyboard-bottom"></div>

<script>
	var midiUrl = '<?= $data->midiUrl ?>';
</script>

<script>
	var playerApp = angular.module('player', []);
	playerApp.controller('playerController', function($scope) {
		$scope.loading = true;
		$scope.playing = false;

		$scope.round = Math.round;

		$scope.msToTimeString = function(ms) {
			var s = Math.round(ms / 1000);
			var min = String(Math.floor(s/60));
			if(min.length < 2) min = '0' + min;
			s = String(s % 60);
			if(s.length < 2) s = '0' + s;
			return min + ':' + s;
		};

		require(['WebAudioFluidPianoNode'], function(WebAudioFluidPianoNode) {

		require(['MidiController', 'WebAudioInstructmentNode', 'WebMidiInstructmentNode', 'MidiView', 'MidiData', 'jasmid-MidiFile', 'jasmid-Stream'],
		function(MidiController, WebAudioInstructmentNode, WebMidiInstructmentNode, MidiView, MidiData, MidiFile, Stream) {
			var $keyboard = $(".piano-keyboard");
			var keyboardObj = MidiView($keyboard, $(".piano-bar-container"));
			var controller = new MidiController(keyboardObj);

			$scope.controller = controller;

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
				if(!$scope.controller.recording) {
					controller.record();
				} else {
					controller.stopRecord();
				}
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

				$scope.newOnClick = function() {
					if(!confirm('Discard all changes?')) return;
					controller.pause();
					MidiData.loadRemoteMidi(midiUrl, function(midiDataObj) {
						controller.load(midiDataObj);
						controller.setEditingMode();
						$scope.loading = false;
						$scope.$apply();
					}, function() {
						alert('Error! Please refresh this page.')
					});
				};
				MidiData.loadRemoteMidi(midiUrl, function(midiDataObj) {
					controller.load(midiDataObj);
					$scope.loading = false;
					$scope.$apply();
				});

				$scope.switchMode = function() {
					if(controller.mode == 'playing')
						controller.setEditingMode();
					else
						controller.setPlayMode();
					controller.refreshBarView();
				};

				controller.setInstructmentSet(WebAudioInstructmentNode.instructmentSet);

				$(window).resize(function() {
					controller.pause();
				}).resize(debouncer(function() {
					if($scope.playing) controller.play();
				}));

				controller.$this.on('evt_load', function() {
					$progressBar.slider('option', {
						min: 0,
						max: controller.totalTicks,
						value: controller.tick,
						step: 240
					});
				}).on('evt_play:before', function() {
					$progressBar.slider('option', {
						value: this.tick
					});
					if(!$scope.$$phase) $scope.$apply();
				}).on('evt_autopause', function() {
					if($scope.playing) {
						$scope.playing = false;
						controller.pause();
					}
					if(!$scope.$$phase) $scope.$apply();
				});

				$scope.backwardOnClick = function() {
					controller.resetCursor();
					$progressBar.slider({value: 0});
				};

				$scope.uploadOnClick = function() {
					var data = controller.getRaw();
					$.post('<?= DOMAIN ?>/api/uploader.php?id=<?= Util::getInt('id', 0) ?>', {data: hexEncode(data)}, function(js) {
						eval(js);
					}, 'text');
				};

				$progressBar.on('slidestart', function() {
					controller.pause();
				}).on('slide', function() {
					var tick = $progressBar.slider('option', 'value');
					controller.sliding(tick);
					$scope.$apply();
				}).on('slidestop', function() {
					var tick = $progressBar.slider('option', 'value');
					controller.setCursor(tick);
					if($scope.playing) controller.play();
					$scope.$apply();
				});

				keyboardObj.$this.on('MidiView:mousedown', function(e, note) {
					var data = Stream(asciiArray2Binary([0x60, 0x90+controller.currentChannel, note, 0x60]));
					var event = MidiFile().readEvent(data);
					controller.handleEvent(event, true);
				}).on('MidiView:mouseup', function(e, note) {
					var data = Stream(asciiArray2Binary([0x60, 0x80+controller.currentChannel, note, 0x60]));
					var event = MidiFile().readEvent(data);
					controller.handleEvent(event, true);
				}).on('MidiView:drag-start', function() {
					controller.model_pause();
				}).on('MidiView:dragged', function(e, info) {
					controller.removeEvent(info.old_trackId, info.old_absoluteTicks, info.old_note, info.old_channel);
					controller.removeEvent(info.old_trackId, info.old_absoluteTicks+info.old_lastTime, info.old_note, info.old_channel);
					var curr_track = controller.insertNoteOnEvent(controller.currentChannel, info.absoluteTicks, info.note, info.volume);
					controller.insertNoteOffEvent(controller.currentChannel, info.absoluteTicks+info.lastTime, info.note);
					info.$ele.data('trackId', curr_track);
					if($scope.playing) controller.play();
				});

				window.addEventListener('midiin-event:x-webmidi-input', function(e) {
					var data = Stream(asciiArray2Binary([0x60].concat(Array.from(e.detail.data))));
					var event = MidiFile().readEvent(data);
					controller.handleEvent(event, true);
				});
				WebMidiInstructmentNode.midi_output = $("#x-webmidi-output").get(0);
				window.addEventListener('midioutput-updated:x-webmidi-output', function(event) {
					if(event.target.outputIdx != "false") {
						// have chosen a MIDI Output Device
						console.log('midi output');
						controller.setInstructmentSet(WebMidiInstructmentNode.instructmentSet);
					} else {
						console.log('audio output');
						controller.setInstructmentSet(WebAudioInstructmentNode.instructmentSet);
					}
				});

				window.addEventListener('keydown', function(e) {
					switch(e.keyCode) {
						case 46: // delete key
							$(".piano-bar-selected").each(function() {
								var $ele = $(this);
								var old_absoluteTicks = $ele.data('absoluteTicks');
								var old_channel = $ele.data('channel');
								var old_note = $ele.data('note');
								var old_lastTime = $ele.data('lastTime');
								var old_trackId = $ele.data('trackId');

								controller.removeEvent(old_trackId, old_absoluteTicks, old_note, old_channel);
								controller.removeEvent(old_trackId, old_absoluteTicks + old_lastTime, old_note, old_channel);

								$ele.remove();
							});
							break;

					}
				});

			});
		});

		});
	});
</script>
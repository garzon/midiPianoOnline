<?php

function mixin_player_navbar() {
	?>
	<nav class="navbar navbar-default" role="navigation">
		<div class="container-fluid">
			<div class="progress">
				<div id="playerProgressBar"></div>
			</div>
			<x-webmidiinput id="x-webmidi-input" autoreselect="true"></x-webmidiinput>
			<x-webmidioutput id="x-webmidi-output" autoreselect="true"></x-webmidioutput>
		</div>
	</nav>
<?
}
<?php

function mixin_player_navbar() {
	?>
	<nav class="navbar navbar-default" role="navigation">
		<div class="container-fluid">
			<div class="progress" ng-app="playerProgressBar" ng-controller="playerProgressBarController">
				<div class="progress-bar" role="progressbar" aria-valuenow="{{ controller.tick }}" aria-valuemin="0" aria-valuemax="{{ controller.totalTicks }}">
					{{ controller.tick }}
				</div>
			</div>
		</div>
	</nav>
<?
}
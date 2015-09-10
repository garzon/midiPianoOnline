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
	<script src="<?= DOMAIN ?>/js/util.js"></script>

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
</head>

<body>
	<div class="row">
		<nav class="navbar navbar-default" role="navigation">
			<div class="container-fluid">
				...
			</div>
		</nav>
	</div>

	<?
	if ($extra_msg) {
		?>
		<div class="main-block container alert <?= $extra_msg_type ?: '' ?>">
			<?= $extra_msg ?>
		</div>
		<?
		if ($extra_msg_exit) exit;
	}
}

?>
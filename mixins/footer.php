<?php

function mixin_footer($jsPatharray = []) {
	global $global_timer;
	?>
		<nav class="navbar navbar-default navbar-bottom">
			<div class="container">
				<div class="row">
					<div class="navbar-header col-md-2 col-sm-2 col-xs-12">
						<a href="https://github.com/sixstars" target="_blank"><img class="navbar-footer-logo" src="<?= DOMAIN ?>/img/footer-logo.png" /></a>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-8 grey">
						<p>Midi Piano Online</p>
						<p class="small">Copyright. garzon</p>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-4 grey">
						<p><?= date("Y-m-d H:i:s", time()) ?></p>
						<p class="small"><?= (microtime()-$global_timer)*1000 ?>ms</p>
					</div>
				</div>
			</div>
		</nav>
	<?
	foreach ($jsPatharray as $jsFile) {
		?>
<script src="<?= DOMAIN ?>/js/<?= $jsFile ?>"></script>
<?
	}
	?>
</body>
</html>
	<?
}
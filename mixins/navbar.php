<?php

function mixin_navbar($nav_tab) {
	$playerFlag = false;
	switch($nav_tab) {
		case 'index':
			$nav_tab = 0;
			break;
		case 'user2':
			$nav_tab = 1;
			break;
		case 'help':
			$nav_tab = 2;
			break;
		case 'player':
			$playerFlag = true;
		default:
			$nav_tab = null;
	}
	?>
		<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
			<div class="container-fluid">
				<div class="navbar-header col-md-2 col-sm-2 col-xs-12">
					<a href="<?= DOMAIN ?>/index.php"><img class="navbar-header-logo" src="<?= DOMAIN . '/img/logo.png' ?>" /></a>
				</div>

				<span class="col-md-5 col-sm-5 col-xs-8">
					<paper-tabs noink <?= ($nav_tab !== null) ? "selected=\"{$nav_tab}\"" : "" ?> class="blue col-md-9 col-xs-12">
						<paper-tab link><a href="<?= DOMAIN ?>/index.php" class="horizontal center-center layout">Discover</a></paper-tab>
						<?
						/*<paper-tab link><a href="<?= DOMAIN ?>/users.php" class="horizontal center-center layout">Users</a></paper-tab>*/
						if(Visitor::user()) {
						?>
						<paper-tab link><a href="<?= DOMAIN ?>/user.php?id=<?= Visitor::user()->id?>" class="horizontal center-center layout">Profile</a></paper-tab>
						<? } ?>
						<paper-tab link><a href="<?= DOMAIN ?>/help.php" class="horizontal center-center layout">Help</a></paper-tab>
					</paper-tabs>
				</span>

				<span class="pull-right">
					<?php
						if($playerFlag) {
							$midi = MidiFile::fetch(Util::getInt('id', 0));
							if($midi && $midi->checkHiddenPermission(Visitor::user())) {
								?>
								<a href="<?= DOMAIN ?>/view.php?id=<?= Util::getInt('id', 0) ?>" class="btn btn-sm btn-default font-black">Go Back</a>
								<?
							}
						}
					?>
					<?php if(Visitor::user()) {?>
						<a href="<?= DOMAIN ?>/upload.php" class="btn btn-sm btn-primary font-black">Upload Midi</a>
						<a href="<?= DOMAIN ?>/editor.php" class="btn btn-sm btn-success font-black">Create</a>
						<a href="<?= DOMAIN ?>/login.php" class="btn btn-sm btn-default font-black">Logout</a>
					<?php } else { ?>
						<a href="<?= DOMAIN ?>/login.php" class="btn btn-sm btn-primary font-black">Login/Register</a>
					<?php }?>
				</span>

			</div>
		</nav>
	<?
}
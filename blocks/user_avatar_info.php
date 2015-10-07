<?php
// 610204679@qq.com

Util::checkEntry();

?>

<div class="row userinfobox">
	<? /*<div class="col-md-1">
		<img src="<?= $user->getImageUrl() ?>" class="middle-avatar" />
	</div>*/ ?>
	<div class="col-md-4">
		<p class="userinfo">
			<a class="black" href="<?= DOMAIN ?>/user.php?id=<?= $user->id ?>"><?= htmlentities($user->name) ?></a>
			<?php
				if (Visitor::user() && Visitor::user()->id == $user->id) {
					?>
						<a class="blue" href="<?= DOMAIN ?>/profile.php">Edit profile</a>
					<?
				}
			?>
		</p>
		<h5 class="userinfo">
			<small>
				<span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span>
				<?= htmlentities($user->location) ?>
			</small>
		</h5>
		<h5 class="userinfo">
			<small class="grey">
				<?= $user->getScore() ?> points · <?= $user->uploadedCounter ?> uploaded
			</small>
		</h5>
	</div>
	<div class="col-md-8">
		<p>
			Self introduction:<br />
			<?= nl2br(htmlentities($user->introduction)) ?>
		</p>
	</div>
</div>

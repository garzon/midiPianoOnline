<?php
// 610204679@qq.com

function mixin_viewad_sidebar_avatar(User $user, $extraMsg) {
	?>
		<hr />
		<div class="row resume-userinfo">
			<div class="col-md-9">
				<h4>
					<a class="blue" href="<?= DOMAIN ?>/user.php?id=<?= $user->id ?>"><?= htmlentities($user->name) ?></a>
				</h4>
				<h5 class="grey">
					<?= $extraMsg ?>
				</h5>
			</div>
		</div>
		<hr />
	<?
}
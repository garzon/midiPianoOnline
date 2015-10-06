<?php
// 610204679@qq.com

Util::checkEntry();

?>

<div class="row userinfobox">
	<div class="col-md-1">
		<img src="<?= $user->getImageUrl() ?>" class="middle-avatar" />
	</div>
	<div class="col-md-11">
		<p class="userinfo">
			<a class="black" href="<?= DOMAIN ?>/user.php?id=<?= $user->id ?>"><?= htmlentities($user->name) ?></a>
			<?php
				if (Visitor::user()->id == $user->id) {
					?>
						<a class="blue" href="<?= DOMAIN ?>/profile.php">编辑资料</a>
					<?
				}
			?>
		</p>
		<h5 class="userinfo">
			<small>
				<span class="glyphicon glyphicon-phone" aria-hidden="true"></span>
				QQ:<?= htmlentities($user->qq) ?> | 微信：<?= htmlentities($user->chatNum) ?>
			</small>
		</h5>
		<h5 class="userinfo">
			<small>
				<span class="glyphicon glyphicon-phone-alt" aria-hidden="true"></span>
				<?= htmlentities($user->phone) ?>
			</small>
		</h5>
	</div>
</div>

<div class="row userinfobox">
	<p class="userextrainfo">
		<small class="grey">
			积分：<?= $user->money ?> · <?= $user->solvedCount ?>解出 · 发布<?= $user->fabuCount ?>个题目
		</small>
	</p>
</div>
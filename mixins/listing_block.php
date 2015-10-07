<?php
// 610204679@qq.com

function mixin_listing_block(MidiFile $midi, $operatorCallback) {
	$user = User::fetch($midi->userId);
	$tag = '';

	$title = $midi->name;
	if ($midi->category) $title = "[{$midi->category}] " . $title;
	?>
	<blockquote class="listing-block">
		<div class="col-md-7 listing-block-info">
			<h4 class="inline-block"><a class="black" href="<?= DOMAIN ?>/view.php?id=<?= $midi->id ?>"><?= htmlentities($title) ?></a></h4>
			<p><?= nl2br(htmlentities(mb_substr($midi->introduction, 0, 50) . '...')) ?></p>
			<p><span class="mySmall"><a href="<?= DOMAIN ?>/user.php?id=<?= $midi->userId ?>"><?= htmlentities($user->name) ?></a> uploaded at <?= date('Y-m-d H:i', $midi->createdTime) ?></span></p>
		</div>
		<? $operatorCallback($midi); ?>
	</blockquote>
	<?
}
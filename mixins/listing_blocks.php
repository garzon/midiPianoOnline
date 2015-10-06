<?php
// 610204679@qq.com

require_once('listing_block.php');

$mixin_listing_blocks_default_callback = function(Problem $resume) {
	?>
	<div class="col-md-2 listing-block-sidebar">
		<span><?= $resume->solvedCount ?>人解出 / <?= $resume->viewCount ?>人浏览</span>
	</div>
	<div class="col-md-3 listing-block-sidebar">
		<span class="red"><?= $resume->prize ?>积分</span>
		<span style="margin-left: 40px"><a href="<?= DOMAIN ?>/problem.php?id=<?= $resume->id ?>" target="_blank">查看详情</a></span>
	</div>
	<?
};

$mixin_listing_blocks = function($midis, $operatorCallback = null) use ($mixin_listing_blocks_default_callback) {
	if (!$operatorCallback) {
		$operatorCallback = $mixin_listing_blocks_default_callback;
	}

	foreach ($midis as $midi) {
		mixin_listing_block($midi, $operatorCallback);
	}

	if (count($midis) == 0) { ?>
		<blockquote class="listing-block">
			<p>没有符合条件的结果</p>
		</blockquote>
	<?php }
};
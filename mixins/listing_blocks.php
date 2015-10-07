<?php
// 610204679@qq.com

require_once('listing_block.php');

$mixin_listing_blocks_default_callback = function(MidiFile $midi) {
	?>
	<div class="col-md-2 listing-block-sidebar">
		<span><?= count($midi->viewList) ?> Browsered</span>
	</div>
	<div class="col-md-3 listing-block-sidebar">
		<span class="red"><?= $midi->price ?> Points</span>
		<span style="margin-left: 40px"><a href="<?= DOMAIN ?>/view.php?id=<?= $midi->id ?>" target="_blank">View</a></span>
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
			<p>Not Found</p>
		</blockquote>
	<?php }
};
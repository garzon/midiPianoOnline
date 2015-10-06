<?php
// 610204679@qq.com

require_once('./init.php');

$pageTitle = 'Midi Piano Online';
require_once(ROOT . '/mixins/header.php');
mixin_header($pageTitle, 'index', [], $data->extra_msg, $data->extra_msg_type);
?>


	<div class="container main-block">

		<?php include(ROOT . "/blocks/filter.php"); ?>
		<?php require_once(ROOT . "/mixins/listing_blocks.php"); ?>
		<?php

		$mixin_listing_blocks($data->midis);
		?>
		<?php
		require_once(ROOT . '/mixins/pager.php');
		mixin_pager($data->totalCount, Util::currentPage());
		?>
	</div>


<?php
require_once(ROOT . '/mixins/footer.php');
mixin_footer();
?>
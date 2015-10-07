<?php
// 610204679@qq.com
require_once('./init.php');

require_once(ROOT . '/mixins/header.php');
mixin_header('User info', 'user' . $data->nav_tab, [], $data->extra_msg, $data->extra_msg_type);

$user = $data->user;

?>
<div class="main-block container block-page" style="padding-top: 20px">
	<? require(ROOT . '/blocks/user_avatar_info.php'); ?>
	<div class="col-md-12">
		<ul class="nav nav-tabs nav-userpage">
			<li role="presentation" class="active"><a href="#">MIDI Repository</a></li>
			<? /*<li role="presentation"><a href="<?= DOMAIN ?>/userFabu.php?id=<?= $user->id ?>">发布的题目</a></li> */ ?>
		</ul>
		<? require(ROOT . '/blocks/filter.php'); ?>
	</div>
	<div class="col-md-12">
		<?
		require_once(ROOT . '/mixins/listing_blocks.php');
		$mixin_listing_blocks($data->midis);
		?>
	</div>
</div>

<?php
require_once(ROOT . '/mixins/footer.php');
mixin_footer();
?>

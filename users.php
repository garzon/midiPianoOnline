<?php
//81326073@qq.com

die('under construction.');

$nav_tab = 1;

require_once('./init.php');
Visitor::checkLogin();


$pageTitle = '排行榜';
require_once('./header.php');
?>
<body class="body-index">
<div class="main-block container">
	<?php
		$positions = User::find(['isSuperman' => false], ['limit'=> 100,  'sort' => ['money' => -1, 'createdTime' => -1]]);
	?>
	<div class="col-md-12 block-page">
		<?php
			require_once(ROOT . '/mixins/listing_positions.php');
			$mixin_listing_positions($positions);
		?>
	</div>
</div>
</body>

<?php
require_once(ROOT . '/mixins/footer.php');
mixin_footer();
?>

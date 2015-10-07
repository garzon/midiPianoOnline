<?php
// 610204679@qq.com

$nav_tab = 1;

require_once('./init.php');

$pageTitle = 'Help';
require_once(ROOT . '/mixins/header.php');
mixin_header($pageTitle, 'view', [], $data->extra_msg, $data->extra_msg_type);
?>

<body class="body-index">
	<div class="main-block container">
		<div class="col-md-12 block-page">

		</div>
	</div>
</body>

<?php
require_once(ROOT . '/mixins/footer.php');
mixin_footer();
?>

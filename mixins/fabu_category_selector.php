<?php
// 610204679@qq.com

$mixin_fabu_category_selector = function ($defaultVal = null, $name = 'category') {
	$categories = Util::$categories;
	if ($defaultVal) $defaultVal = htmlentities($defaultVal, ENT_QUOTES);
	?>
	<input class="form-control form-category-selector myHidden" name='<?= $name ?>' <?= $defaultVal ? '' : 'required="required"' ?> type="text" <?= $defaultVal ? "value='$defaultVal'" : '' ?> />
	<div class="btn-group">
		<button type="button" class="btn btn-default dropdown-toggle fabu-form-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<?= $defaultVal ?: 'Please select a category' ?> <span class="caret"></span>
		</button>
		<ul class="dropdown-menu">
			<? foreach ($categories as $id => $cat) {
				if ($id == 0) continue;
				?>
				<li><a href="#" class="fabu-form-category" data-category="<?= $id ?>"><?= $cat ?></a></li>
			<? } ?>
		</ul>
	</div>
	<?
};
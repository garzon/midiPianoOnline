<?php
// 610204679@qq.com

function mixin_pager($totalCount, $currentPage = 1, $numPerPage = 20) {
	$totalPage = ceil($totalCount * 1.0 / $numPerPage - 0.000001);
	if($totalPage == 0) $totalPage = 1;
	if ($currentPage > $totalPage) $currentPage = $totalPage+1;
	$make_button = function($i, $txt = null) use ($currentPage) {
		if ($txt === null) $txt = $i;
		?>
		<button type="button" class="btn btn-default btn-pager" data-page="<?= $i ?>" <?= $i == $currentPage ? 'disabled' : '' ?>><?= $txt ?></button>
		<?
	};
	?>
	<div class="btn-toolbar pull-right" role="toolbar">
		<div class="btn-group" role="group">
		<?
			$range = [max(1, $currentPage - 4), min($totalPage, $currentPage + 4)];
			$inRange = function ($i) use ($range) { return $range[0] <= $i && $i <= $range[1]; };
			if (!$inRange(1)) $make_button(1);
			if ($currentPage != 1) $make_button($currentPage-1, '<');
			for($i = $range[0]; $i <= $range[1]; $i++ ) {
				$make_button($i);
			}
			if ($currentPage != $totalPage) $make_button($currentPage+1, '>');
			if (!$inRange($totalPage)) $make_button($totalPage);
		?>
		</div>
	</div>
<?
}
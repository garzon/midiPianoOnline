<?php
// 610204679@qq.com

Util::checkEntry();

?>
<div class="row listing-filters">
	<div class="col-md-3">
		<a class="listing-filter" href="#"><span class="caret"></span></a>
		<div class="listing-filter-menu horizontal-section hidden" data-showFlag="0">
			<paper-menu data-prop="category" selected="<?= intval(Util::get('category', 0)) ?>">
				<? foreach (Util::$categories as $id => $cat) {
					?>
					<paper-item data-val="<?= $id ?>"><?= $cat ?></paper-item>
				<? } ?>
			</paper-menu>
		</div>
	</div>
	<div class="col-md-3">
		<a class="listing-filter" href="#"><span class="caret"></span></a>
		<div class="listing-filter-menu horizontal-section hidden" data-showFlag="0">
			<paper-menu data-prop="price" selected="<?= intval(Util::get('price', 0)) ?>">
				<?php
					foreach (Util::$filter_price as $id => $range) {
						if ($id == 0) echo '<paper-item data-val="0">All Rates</paper-item>';
						else {
							?>
								<paper-item data-val="<?= $id ?>"><?= $range[0] ?>~<?= $range[1] ?> Likes</paper-item>
							<?php
						}
					}
				?>
			</paper-menu>
		</div>
	</div>
	<div class="col-md-2 listing-filter">
		<input type="checkbox" data-prop="uploadedOnly" <?= Util::get('uploadedOnly') ? 'checked="checked"' : '' ?> />
		<label>
			No duplicate
		</label>
	</div>
	<?php /*
	<div class="col-md-2 listing-filter">
		<input type="checkbox" data-prop="notSolvedOnly" <?= Util::get('notSolvedOnly') ? 'checked="checked"' : '' ?> />
		<label>
			只看未解出
		</label>
	</div>
	*/ ?>
</div>

<script>
	$document.ready(function() {
		// selector build query logic
		var $item = $(".listing-filters .listing-filter-menu paper-item");
		$item.on('click', function() {
			var query = {page: 1};
			query[$(this.parentNode.parentNode).data('prop')] = $(this).data('val');
			window.location.href = buildNewUrlQueryArray(query);
		});

		// 初始化下拉菜单显示项
		$item.filter('.iron-selected').each(function() {
			var showSpan = $(this).parents('.listing-filters>div').children('a').get(0);
			showSpan.innerHTML = this.innerText + ' ' + showSpan.innerHTML;
		});

		// filter下拉菜单展示和隐藏
		var $listing_filter_a = $(".listing-filters a");
		$listing_filter_a.on('click', function() {
			var $menu = $(this).siblings(".listing-filter-menu");
			setTimeout(function() {
				var showFlag = $menu.data('showFlag');
				if (showFlag) $menu.hide();
				else $menu.show().removeClass('hidden');
				$menu.data('showFlag', !showFlag);
			}, 100);
		});

		var $menu = $(".listing-filter-menu");
		$("body").on('click', function() {
			$menu.each(function() {
				var $this = $(this);
				var showFlag = $this.data('showFlag');
				if (showFlag) {
					$this.data('showFlag', false).hide();
				}
			});
		});

		// checkbox build query logic
		var $listing_filter_checkbox = $(".listing-filters input");
		$listing_filter_checkbox.on('click', function() {
			var val = this.checked ? 1 : 0;
			var prop = $(this).data('prop');
			var query = {page: 1};
			query[prop] = val;
			window.location.href = buildNewUrlQueryArray(query);
		});
	});
</script>
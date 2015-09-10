<?php

function mixin_footer($jsPatharray = []) {
	foreach ($jsPatharray as $jsFile) {
		?>
<script src="<?= DOMAIN ?>/js/<?= $jsFile ?>"></script>
<?
	}
	?>
</body>
</html>
	<?
}
<?php

require_once('init.php');

require(ROOT . '/mixins/header.php');
mixin_header('Midi Piano Online', 0, ['midikeyboard.css']);
?>

<div class="piano-keyboard piano-keyboard-bottom">

</div>

<script>
	$document.ready(function() {
		var $keyboard = $(".piano-keyboard");
		var keyboardObj = MidiKeyBoard($keyboard);
		keyboardObj.render();
	});
</script>

<?php
require(ROOT . '/mixins/footer.php');
mixin_footer(['midikeyboard.js']);
?>
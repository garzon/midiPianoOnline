<?php
//813626073@qq.com
require_once('./init.php');

require_once(ROOT . '/mixins/header.php');
mixin_header($data->pageTitle, 'upload', [], $data->extra_msg, $data->extra_msg_type);

$default_category = $data->default_category;
$default_name = $data->default_name;
$default_comment = $data->default_comment;
$default_price = $data->default_price;

require_once(ROOT . '/mixins/fabu_category_selector.php');
?>
<div class="container main-block block-page">
	<h3><?= $data->pageTitle ?></h3>
	<h4><small>Sharing is great!</small></h4>

	<form method="post" enctype="multipart/form-data" class="form-horizontal" ng-app="" ng-submit="submit()" role="form" action>

		<div class="form-group">
			<div class="row">
				<label for="midiData" class="col-md-1-2 control-label">Upload MIDI：</label>
				<div class="col-md-10 row">
					<div class="col-md-6">
						<span class="btn btn-primary btn-lg btn-file fabu-form-button">
							<span>Not selected</span>
							<input type="file" id="midiData" name='midiData' accept=".mid,.midi" <?= $data->editMidiFlag ? '' : 'required="required"' ?>>
						</span>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group">
			<div class="row">
				<label for="category" class="col-md-1-2 control-label">Category：</label>
				<div class="row col-md-10">
					<div class="col-md-6">
						<? $mixin_fabu_category_selector($default_category); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group">
			<div class="row">
				<label for="name" class="col-md-1-2 control-label">Name：</label>
				<div class="row col-md-10">
					<div class="col-md-6">
						<input class="form-control" id="name" name='name' required="required" type="text" <?= $default_name ? "value='$default_name'" : '' ?> />
					</div>
				</div>
			</div>
		</div>

		<div class="form-group">
			<div class="row">
				<label for="introduction" class="col-md-1-2 control-label">Introduction：</label>
				<div class="row col-md-10">
					<div class="col-md-6">
						<textarea class="form-control" id="introduction" name='introduction'><?= $default_comment ?: '' ?></textarea>
					</div>
				</div>
			</div>
			<div class="col-md-offset-1-2 row">
				<span class="fabu-form-hint grey"></span>
			</div>
		</div>

		<div class="form-group">
			<div class="col-md-offset-2">
				<input type="submit" class="btn btn-success btn-lg fabu-form-button" value="Submit!" />
			</div>
		</div>
	</form>
</div>

<?php
require_once(ROOT . '/mixins/footer.php');
mixin_footer();
?>
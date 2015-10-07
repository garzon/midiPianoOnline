<?php
// 610204679@qq.com

function mixin_viewad_sidebar_operations($commentTips, $shareTips, $obj = null) {
	?>
		<? if(Visitor::isSuperman() || (Visitor::user() && Visitor::user()->id == $obj->userId)) { ?>
			<div id="adminOps" class="row">
				<h3></h3>
				<form method="post" action="" onsubmit="return confirm('Do you really want to delete this midi file?')">
					<input type="hidden" name="post_type" value="manager_control">
					<input type="submit" class="btn btn-danger" value="Delete" />
					<? if ($obj && ($obj instanceof MidiFile)) { ?>
						<a class="btn btn-default" href="<?= DOMAIN ?>/upload.php?editMidiId=<?= $obj->id ?>">Edit MIDI Info</a>
					<? } ?>
				</form>
			</div>
		<? } ?>
		<div id="resume-comment" class="row">
			<h3><?= $commentTips ?></h3>
			<form method="post" action>
				<div class="form-group">
					<textarea class="form-control" rows="6" name="content"></textarea>
				</div>
				<button type="submit" class="btn btn-default">Submit</button>
			</form>
		</div>
		<? /*
	    if ($obj && ($obj instanceof Problem)) { //&& (Visitor::isSuperman() || Visitor::user()->id === $obj->userId)
			$resumeView = ProblemView::find(['problemId' => $obj->id], ['sort' => ['createdTime' => -1]]);
			$resumeDownloads = ProblemSolved::find(['problemId' => $obj->id], ['sort' => ['createdTime' => -1]]);
			?>
			<div id="history-log" class="row">
				<h3>历史状态：</h3>
				<?
					foreach ($resumeDownloads as $download) {
						$user = User::fetch($download->userId);
						if(!$user) continue;
						$status_msg = '解出了此题';
						?>
						<div class="mySmall<?= $user->isSuperman ? ' italic' : '' ?>"><a target="_blank" href="<?= DOMAIN ?>/user.php?id=<?= $download->userId?>"><img src="<?= $user->getImageUrl() ?>" class="small-avatar"/><?= htmlentities($user->name) ?></a> 于<?= date('Y-m-d H:i', $download->createdTime) ?> <?= $status_msg ?></div>
						<?
					}
					foreach ($resumeView as $download) {
						$user = User::fetch($download->userId);
						if(!$user) continue;
						$status_msg = '查看了此题';
						?>
						<div class="mySmall<?= $user->isSuperman ? ' italic' : '' ?>"><a target="_blank" href="<?= DOMAIN ?>/user.php?id=<?= $download->userId?>"><img src="<?= $user->getImageUrl() ?>" class="small-avatar"/><?= htmlentities($user->name) ?></a> 于<?= date('Y-m-d H:i', $download->createdTime) ?> <?= $status_msg ?></div>
					<?
					}
				?>
			</div>
	    <? }*/
}
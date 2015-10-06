<?php
// 610204679@qq.com

function mixin_viewad_sidebar_operations($commentTips, $shareTips, $obj = null) {
	?>
		<div id="resume-comment" class="row">
			<h3><?= $commentTips ?></h3>
			<form method="post" action>
				<div class="form-group">
					<textarea class="form-control" rows="6" name="content"></textarea>
				</div>
				<button type="submit" class="btn btn-default">评论</button>
			</form>
		</div>
		<? if (Visitor::isSuperman()) { ?>
			<div id="adminOps" class="row">
				<h3>超人操作：</h3>
				<form method="post">
					<input type="hidden" name="post_type" value="manager_control">
					<input name='reason' placeholder="删除原因">
					<button>确定</button>
				</form>
				<? if ($obj && ($obj instanceof MidiFile)) { ?>
					<div><a href="<?= DOMAIN ?>/upload.php?editMidiId=<?= $obj->id ?>">Edit MIDI Info</a></div>
				<? } ?>
			</div>
		<? }
		/*
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
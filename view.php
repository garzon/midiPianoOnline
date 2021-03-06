<?php
//813626073@qq.com
require_once('./init.php');

extract(get_object_vars($data));

$pageTitle = 'View MIDI - ' . $midi->name;
require_once(ROOT . '/mixins/header.php');
mixin_header($pageTitle, 'view', [], $data->extra_msg, $data->extra_msg_type);

?>

<div class="container main-block" style="margin-bottom: 100px;" ng-app="resumeSidebar" ng-controller="resumeSidebarController">
	<div class="row col-md-12">
		<div class="{{isSiderbarShown ? 'col-md-8' : 'col-md-12'}} resume-view block-page">
			<div class="row">
				<ul class="nav nav-tabs">
					<li role="presentation" class="active">
						<a href="#">Introduction</a>
					</li>
					<li role="presentation"><a href="#comment-history">Comments(<?= count($comments) ?>)</a></li>
					<span class="pull-right resume-info-counter grey">
						<?= count($midi->tipedList) ?> Likes / <?= $viewCount ?> Viewed
					</span>
				</ul>
			</div>
			<div class="row">
				<h3>
					<?= htmlentities('[' . $midi->category . '] ' . $midi->name) ?>
					<? if(Visitor::user()) {
						if(true) { ?>
							<a class="btn btn-primary btn-sm pull-right btn-fork" href="#">Fork</a>
						<? } else { ?>
							<a class="btn btn-sm btn-default pull-right" href="#">Forked</a>
						<? }

						if (!in_array(Visitor::user()->id, $midi->tipedList)) { ?>
							<a class="btn btn-primary btn-sm pull-right btn-like" href="#">Like</a>
						<? } else { ?>
							<a class="btn btn-sm btn-default pull-right" href="#">Liked</a>
						<? }
					}?>
				</h3>
				<div>
					<p><?= nl2br(htmlentities($midi->introduction)) ?></p>
					<? if($midi->originId) {
						$source = MidiFile::fetch($midi->isForkedFromId);
						if($source) {
							?>
							<p class="grey"><small>Forked from <a href="<?= DOMAIN . '/view.php?id=' . $source->id ?>"><?= htmlentities($source->name) ?></a></small></p>
							<?
						}
						$source = MidiFile::fetch($midi->originId);
						if($source) {
							?>
							<p class="grey"><small>Originally from <a href="<?= DOMAIN . '/view.php?id=' . $source->id ?>"><?= htmlentities($source->name) ?></a></small></p>
						<?
						}
					 } ?>
				</div>
			</div>
			<br />
			<a class="btn btn-success btn-lg" href="<?= DOMAIN . '/editor.php?id=' . $data->midi->id ?>">Play!</a>&nbsp;
			<? if(Visitor::user()) { ?>
				<a class="btn btn-default btn-lg" href="<?= DOMAIN . '/api/download.php?id=' . $data->midi->id ?>">Download</a>
			<? } ?>
			<br />
			<div id="comment-history" class=".resume-comment">
				<h3>Comments:</h3>
				<hr />
				<div>
					<?php foreach ($comments as $comment) { ?>
						<div class="row">
							<div class="col-md-7">
								<h4><?= date('Y-m-d', $comment->createdTime)?> <a href="<?= DOMAIN ?>/user.php?id=<?= $comment->userId ?>"><?= htmlentities(User::fetch($comment->userId)->name) ?></a> said:</h4>
							</div>
							<div class="col-md-5">
								<button id="comment-zan-button-<?= $comment->id ?>" <?= MidiComment::checkVisitorCommented($comment->id) ? 'disabled="disabled"' : ''?> class="btn btn-success col-md-5" onclick="agree(<?= $comment->id ?>)">
									Up(<span id="agree_count_<?= $comment->id ?>"><?= $comment->agreeCount ?></span>)
								</button>
								<button id="comment-pei-button-<?= $comment->id ?>" <?= MidiComment::checkVisitorCommented($comment->id) ? 'disabled="disabled"' : ''?> class="btn btn-danger col-md-5 col-md-offset-1" onclick="disagree(<?= $comment->id ?>)">
									Down(<span id="disagree_count_<?= $comment->id ?>"><?= $comment->disagreeCount ?></span>)
								</button>
							</div>
						</div>
						<div>
							<?= htmlentities($comment->content) ?>
						</div>
						<hr />
					<?php } ?>
				</div>
			</div>
		</div>

		<div class="col-md-4 resume-sidebar block-page" ng-show="isSiderbarShown">
			<div>
				<?
					require_once(ROOT . '/mixins/viewad_sidebar_avatar.php');
					require_once(ROOT . '/mixins/viewad_sidebar_operations.php');
					mixin_viewad_sidebar_avatar($user, 'uploaded in ' . date('Y-m-d', $midi->createdTime));
					mixin_viewad_sidebar_operations('Comment：', 'Share：', $midi);
				?>
			</div>
		</div>
	</div>
	<div class="btn btn-default" style="width: 50px; margin-left: -20px; position: relative; float: left;" ng-click="sidebar_switch()">{{isSiderbarShown ? 'Hide' : 'Expand'}}</div>
</div>

<?php
require_once(ROOT . '/mixins/footer.php');
mixin_footer();
?>

<script>
	function agree(id) {
		$.post('<?= DOMAIN ?>/api/dianzan.php', {id: id}, function(text) {
			if(text != 'succ') return;
			var span = $('#agree_count_' + id);
			span.html(parseInt(span.html()) + 1);
			alert('Successfully upvoted!');
			$('#comment-zan-button-' + id).attr('disabled', 'disabled');
			$('#comment-pei-button-' + id).attr('disabled', 'disabled');
		}, 'text');
	}
	function disagree(id) {
		$.post('<?= DOMAIN ?>/api/pei.php', {id: id}, function(text) {
			if(text != 'succ') return;
			var span = $('#disagree_count_' + id);
			span.html(parseInt(span.html()) + 1);
			alert('Successfully downvoted!');
			$('#comment-zan-button-' + id).attr('disabled', 'disabled');
			$('#comment-pei-button-' + id).attr('disabled', 'disabled');
		}, 'text');
	}
	(function() {
		var app = angular.module("resumeSidebar", []);
		app.controller('resumeSidebarController', function($scope) {
			$scope.isSiderbarShown = true;
			$scope.sidebar_switch = function() {
				$scope.isSiderbarShown = !$scope.isSiderbarShown;
			};
		});

		$(".btn-fork").on('click', function() {
			if(confirm('Fork this midi?')) {
				$.post('<?= DOMAIN ?>/api/fork.php', {id: <?= $midi->id ?>}, function(obj) {
					obj = JSON.parse(obj);
					if(obj.hasOwnProperty('id')) {
						var id = obj.id;
						alert('successfully forked');
						window.location.href = '<?= DOMAIN ?>/view.php?id=' + id;
					} else {
						alert(obj.msg);
					}
				})
			}
		});

		$(".btn-like").on('click', function() {
			if(confirm('Like this midi?')) {
				$.post('<?= DOMAIN ?>/api/tip.php', {id: <?= $midi->id ?>}, function(text) {
					if(text != 'succ') {
						alert(text);
						return;
					}
					$(".btn-like").text('Liked').removeClass("btn-like").removeClass("btn-primary").addClass("btn-default").removeEventListener('');
				}, 'text');
			}
		});
	})();
</script>

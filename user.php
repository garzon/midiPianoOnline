<?php
// 610204679@qq.com
require_once('./init.php');
Visitor::checkLogin();
$visitor = Visitor::user();

$user = User::fetch(getInt('id', 0));
if (!$user) {
	header('Location: ' . DOMAIN . '/index.php');
	die('');
}

$pageTitle = '查看用户';
$recommendFlag = get('toPositionId', 0);

$mixin_listing_blocks_select_resume_callback = null;

if ($recommendFlag) {
	$position = MyPosition::fetch($recommendFlag);
	if (!$position || $user->id != $visitor->id) $recommendFlag = 0;
	else {
		$pageTitle = '推荐简历';
		$mixin_listing_blocks_select_resume_callback = function(Resume $resume) use ($user, $recommendFlag) {
			?>
			<div class="col-md-2 listing-block-sidebar"><span class="red"><?= $resume->prize ?>积分</span></div>
			<div class="col-md-3 listing-block-sidebar">
				<span>
					<a href="/resume.php?id=<?= $resume->id ?>" target="_blank">查看详情</a>
					<a class="bold red user-recommend-resume" href="#" data-resumeid="<?= $resume->id ?>">推荐此简历</a>
				</span>
			</div>
			<?
		};
	}
}

$flag = Problem::FIND_NORMAL;
if ($user->id == $visitor->id) {
	$nav_tab = 2;
	$flag = Problem::FIND_ALL;
}

$resumes = Problem::find($buildQueryFromFilters(['solvedList' => $user->id, 'isHidden' => $flag]), ['limit' => 100,  'sort' => ['createdTime' => -1]]);

require_once('./header.php');
?>
<div class="main-block container">
	<? require(ROOT . '/blocks/user_avatar_info.php'); ?>
	<div class="col-md-12">
		<ul class="nav nav-tabs nav-userpage">
			<li role="presentation" class="active"><a href="#"><?= $recommendFlag ? '解出' : '解出' ?>的题目</a></li>
			<? if (!$recommendFlag) { ?>
			<li role="presentation"><a href="<?= DOMAIN ?>/userFabu.php?id=<?= $user->id ?>">发布的题目</a></li>
			<? } ?>
		</ul>
		<? require(ROOT . '/blocks/filter.php'); ?>
	</div>
	<div class="col-md-12">
		<?
		require_once(ROOT . '/mixins/listing_blocks.php');
		$mixin_listing_blocks($resumes, $mixin_listing_blocks_select_resume_callback);
		?>
	</div>
</div>

<?php
require_once(ROOT . '/mixins/footer.php');
mixin_footer();
?>

<script>
	$document.ready(function() {
		var toPositionId = <?= getInt('toPositionId', 0) ?>;
		$(".user-recommend-resume").on('click', function (e) {
			e.preventDefault();
			var $this = $(this);
			$.post('/api/recommendResume.php', {toPositionId: toPositionId, recommendedResumeId: $this.data('resumeid')}, function(txt) {
				if (txt == 'success') {
					$this.parent().html('已推荐成功');
				}
				if (txt == 'please login') {
					window.location.href = '/login.php';
				}
				if (txt == 'not exists') {
					$this.parent().html('该职位已下线，无法推荐');
				}
				if (txt == 'already recommended') {
					$this.parent().html('您已经推荐过该简历');
				}
			}, 'text');
		});
	});
</script>
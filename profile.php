<?php
//813626073@qq.com
require_once('./init.php');

$pageTitle = 'Edit profile';
require_once(ROOT . '/mixins/header.php');
mixin_header($pageTitle, 'profile', [], $data->extra_msg, $data->extra_msg_type);

$visitor = Visitor::user();

?>
<body class="body-index">
<div class="main-block container block-page">
	<?/*<div class="col-sm-3" style="margin-top: 20px">
		<form enctype="multipart/form-data" class="col-sm-12" method="post">
			<div id="fileList">
				<img id="head-image" src="<?= $visitor->getImageUrl()?>" width = 200 height = 200/>
			</div>
			<input name="image" type="file" id="fileElem" multiple accept="image/*"  onchange="handleFiles(this)">
			<button type="submit" class="btn btn-default">保存头像</button>
			<script>
				window.URL = window.URL || window.webkitURL;
				var fileElem = document.getElementById("fileElem"),
					fileList = document.getElementById("fileList");
				function handleFiles(obj) {
					var files = obj.files,
						img = new Image();
					if(window.URL){
						//File API
						if(files[0].size > 1000000)
							alert("最大只接受1M的头像");
						img.src = window.URL.createObjectURL(files[0]); //创建一个object URL，并不是你的本地路径
						img.width = 200;
						img.height = 200;
						img.id = 'head-image';
						img.onload = function(e) {
							window.URL.revokeObjectURL(this.src); //图片加载后，释放object URL
						}
						var old = document.getElementById("head-image");
						fileList.replaceChild(img, old);
					}else if(window.FileReader){
						//opera不支持createObjectURL/revokeObjectURL方法。我们用FileReader对象来处理
						var reader = new FileReader();
						reader.readAsDataURL(files[0]);
						reader.onload = function(e){
							if(e.total > 1000000)
								alert("最大只接受1M的头像");
							img.src = this.result;
							img.width = 200;
							img.height = 200;
							img.id = 'head-image';
							var old = document.getElementById("head-image");
							fileList.replaceChild(img, old);
						}
					}else{
						//ie
						obj.select();
						obj.blur();
						var nfile = document.selection.createRange().text;
						document.selection.empty();
						img.src = nfile;
						img.width = 200;
						img.height = 200;
						img.id = 'head-image';
						img.onload=function(){
							if(img.fileSize > 1000000)
								alert("最大只接受1M的头像");
						};
						var old = document.getElementById("head-image");
						fileList.replaceChild(img, old);
						//fileList.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod='image',src='"+nfile+"')";
					}
				}
			</script>
		</form>
	</div>*/?>
	<div class="col-sm-12">
		<h3>Edit Profile</h3>
		<form method="post" enctype="multipart/form-data" class="form-horizontal">
			<div class="form-group">
				<label for="name" class="col-sm-2 control-label">Username</label>
				<div class="col-sm-6">
					<input class="form-control" id="name" name='name' disabled value="<?= htmlentities($visitor->name, ENT_QUOTES) ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="location" class="col-sm-2 control-label">Location/City</label>
				<div class="col-sm-6">
					<input class="form-control" id="location" name='location' value="<?= isset($visitor->location) ? htmlentities($visitor->location, ENT_QUOTES) : '' ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="introduction" class="col-sm-2 control-label">Self Introduction</label>
				<div class="col-sm-6">
					<textarea class="form-control" id="introduction" name='introduction'><?= isset($visitor->introduction) ? htmlentities($visitor->introduction) : '' ?></textarea>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-6">
					<button type="submit" class="btn btn-lg btn-primary">Submit</button>
				</div>
			</div>
		</form>
	</div>
</div>
</body>

<?php
require_once(ROOT . '/mixins/footer.php');
mixin_footer();
?>
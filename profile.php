<?php
//813626073@qq.com
require_once('./init.php');
Visitor::checkLogin();

$visitor = Visitor::user();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$acceptedProps = ['phone', 'chatNum', 'qq', 'student_email'];
	foreach ($_POST as $key => $value) {
		if(!in_array($key, $acceptedProps)) continue;
		$visitor->$key = $value;
	}
	if ($image = $_FILES['image']) {
		$image_path = ROOT . '/picture/' . time() . mt_rand(10000, 99999);
		move_uploaded_file($image['tmp_name'], $image_path);
		$visitor->image = $image_path;
	}
	$visitor->save();
	header('Location: ' . DOMAIN . '/user.php?id=' . $visitor->id);
}

$pageTitle = '编辑个人资料';
require_once('./header.php');
?>
<body class="body-index">
<div class="main-block container block-page">
	<div class="col-sm-3" style="margin-top: 20px">
		<form enctype="multipart/form-data" class="col-sm-offset-5" method="post">
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
	</div>
	<div class="col-sm-offset-1 col-sm-8">
		<form method="post" enctype="multipart/form-data" class="col-sm-offset-1 form-horizontal">
			<div class="form-group">
				<label for="name" class="col-sm-2 control-label">昵称</label>
				<div class="col-sm-6">
					<input class="form-control" id="name" name='name' disabled value="<?= htmlentities($visitor->name, ENT_QUOTES) ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="position" class="col-sm-2 control-label">QQ号</label>
				<div class="col-sm-6">
					<input class="form-control" id="qq" name='qq' value="<?= isset($visitor->qq) ? htmlentities($visitor->qq, ENT_QUOTES) : '' ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="chatNum" class="col-sm-2 control-label">微信号</label>
				<div class="col-sm-6">
					<input class="form-control" id="chatNum" name='chatNum' value="<?= isset($visitor->chatNum) ? htmlentities($visitor->chatNum, ENT_QUOTES) : '' ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="phone" class="col-sm-2 control-label">手机号码</label>
				<div class="col-sm-6">
					<input class="form-control" id="phone" name='phone' type="number" value="<?= isset($visitor->phone) ? htmlentities($visitor->phone, ENT_QUOTES) : '' ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="student_email" class="col-sm-2 control-label">学邮</label>
				<div class="col-sm-6">
					<input class="form-control" id="student_email" name='student_email' type="email" value="<?= isset($visitor->student_email) ? htmlentities($visitor->student_email, ENT_QUOTES) : '' ?>">
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-6">
					<button type="submit" class="btn btn-default">保存</button>
				</div>
			</div>
		</form>
	</div>
</div>
</body>

<?php
require_once(ROOT . '/mixins/footer.php');
mixin_footer(true);
?>
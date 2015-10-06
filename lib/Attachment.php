<?php
// 610204679@qq.com

class Attachment {
	public static $attachmentDir;

	public static function generateFilename($ext, $allowedExt = '*') {
		if(is_array($allowedExt)) {
			if(!in_array($ext, $allowedExt)) return false;
		}
		$filename = time() . mt_rand(1000000, 9999999) . '.' . $ext;
		while(file_exists(self::$attachmentDir . $filename)) {
			$filename = time() . mt_rand(1000000, 9999999) . '.' . $ext;
		}
		return $filename;
	}

	public static function generateFilepath($ext, $allowedExt = '*') {
		return self::$attachmentDir . self::generateFilename($ext, $allowedExt);
	}

	public static function copyFile($path) {
		$info = pathinfo($path);
		$ext = $info['extension'];

		$newFilePath = self::generateFilepath($ext);
		copy($path, $newFilePath);

		return $newFilePath;
	}

	public static function deleteFile($path) {
		return shell_exec("rm $path");
	}
}

Attachment::$attachmentDir = ROOT . '/attachments/';
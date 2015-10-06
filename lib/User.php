<?php
// 610204679@qq.com

class User extends MongoContentData {
	public $name;
	protected $score;
	public $email;
	public $introduction;
	public $hash;
	public $lastLoginTime;
	public $isSuperman;
}
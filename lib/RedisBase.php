<?php
//813626073@qq.com

abstract class RedisBase {
	protected $key;
	protected $expire;

	public static $_client = null;

	public function __construct($key, $expire = -1) {
		$this->key = $key;
		$this->expire = $expire;
	}

	public function setExpire($expire) {
		$client = self::client();
		$this->expire = $expire;
		$client->expire($this->key, $expire);
	}

	public function updateExpire() {
		return self::client()->expire($this->key, $this->expire);
	}

	/**
	 * @return Redis
	 */
	protected static function client() {
		if (!RedisBase::$_client) {
			RedisBase::$_client = new Redis();
			RedisBase::$_client->connect('127.0.0.1', 6379);
		}
		return RedisBase::$_client;
	}

	public static function closeClient() {
		if (!RedisBase::$_client) return;
		self::client()->close();
		RedisBase::$_client = null;
	}
}
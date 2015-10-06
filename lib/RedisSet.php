<?php
//813626073@qq.com

class RedisSet extends RedisBase {
	public function add($value) {
		$client = self::client();
		$ret = $client->sAdd($this->key, $value);
		if ($ret !== false) $this->updateExpire();
		return $ret;
	}

	public function count() {
		$client = self::client();
		return $client->sCard($this->key);
	}

	public function isMember($value) {
		$client = self::client();
		return $client->sIsMember($this->key, $value);
	}

	public function getAll() {
		$client = self::client();
		return $client->sMembers($this->key);
	}
}
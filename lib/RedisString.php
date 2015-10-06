<?php
//813626073@qq.com

class RedisString extends RedisBase {
	public function set($value) {
		$client = self::client();
		$ret = $client->set($this->key, $value);
		if ($ret) $this->updateExpire();
		return $ret;
	}

	public function get() {
		$client = self::client();
		return $client->get($this->key);
	}

	public function exist() {
		$client = self::client();
		return $client->exists($this->key);
	}

	public function incr() {
		$client = self::client();
		$ret = $client->incr($this->key);
		$this->updateExpire();
		return $ret;
	}
}
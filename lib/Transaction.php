<?php
// 610204679@qq.com

class Transaction extends MongoData {
	public $userId;
	public $money;
	public $transactionType;
	public $extraData;
	public $createdTime;

	const TYPE_TIPS = 1,
		TYPE_TIPED = 2,
		TYPE_ADDMONEY = 3;

	public function save() {
		if (!$this->extraData) $this->extraData = ['msg' => ''];
		if (!$this->id) {
			$this->createdTime = time();
		}
		parent::save();
	}

	public static function tips(User $payFrom, User $payTo, $price, $msg = '') {
		$price = intval($price);
		if ($payFrom->money < $price) throw new Exception("您的余额不足！");

		$transaction_from = new self();
		$transaction_from->transactionType = self::TYPE_DOWNLOAD;
		$transaction_from->money = -$price;
		$transaction_from->userId = $payFrom->id;
		$transaction_from->extraData = ['msg' => $msg];
		$transaction_from->save();
		$payFrom->_addScore(-$price);

		$transaction_to = new self();
		$transaction_to->transactionType = self::TYPE_DOWNLOADED;
		$transaction_to->money = $price;
		$transaction_to->userId = $payTo->id;
		$transaction_to->extraData = ['msg' => $msg];
		$transaction_to->save();
		$payTo->_addScore($price);
	}

	public static function addScore(User $payer, $money, $msg = '') {
		$money = intval($money);

		$transaction_from = new self();
		$transaction_from->transactionType = self::TYPE_ADDMONEY;
		$transaction_from->money = $money;
		$transaction_from->userId = $payer->id;
		$transaction_from->extraData = ['msg' => $msg];
		$transaction_from->save();
		$payer->_addScore($money);
	}
}
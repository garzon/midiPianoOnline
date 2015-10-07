<?php
// 610204679@qq.com

class Transaction extends MongoData {
	public $userId;
	public $money;
	public $transactionType;
	public $extraData;
	public $createdTime;

	const TYPE_DUMMY = 1,
		TYPE_TIPED = 2,
		TYPE_ADDMONEY = 3;

	public function save() {
		if (!$this->extraData) $this->extraData = ['msg' => ''];
		if (!$this->id) {
			$this->createdTime = time();
		}
		parent::save();
	}

	public static function tips(User $payFrom, User $payTo, MidiFile $midi) {
		$transaction_to = new self();
		$transaction_to->transactionType = self::TYPE_TIPED;
		$transaction_to->money = 1;
		$transaction_to->userId = $payTo->id;
		$transaction_to->extraData = ['msg' => $payFrom->id, 'midiId' => $midi->id];
		$transaction_to->save();
		$payTo->_addScore(1);
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
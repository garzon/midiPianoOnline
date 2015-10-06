<?php
//813626073@qq.com

class MongoData {
	const DATABASE_NAME = 'midi';

	private static $dataPool;
	public $id = null;

	public static function flushCache() {
		$clsName = get_called_class();
		if ($clsName === 'MongoData')
			self::$dataPool = [];
		else
			unset(self::$dataPool[$clsName]);
	}

	public function __invoke() {
		return $this->id !== null;
	}

	public function __construct($id = null) {
		if (!$id) return;
		try {
			$this->parse(self::connection()->findOne(array('_id' => $id)));
		} catch (MongoException $exc) {
			if ($exc->getCode() != 12) throw $exc;
		}
	}

	public static function aggregate(array $pipeline, array $op = [], array $pipelineOperators = []) {
		$datas = self::connection()->aggregate($pipeline, $op, $pipelineOperators);
		return $datas;
	}

	// find all matched elements in array $arrayName, each item in returned array is a document where property $arrayName is an element in array rather than the array
	public static function arrayFind(array $query, $arrayName) {
		return static::aggregate(['$match'=>$query], ['$unwind'=>'$'.$arrayName], ['$match'=>$query])['result'];
	}

	/**
	 * @param array $query
	 * @param array $opt
	 * @return static[]
	 */
	public static function find(array $query, array $opt = []) {
		$datas = self::connection()->find($query, ['_id' => 1]);
		if (isset($opt['skip'])) $datas->skip($opt['skip']);
		if (isset($opt['sort'])) $datas->sort($opt['sort']);
		if (isset($opt['limit'])) $datas->limit($opt['limit']);

		$ret = [];
		foreach ($datas as $data) {
			if (is_scalar($data['_id']))
				if (!isset($opt['commonArray']))
					$ret[$data['_id']] = static::fetch($data['_id']);
				else
					$ret []= static::fetch($data['_id']);
			else
				continue;
		}
		return $ret;
	}

	/**
	 * @param array $query
	 * @param array $opt
	 * @return static
	 */
	public static function findOne(array $query, array $opt = []) {
		return current(self::find($query, $opt));
	}

	/**
	 * @param $id
	 * @return static
	 */
	public static function fetch($id) {
		if ($id === null) return null;
		if ($id instanceof MongoId) return null;
		$id = intval($id);
		if (!isset(self::$dataPool)) self::$dataPool = [];
		if (!isset(self::$dataPool[get_called_class()])) self::$dataPool[get_called_class()] = [];
		if (!isset(self::$dataPool[get_called_class()][$id])) {
			$tmp = self::$dataPool[get_called_class()][$id] = new static($id);
			if (!$tmp()) self::$dataPool[get_called_class()][$id] = null;
		}
		return self::$dataPool[get_called_class()][$id];
	}

	public static function count(array $query) {
		return self::connection()->count($query);
	}

	public static function distinct(array $query, $field) {
		return self::connection()->distinct($field, $query);
	}

	public static function distinctAndCount(array $query, $field) {
		return count(self::distinct($query, $field) ?: []);
	}

	/**
	 * @return $this
	 */
	public function save() {
		$data = $this->data();
		try {
			if (!isset($data['id'])) {
				$data['_id'] = self::getAutoId(get_class($this));
				self::connection()->insert($data);
				$this->id = $data['_id'];
				self::$dataPool[get_class($this)][$this->id] = $this;
			} else {
				$data['_id'] = $data['id'];
				unset($data['id']);
				self::connection()->update(array('_id' => $data['_id']), $data, array("upsert" => true));
			}
		} catch (Exception $e) {
			var_dump($e->getMessage());
		}
		return $this;
	}

	public function delete() {
		$data = $this->data();
		if (!isset($data['id'])) throw new Exception('要删除的数据不存在！');
		$id = $data['id'];
		self::connection()->remove(array('_id' => $id));
		self::$dataPool[get_called_class()][$id] = null;
	}

	protected function columns() {
		$vars = get_object_vars($this);
		unset($vars['connections']);
		return array_keys($vars);
	}

	/**
	 * @return array
	 */
	public function data() {
		$data = [];
		foreach ($this->columns() as $each_col) {
			$data[$each_col] = $this->$each_col;
		}
		return $data;
	}

	protected function parse($data) {
		if ($data === null) return;
		if (is_array($data)) {
			foreach ($this->columns() as $key) {
				$this->$key = isset($data[$key]) ? $data[$key] : null;
			}
			$this->id = $data['_id'];
		}
	}

	private static $connections = [];

	/**
	 * @return MongoCollection
	 */
	protected static function connection() {
		$table_name = get_called_class();
		if (!isset(self::$connections[$table_name])) {
			$client = new MongoClient('mongodb://127.0.0.1:27017', ['connectTimeoutMS' => 1500]);
			self::$connections[$table_name] = $client->selectDB(self::DATABASE_NAME)->selectCollection($table_name);
		}
		return self::$connections[$table_name];
	}

	private static function getAutoId($connectionName) {
		$id = self::connection()->db->command([
			'findAndModify' => 'Sequence',
			'query' => array('name' => $connectionName),
			'update' => array('$inc' => array('id' => 1)),
			'upsert' => true,
			'new' => true,
		]);
		return $id['value']['id'];
	}
}
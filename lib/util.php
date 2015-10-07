<?php
// 610204679@qq.com

class Util {

	public static $categories = ["All categories", "Classic", "Light Music", "New Century", "Comic & Game", "b", "c", "d", "Others",];

	public static $filter_price = [[0, 500], [0, 50], [51, 100], [101, 200], [201, 500]];

	public static function get($key, $defaultVal = null) {
		if (!isset($_GET[$key])) return $defaultVal;
		return strval($_GET[$key]);
	}

	public static function getInt($key, $defaultVal = null) {
		return intval(self::get($key, $defaultVal));
	}

	public static function post($key, $defaultVal = null) {
		if (!isset($_POST[$key])) return $defaultVal;
		return strval($_POST[$key]);
	}

	public static function postInt($key, $defaultVal = null) {
		return intval(self::post($key, $defaultVal));
	}

	public static function buildQueryFromFilters($query = []) {
		if (Util::get('solvedOnly', 0)) $query['solvedList'] = Visitor::user()->id;
		if (Util::get('uploadedOnly', 0)) {
			$query['originId'] = null;
		}

		$price_groupid = intval(Util::get('price', 0));
		if ($price_groupid) {
			$range = Util::$filter_price[$price_groupid];
			$query['price'] = ['$gt' => $range[0] - 1, '$lt' => $range[1] + 1];
		}

		$category = Util::get('category', 0);
		if ($category != 0) $query['category'] = Util::$categories[$category];

		return $query;
	}

	public static function currentPage() {
		$currentPage = Util::getInt('page', 1);
		if ($currentPage < 1) $currentPage = 1;
		return $currentPage;
	}

	public static function buildOptFromPage($query = [], $numPerPage = 20) {
		$currentPage = Util::currentPage();
		$skip = ($currentPage - 1) * $numPerPage;
		$query['limit'] = $numPerPage;
		$query['skip'] = $skip;
		return $query;
	}

	public static function arrToObj($arr) {
		$ret = new \stdClass();
		foreach ($arr as $key => $val) {
			$ret->$key = $val;
		}
		return $ret;
	}

	public static function checkEntry() {
		if (!defined('ROOT')) die('');
	}

	public static function addCharsetMeta() {
		echo "<meta charset='UTF-8'>";
	}

	public static function inRange($item, $dbound, $ubound) {
		return $dbound <= $item && $item <= $ubound;
	}

	public static function reverseBinarySearch($arr, $dbound, $ubound = null, $rangeDBound = 0, $rangeUBound = null) {
		if ($ubound === null) $ubound = $dbound;
		if ($rangeUBound === null) $rangeUBound = count($arr) - 1;
		if ($rangeUBound - $rangeDBound === 1) {
			if (Util::inRange($arr[$rangeDBound], $dbound, $ubound)) return $rangeDBound;
			if (Util::inRange($arr[$rangeUBound], $dbound, $ubound)) return $rangeUBound;
			return -1;
		}
		if ($rangeDBound === $rangeUBound) {
			if (Util::inRange($arr[$rangeDBound], $dbound, $ubound)) return $rangeDBound;
			return -1;
		}
		$m = intval(($rangeUBound + $rangeDBound) / 2);
		if (Util::inRange($arr[$m], $dbound, $ubound)) {
			$idx = Util::reverseBinarySearch($arr, $dbound, $ubound, $rangeDBound, $m - 1);
			if ($idx === -1) return $m; else return $idx;
		}
		if ($arr[$m] < $dbound) return Util::reverseBinarySearch($arr, $dbound, $ubound, $rangeDBound, $m - 1);
		return Util::reverseBinarySearch($arr, $dbound, $ubound, $m + 1, $rangeUBound);
	}

	public static function redirect($url) {
		header("Location: " . $url);
		die('');
	}
}
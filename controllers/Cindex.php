<?php
// 610204679@qq.com

class Cindex extends BaseController {
	public function __construct() {
		parent::__construct();

		$query = Util::buildQueryFromFilters();
		$totalCount = MidiFile::count($query);
		$midis = MidiFile::find($query, Util::buildOptFromPage(['limit' => 100, 'sort' => ['createdTime' => -1]]));

		$this->data->midis = $midis;
		$this->data->totalCount = $totalCount;
	}
}
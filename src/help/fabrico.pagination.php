<?php

namespace Fabrico\Page;

class PaginationPager {
	/**
	 * pagination types
	 */
	const TYPE_DATASET = 'dataset';
	const TYPE_MODEL = 'model';

	/**
	 * page information
	 */
	private $data;
	private $rpp;
	private $page;
	private $type;

	public function __construct (& $data, $rpp = 10, $page = 1, $type = self::TYPE_MODEL) {
		$this->rpp = $rpp;
		$this->page = $page;
		$this->type = $type;
		$this->data = & $data;
	}

	public function get_data () {
		$results = [];
		$from = $this->rpp * ($this->page - 1);
		$to = $from + $this->rpp;

		for ($i = $from; $i < $to; $i++) {
			if (isset($this->data[ $i ])) {
				$results[] = $this->data[ $i ];
			}
		}

		return $results;
	}

	public function get_total () {
		return count($this->data);
	}

	public function set_page ($page) {
		$this->page = $page;
	}

	public function get_page () {
		return $this->page;
	}

	public function set_rpp ($rpp) {
		$this->rpp = $rpp;
	}

	public function get_rpp () {
		return $this->rpp;
	}
}

trait Pagination {
	private function pager ($info) {
		if (!$this->pager_page) {
			$this->pager_page = 1;
		}

		if (!$this->pager_rpp) {
			$this->pager_rpp = 10;
		}

		if (isset($info['dataset'])) {
			$data = $info['dataset']::all();
			$type = PaginationPager::TYPE_DATASET;
		}
		else if (isset($info['model'])) {
			$data = $info['model']::all();
			$type = PaginationPager::TYPE_MODEL;
		}

		$this->pager = new PaginationPager($data, $this->pager_rpp, $this->pager_page, $type);
	}
}

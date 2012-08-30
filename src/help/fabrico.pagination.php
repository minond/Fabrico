<?php

namespace Fabrico\Page;

class PaginationPager {
	private $data;
	private $rpp;
	private $page;

	public function __construct (& $data, $rpp = 10, $page = 1) {
		$this->data = & $data;
		$this->rpp = $rpp;
		$this->page = $page;
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
	private function init_pager () {
		if (!$this->pager_page) {
			$this->pager_page = 1;
		}

		if (!$this->pager_rpp) {
			$this->pager_rpp = 10;
		}
	}

	private function create_pager ($info) {
		return new PaginationPager(
			$info['data'],
			$info['rpp'],
			$info['page']
		);
	}
}

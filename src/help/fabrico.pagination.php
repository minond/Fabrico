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

	public function __construct (& $data, $type = self::TYPE_MODEL) {
		$this->type = $type;
		$this->data = & $data;
	}

	public function get_data () {
		$results = [];
		$from = $this->get_rpp() * ($this->get_page() - 1);
		$to = $from + $this->get_rpp();
		$max = $this->get_total();

		for ($i = $from; $i < $to; $i++) {
			if (isset($this->data[ $i ])) {
				$results[] = $this->data[ $i ];
			}

			if ($i > $max) {
				break;
			}
		}

		return $results;
	}
	
	public function get_pages () {
		$pages = range($this->get_page() - 5, $this->get_page() + 5);
		$max = $this->get_num_pages();

		foreach ($pages as $index => $page) {
			if ($page < 1 || $page > $max) {
				unset($pages[ $index ]);
			}
		}

		return implode(', ', $pages);
	}

	public function get_first_page () {
		return 1;
	}

	public function get_last_page () {
		return $this->get_num_pages();
	}

	public function get_next_page () {
		return $this->get_num_pages() >= $this->get_page() + 1 ?
		       $this->get_page() + 1 : $this->get_page();
	}

	public function get_previous_page () {
		return $this->get_page() - 1 > 0 ?
		       $this->get_page() - 1 : $this->get_page();
	}

	public function get_num_pages () {
		return ceil($this->get_total() / $this->get_rpp());
	}

	public function get_total () {
		return count($this->data);
	}

	public function set_page ($page) {
		$this->page = $page;

		if ($this->page > $this->get_num_pages()) {
			$this->page = $this->get_num_pages();
		}
		else if ($this->page < 1) {
			$this->page = 1;
		}
	}

	public function get_page () {
		return is_numeric($this->page) ? $this->page : 1;
	}

	public function set_rpp ($rpp) {
		$this->rpp = $rpp;
	}

	public function get_rpp () {
		return is_numeric($this->rpp) && $this->rpp > 0 ? $this->rpp : 10;
	}

	public function has_previous () {
		return $this->get_page() > 1;
	}

	public function has_next () {
		return $this->get_page() != $this->get_num_pages();
	}
}

trait Pagination {
	private function pager ($info) {
		if (isset($info['dataset'])) {
			$data = $info['dataset']::all();
			$type = PaginationPager::TYPE_DATASET;
		}
		else if (isset($info['model'])) {
			$data = $info['model']::all();
			$type = PaginationPager::TYPE_MODEL;
		}

		$this->pager = new PaginationPager($data, $type);
	}

	public function set_pager_info ($page, $rpp) {
		$this->pager->set_rpp($rpp);
		$this->pager->set_page($page);
	}
}

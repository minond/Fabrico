<?php

namespace Fabrico\Page;

class PaginationPager {
	/**
	 * ui settings
	 */
	const MAXPAGES = 7;

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

	public function get_data_range () {
		$range = new \stdClass;

		$range->from = $this->get_rpp() * ($this->get_page() - 1);
		$range->to = $range->from + $this->get_rpp();
		$range->max = $this->get_total();
		$range->human_from = $range->from + 1;
		$range->human_to = $range->to > $range->max ? $range->max : $range->to;
		
		return $range;
	}

	public function get_data () {
		$results = [];
		$range = $this->get_data_range();

		for ($i = $range->from; $i < $range->to; $i++) {
			if (isset($this->data[ $i ])) {
				$results[] = $this->data[ $i ];
			}

			if ($i > $range->max) {
				break;
			}
		}

		return $results;
	}
	
	public function get_pages () {
		$padding = floor(self::MAXPAGES / 2);
		$range = $padding * 2 + 1;
		$pages = range($this->get_page() - $padding, $this->get_page() + $padding);
		$max = $this->get_num_pages();

		foreach ($pages as $index => $page) {
			if ($page < 1 || $page > $max) {
				unset($pages[ $index ]);
			}
		}

		$pages = array_values($pages);

		// can we add down?
		if (count($pages) < $range) {
			$first = $pages[ 0 ];

			if ($first != 1) {
				for ($i = $first - 1; $i > 0; $i--) {
					if (count($pages) < $range) {
						array_unshift($pages, $i);
					}
					else {
						break;
					}
				}
			}
		}

		// can we add up?
		if (count($pages) < $range) {
			$last = $pages[ count($pages) - 1 ];

			if ($last != $this->get_last_page()) {
				for ($i = $last + 1; $i <= $this->get_last_page(); $i++) {
					if (count($pages) < $range) {
						array_push($pages, $i);
					}
					else {
						break;
					}
				}
			}
		}

		return $pages;
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

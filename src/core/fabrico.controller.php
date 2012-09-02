<?php

namespace Fabrico;

class Controller {
	/**
	 * state helpers
	 */
	const STATE_ERROR = 'Invalid controller variable requested by state';

	/**
	 * standard controller methods
	 */
	const GET_NODE_CONTENT = 'get_node_content';
	const SET_PAGER_INFO = 'set_pager_info';

	/**
	 * methods public to http requests
	 * @var array
	 */
	public $public = [
		self::GET_NODE_CONTENT,
		self::SET_PAGER_INFO,
	];

	/**
	 * pagination variables
	 * @var PaginationPager
	 */
	public $pager;

	/**
	 * called after setting state
	 */
	public function initialize () {}

	/**
	 * on view virtual method
	 */
	public function onview () {}

	/**
	 * state getter
	 *
	 * @return array of controller variables to save
	 */
	public function __get_state () {
		$state = [];

		foreach ($this->track as $field) {
			if (!property_exists($this, $field)) {
				throw new \Exception(self::STATE_ERROR);
			}

			$state[ $field ] = $this->{ $field };
		}

		return $state;
	}

	/**
	 * state setter
	 *
	 * @param array of controller variables to set
	 */
	public function __load_state ($state) {
		foreach ($state as $field => $value) {
			if (!property_exists($this, $field)) {
				throw new \Exception(self::STATE_ERROR);
			}

			$this->{ $field } = $value;
		}
	}
}

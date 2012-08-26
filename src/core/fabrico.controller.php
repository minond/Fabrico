<?php

namespace Fabrico;

class Controller {
	/**
	 * standard api methods
	 */
	const GET_NODE_CONTENT = 'get_node_content';

	/**
	 * methods public to http requests
	 * @var array
	 */
	public $public = [];

	/**
	 * state whitelist
	 * @var array
	 */
	public $track = [];

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
			$this->{ $field } = $value;
		}
	}
}

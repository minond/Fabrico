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
	 * used to flag a variable as one to save in the state
	 * @var array
	 */
	public $track = [];

	/**
	 * allowed formats for data requests
	 * @var array
	 */
	public $formats = [];

	/**
	 * parameter getter helper
	 * @var param
	 */
	public $param;

	public function __construct () {
		$this->param = new \param;
	}
	
	/**
	 * makes a method public
	 *
	 * @param string methods*
	 */
	public function publish ($methods) {
		$this->public = array_merge($this->public, func_get_args());
	}

	/**
	 * for method requests
	 * @var session id
	 */
	public function session_id () {
		return session_id();
	}

	/**
	 * called after setting state
	 */
	public function setup () {}

	/**
	 * called right before serving request
	 */
	public function initialize () {}

	/**
	 * on view virtual method
	 */
	public function onview () {}

	/** 
	 * placeholder for the Controller\PublicAccess interface
	 */
	public function onbeforemethod ($method, & $arguments) {}

	/** 
	 * placeholder for the Controller\PublicAccess interface
	 */
	public function onaftermethod ($method, & $arguments) {}

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
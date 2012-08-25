<?php

namespace Fabrico;

class Dataset {
	/**
	 * session key
	 */
	const ROOT = '__dataset';

	/**
	 * data set memory array
	 * @var array
	 */
	private static $memory;

	/**
	 * data set instance id
	 * @var integer
	 */
	public $__id;

	/**
	 * data set created date timestamp
	 * @var unix time
	 */
	public $__ts;

	/**
	 * data set instance storage access
	 * @var array
	 */
	private $__storage;

	/**
	 * creates a new data set instance
	 * will also create a new session property and data
	 * set storage place
	 *
	 * @param array of data set data
	 */
	public function __construct	($information = []) {
		self::initialize_session();
		$this->declate_set();

		foreach ($information as $field => $data) {
			if (property_exists($this, $field)) {
				$this->{ $field } = $data;
			}
		}
	}

	/**
	 * creates a place in the session to store data set information
	 */
	public static function initialize_session () {
		if (!isset($_SESSION[ self::ROOT ])) {
			$_SESSION[ self::ROOT ] = [];
		}

		self::$memory = & $_SESSION[ self::ROOT ];
	}

	/**
	 * clears the session storage
	 */
	public static function clear_session () {
		unset($_SESSION[ self::ROOT ]);
		self::initialize_session();
	}

	/**
	 * created a place in memory to save a specific data set
	 *
	 * @param string data set name
	 */
	private function declate_set ($name = false) {
		if (!$name) {
			$name = get_class($this);
		}

		$name = util::last(explode('\\', $name));

		if (!isset(self::$memory[ $name ])) {
			self::$memory[ $name ] = [];
		}

		$this->__storage = & self::$memory[ $name ];
	}

	/**
	 * saves a data set
	 *
	 * @return integer set id
	 */
	public function save () {
		// update
		if ($this->__id) {
			foreach ($this->__storage as $index => $set) {
				if (json_decode($set)->__id === $this->__id) {
					$this->__storage[ $index ] = json_encode($this);
					break;
				}
			}
		}
		// save
		else {
			$this->__ts = time();
			$this->__id = count($this->__storage) + 1;
			$this->__storage[] = json_encode($this);
		}

		return $this->__id;
	}

	/**
	 * finds a data set
	 *
	 * @param array of filters
	 * @param boolean return first match
	 * @return mixed Dataset instance | array of Datasets
	 */
	public static function find ($filters, $first = false) {
		$name = get_called_class();
		$matches = [];

		if (isset(self::$memory[ $name ])) {
			$storage = & self::$memory[ $name ];

			foreach ($storage as $set) {
				$tmp = json_decode($set);
				$match = true;

				foreach ($filters as $filter => $value) {
					if ($tmp->{ $filter } !== $value) {
						$match = false;
						break;
					}
				}

				if ($match && $first) {
					return new $name($tmp);
				}
				else if ($match) {
					$matches[] = new $name($tmp);
				}
			}
		}

		return $first ? null : $matches;
	}

	/**
	 * finds a data set
	 *
	 * @param array of filters
	 * @return mixed Dataset instance
	 */
	public static function get ($filters) {
		return self::find($filters, true);
	}
}

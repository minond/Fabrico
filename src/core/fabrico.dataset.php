<?php

namespace Fabrico;

class Dataset {
	/**
	 * session key
	 */
	const ROOT = '__dataset';

	/**
	 * storage types
	 */
	const SESSION = 'use_session';

	/**
	 * storage type
	 * @var string
	 */
	protected static $__storage = self::SESSION;

	/**
	 * data set memory array
	 * @var array
	 */
	private static $__memory;

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
	 * creates a new data set instance
	 * will also create a new session property and data
	 * set storage place
	 *
	 * @param array of data set data
	 */
	public function __construct	($information = []) {
		self::initialize();

		foreach ($information as $field => $data) {
			if (property_exists($this, $field)) {
				$this->{ $field } = $data;
			}
		}
	}

	/**
	 * initializes memory sets are saved in
	 */
	public static function initialize () {
		switch (static::$__storage) {
			case self::SESSION:
				self::initialize_session();
				break;

			default:
				throw new \Exception(Merge::parse('Invalid storage type "#{type}" for Dataset #{name}', [
					'type' => static::$__storage,
					'name' => get_called_class()
				]));
		}

		self::declate_set();
	}

	/**
	 * creates a place in the session to store data set information
	 */
	private static function initialize_session () {
		if (!isset($_SESSION[ self::ROOT ])) {
			$_SESSION[ self::ROOT ] = [];
		}

		self::$__memory = & $_SESSION[ self::ROOT ];
	}

	/**
	 * clears the session storage
	 */
	public static function clear () {
		switch (static::$__storage) {
			case self::SESSION:
				unset($_SESSION[ self::ROOT ]);
				break;
		}

		self::initialize();
	}

	/**
	 * created a place in memory to save a specific data set
	 *
	 * @param string data set name
	 */
	private static function declate_set ($name = false) {
		if (!$name) {
			$name = get_called_class();
		}

		$name = util::last(explode('\\', $name));

		if (!isset(self::$__memory[ $name ])) {
			self::$__memory[ $name ] = [];
		}
	}

	/**
	 * saves a data set
	 *
	 * @return integer set id
	 */
	public function save () {
		$storage = & self::$__memory[ get_class($this) ];

		// update
		if ($this->__id) {
			foreach ($storage as $index => $set) {
				if (json_decode($set)->__id === $this->__id) {
					$storage[ $index ] = json_encode($this);
					break;
				}
			}
		}
		// save
		else {
			$this->__ts = time();
			$this->__id = count($storage) + 1;
			$storage[] = json_encode($this);
		}

		return $this->__id;
	}

	/**
	 * returns all data sets of the current type
	 *
	 * @return array of Dataset instances
	 */
	public static function all () {
		self::initialize();
		$name = get_called_class();
		$ret = [];

		if (isset(self::$__memory[ $name ]))
			foreach (self::$__memory[ $name ] as $set)
				$ret[] = new $name(json_decode($set));

		return $ret;
	}

	/**
	 * finds a data set
	 *
	 * @param array of filters
	 * @param boolean return first match
	 * @return mixed Dataset instance | array of Datasets
	 */
	public static function find ($filters, $first = false) {
		self::initialize();
		$name = get_called_class();
		$matches = [];

		if (!count($filters)) {
			return $matches;
		}

		if (isset(self::$__memory[ $name ])) {
			foreach (self::$__memory[ $name ] as $set) {
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
		if (!is_array($filters)) {
			$filters = [ '__id' => $filters ];
		}

		return self::find($filters, true);
	}
}

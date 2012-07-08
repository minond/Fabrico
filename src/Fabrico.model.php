<?php

class FabricoModel extends FabricoQuery {
	/**
	 * @name instance
	 * @var FabricoModel
	 */
	private static $instance;

	/**
	 * @name table
	 * @var string module table
	 */
	private $table;

	/**
	 * @name FabricoModel
	 * creates a new connection if needed and selects
	 * the project database
	 */
	private function __construct () {
		$info = Fabrico::get_config()->project;

		if (!isset(self::$connection)) {
			require_once Fabrico::$service->database;
			self::$connection = FabricoSingleDatabaseConnection::request(
				$info->database->server,
				$info->database->username,
				$info->database->password
			);
		}

		self::$connection->open(
			$info->databases-> {
				$info->database->current
			}
		);

		$this->table = strtolower(get_called_class());
		$this->loadinfo();
	}

	/**
	 * @name init
	 * creates a new model instalce (singleton)
	 */
	public static function init () {
		if (!isset(self::$instance)) {
			$class = get_called_class();
			self::$instance = new $class;
		}
	}

	/** 
	 * @name loadinfo
	 * loads the model's table/field inforamation
	 */
	public function loadinfo () {
		$this->show(self::COLUMNS);
		$this->from($this->table);
		return $this->run_query();
	}
}


class FabricoQuery {
	// database connection singleton
	protected static $connection;

	// query structure
	protected $select;
	protected $show;
	protected $from;
	protected $join;
	protected $where;
	protected $group;
	protected $having;
	protected $order;

	// show list
	const COLUMNS = 'columns';
	const PROCESSLIST = 'processlist';

	// show
	protected function show ($what) {
		$this->show = "show {$what}";
	}

	// from
	protected function from ($table) {
		$this->from = "from {$table}";
	}

	// select
	protected function select ($fields) {
		$this->select = 'select ' . (is_array($fields) ? implode($fields, ',') : $fields);
	}

	// join
	protected function join ($from, $table, $on) {
		$this->join .= "{$from} join {$table} on ({$on})" . PHP_EOL;
	}

	// where
	protected function where ($filters) {
		$this->where = "where {$filters}";
	}

	// group
	protected function group ($by) {
		$this->group = 'group by ' . (is_array($by) ? implode($by, ',') : $by);
	}

	// having
	protected function having ($have) {
		$this->having = "having {$having}";
	}

	// order
	protected function order ($by) {
		$this->order = 'order by ' . (is_array($by) ? implode($by, ',') : $by);
	}

	// run_query
	protected function run_query () {
		if (isset(self::$connection)) {
			$sql = $this->get_query();
			$this->clear_query();

			return self::$connection->query($sql);
		}
	}

	// get query
	public function get_query () {
		return trim(implode(array(
			$this->select,
			$this->show,
			$this->from,
			$this->join,
			$this->where,
			$this->group,
			$this->having,
			$this->order
		), PHP_EOL));
	}

	// clear_query
	public function clear_query () {
		$this->select = '';
		$this->show = '';
		$this->from = '';
		$this->join = '';
		$this->where = '';
		$this->group = '';
		$this->having = '';
		$this->order = '';;
	}
}

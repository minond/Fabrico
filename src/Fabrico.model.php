<?php

/**
 * @name FabricoModel
 * @var FabricoModel
 */
class FabricoModel extends FabricoQuery {
	/**
	 * @name just_checking
	 * @var array
	 */
	protected static $just_checking;

	/**
	 * @name and_must_be
	 * @var array
	 */
	protected static $and_must_be;

	/**
	 * @name and_must_not_be
	 * @var array
	 */
	protected static $and_must_not_be;

	/**
	 * @name send_back
	 * @var array
	 */
	protected static $send_back;

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
	 * @name columns
	 * @var stdClass of table columns
	 */
	private $columns;

	/**
	 * @name column_names
	 * @var array of table columns
	 */
	private $column_names = array();

	/**
	 * @name primary_key
	 * @var string columns name
	 */
	private $primary_key;

	/**
	 * @name primary_key
	 * @return string model primary key
	 */
	public static function primary_key ($forquery = true) {
		return !$forquery ? self::$instance->primary_key : 
		       sprintf(self::FIELD, self::$instance->primary_key);
	}

	/**
	 * @name sel_fields
	 * @param fields*
	 * @return string field selector
	 */
	public static function sel_fields () {
		$list = array();
		$first = func_num_args() !== 0 ? func_get_arg(0) : null;
		$fields = is_array($first) ? $first : func_get_args();
		array_unshift($fields, self::primary_key(false));

		foreach ($fields as $field) {
			$list[] = sprintf(self::FIELD, $field);
		}

		return implode(', ', $list);
	}

	/**
	 * @name get_columns
	 * @return array
	 */
	public function get_columns () {
		if (!isset($this->fields)) {
			$this->loadinfo();
		}

		return array_keys($this->column_names);
	}

	/**
	 * @name FabricoModel
	 * creates a new connection if needed and selects
	 * the project database
	 */
	private function __construct () {
		$info = Fabrico::get_config()->project;
		$this->table = strtolower(get_called_class());

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
	}

	/**
	 * @name init
	 * creates a new model instance (singleton)
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
		$this->columns = $this->run_query();

		foreach ($this->columns as $index => $field) {
			$this->columns[ $index ] = $field = (object) $field;
			$this->column_names[ $field->Field ] = $field;

			if (strpos($field->Key, self::PRIMARY_KEY) !== false) {
				$this->primary_key = $field->Field;
			}
		}
	}

	/**
	 * @name obj2str
	 * @param stdClass model object
	 * @return string filter query
	 */
	public static function obj2str (& $obj) {
		$str = array();

		foreach ($obj as $key => $value) {
			if (!is_null($value)) {
				if ($value === true) {
					$str[] = "`{$key}`";
				}
				else if ($value === false) {
					$str[] = "!`{$key}`";
				}
				else {
					$clean = mysql_escape_string($value);
					$str[] = "`{$key}` = '{$clean}'";
				}
			}
		}

		return implode(self::ANDS, $str);
	}

	/** 
	 * @name get
	 * @param int primary key id
	 * @return table row
	 */
	public static function get ($id) {
		if (!isset(self::$instance->fields)) {
			self::$instance->loadinfo();
		}

		self::$instance->select(self::ALL);
		self::$instance->from(self::$instance->table);
		self::$instance->where(self::$instance->primary_key . self::EQ . $id);

		$return = self::$instance->run_query();
		return count($return) ? (object) $return[ 0 ] : new stdClass;
	}

	/**
	 * @name create
	 * @return stdClass blank model object
	 */
	public static function create () {
		$item = new stdClass;
		$values = array();
		$fields = self::$instance->get_columns();

		// query checks
		$first = func_num_args() !== 0 ? func_get_arg(0) : null;
		$tofill = is_array($first) ? $first : func_get_args();

		if (util::is_hash($tofill)) {
			$values =& $tofill;
		}
		else {
			foreach ($tofill as $fill) {
				$values[ $fill ] = Fabrico::req($fill);
			}
		}

		foreach ($fields as $field) {
			$item->{ $field } = array_key_exists($field, $values) ? $values[ $field ] : null;
		}

		return $item;
	}

	/**
	 * @name search
	 * @param string filters
	 * @return stdClass retults
	 */
	public static function search ($filters, $select = self::ALL, $just_one = false) {
		$filter = is_object($filters) || is_array($filters) ? self::obj2str($filters) : $filters;

		self::$instance->select($select);
		self::$instance->from(self::$instance->table);
		self::$instance->where($filter);

		if ($just_one) {
			self::$instance->limit(1);
		}

		$return = self::$instance->run_query();

		if (count($return)) {
			if ($just_one)
				$return = (object) $return[ 0 ];
			else {
				foreach ($return as $index => $data) {
					$return[ $index ] = (object) $data;
				}
			}
		}
		else {
			$return = new stdClass;
		}

		return $return;
	}

	/**
	 * @name add
	 * @param array of data
	 * @return int last insert id
	 */
	public static function add ($data) {
		if (!isset(self::$instance->fields)) {
			self::$instance->loadinfo();
		}

		self::$instance->insert(
			self::$instance->table, $data, 
			self::$instance->column_names
		);

		self::$instance->run_query();
		return self::$instance->last_id();
	}

	/**
	 * @name check
	 * @param array defaults to just_checking
	 * @return stdClass results
	 * @see just_checking
	 */
	public static function check ($data = null) {
		return static::search(
			// filter
			static::create(
				util::is_hash($data) ? $data : static::$just_checking
			),

			// selects
			is_array(static::$send_back) ?
				static::sel_fields(static::$send_back) :
				self::ALL,

			// limit
			true
		);
	}
}


/**
 * @name FabricoQuery
 * var FabricoQuery
 */
class FabricoQuery {
	// errors
	const UNKNOW_COLUMN = 'unknown column';

	// database connection singleton
	protected static $connection;

	// query structure
	protected $select;
	protected $insert;
	protected $update;
	protected $show;
	protected $from;
	protected $join;
	protected $where;
	protected $group;
	protected $having;
	protected $order;
	protected $limit;

	// show list
	const COLUMNS = 'columns';
	const PROCESSLIST = 'processlist';
	
	// keys
	const PRIMARY_KEY = 'PRI';

	// selects
	const ALL = '*';

	// filters
	const EQ = ' = ';
	const ANDS = ' and ';

	// delimeter
	const COMMA = ', ';
	const FIELD = '`%s`';
	const VALUE_STR = '"%s"';

	// is_string_field
	public static function is_string_field ($field_data) {
		return preg_match(
			'/varchar|char|string|date|datetime|timestamp|enum/',
			strtolower($field_data->Type)
		);
	}

	// show
	protected function show ($what) {
		$this->show = "show {$what}";
	}

	// insert
	protected function insert ($table, & $data, & $columns) {
		$fields = array_keys($data);
		$values = array_values($data);

		array_walk($values, function ($value, $index) use (& $values, & $fields, & $columns, & $table) {
			if (array_key_exists($fields[ $index ], $columns)) {
				if (FabricoQuery::is_string_field($columns[ $fields[ $index ] ])) {
					$values[ $index ] = sprintf(FabricoQuery::VALUE_STR, $value);
				}
				else {
					$values[ $index ] = $value;
				}
			}
			else {
				util::loglist(FabricoQuery::UNKNOW_COLUMN, array(
					'table' => $table,
					'field' => $fields[ $index ]
				));

				unset($values[ $index ]);
				unset($fields[ $index ]);
			}
		});

		array_walk($fields, function ($value, $index) use (& $fields) {
			$fields[ $index ] = sprintf(FabricoQuery::FIELD, $value);
		});

		$fields = implode(self::COMMA, $fields);
		$values = implode(self::COMMA, $values);

		$this->insert = "insert into {$table} ($fields) values ({$values})";
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

	// limit
	protected function limit ($num) {
		$this->limit = "limit {$num}";
	}

	// run_query
	protected function run_query () {
		if (isset(self::$connection)) {
			$sql = $this->get_query();
			$noret = $this->insert;
			$this->clear_query();

			return self::$connection->query($sql, $noret);
		}
	}

	// last_id
	protected function last_id () {
		if (isset(self::$connection)) {
			return self::$connection->last_id();
		}
		else {
			return 0;
		}
	}

	// get query
	public function get_query () {
		$list = array(
			$this->select,
			$this->insert,
			$this->update,
			$this->show,
			$this->from,
			$this->join,
			$this->where,
			$this->group,
			$this->having,
			$this->order,
			$this->limit,
		);

		foreach ($list as $index => $item)
			if (!$item)
				unset($list[ $index ]);

		return trim(implode($list, PHP_EOL));
	}

	// clear_query
	public function clear_query () {
		$this->select = '';
		$this->show   = '';
		$this->insert = '';
		$this->update = '';
		$this->from   = '';
		$this->join   = '';
		$this->where  = '';
		$this->group  = '';
		$this->having = '';
		$this->order  = '';
		$this->limit  = '';
	}
}

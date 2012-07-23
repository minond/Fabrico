<?php

/**
 * @name FabricoModel
 * @var FabricoModel
 */
class FabricoModel extends FabricoQuery {
	/**
	 * @name has_many
	 * @var string
	 */
	protected static $has_many;

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
	public static $instance;

	/**
	 * @name data
	 * @var array
	 */
	private $data = array();

	/**
	 * @name primary_key
	 * @return string model primary key
	 */
	public static function primary_key ($forquery = true) {
		$class = get_called_class();
		return !$forquery ? static::$instance->data[ $class ]->primary_key :
		       sprintf(self::FIELD, static::$instance->data[ $class ]->primary_key);
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
	public function get_columns ($class) {
		$this->loadinfo($class);
		return array_keys($this->data[ $class ]->column_names);
	}

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
	}

	/**
	 * @name init
	 * creates a new model instance (singleton)
	 */
	public static function init () {
		if (!isset(self::$instance)) {
			self::$instance = new self;
		}

		$class = get_called_class();
		self::$instance->data[ $class ] = new stdClass;
		self::$instance->data[ $class ]->table = strtolower(get_called_class());
		self::$instance->data[ $class ]->primary_key = null;
		self::$instance->data[ $class ]->columns = array();
		self::$instance->data[ $class ]->column_names = array();
		define($class, $class);
	}

	/** 
	 * loads the model's table/field inforamation
	 *
	 * @name loadinfo
	 * @param string model name
	 * @param boolean force info check even if already done
	 */
	public function loadinfo ($class, $force = false) {
		if (!$force && isset(self::$instance->data[ $class ]->columns) &&
		    isset(self::$instance->data[ $class ]->column_names) &&
		    isset(self::$instance->data[ $class ]->primary_key)) return;

		$this->clear_query();
		$this->show(self::COLUMNS);
		$this->from(self::$instance->data[ $class ]->table);
		self::$instance->data[ $class ]->columns = $this->run_query();
		$columns =& self::$instance->data[ $class ]->columns;
		$column_names =& self::$instance->data[ $class ]->column_names;

		foreach ($columns as $index => $field) {
			$columns[ $index ] = $field = (object) $field;
			$column_names[ $field->Field ] = $field;

			if (strpos($field->Key, self::PRIMARY_KEY) !== false) {
				self::$instance->data[ $class ]->primary_key = $field->Field;
			}
		}
		
		if (!isset(self::$instance->data[ $class ]->primary_key)) {
			self::$instance->data[ $class ]->primary_key = self::DEF_PRIMARY_KEY;
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
		$class = get_called_class();
		self::$instance->loadinfo($class);

		self::$instance->select(self::ALL);
		self::$instance->from(self::$instance->data[ $class ]->table);
		self::$instance->where(self::$instance->data[ $class ]->primary_key . self::EQ . $id);

		$return = self::$instance->run_query();

		if (count($return)) {
			$data = (object) $return[ 0 ];
			$return = new FabricoModelInstance($data, $class);
		}
		else {
			$return = static::create(false);
		}

		return $return;
	}

	/**
	 * @name create
	 * @return stdClass blank model object
	 */
	public static function create () {
		$class = get_called_class();
		$item = new stdClass;
		$values = array();
		$fields = self::$instance->get_columns($class);

		// query checks
		$first = func_num_args() !== 0 ? func_get_arg(0) : null;
		$tofill = is_array($first) ? $first : func_get_args();

		if ($first === false) {
			$tofill = array();
		}

		if (is_object($tofill) || util::is_hash($tofill)) {
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

		return new FabricoModelInstance($item, $class);
	}

	/**
	 * @name search
	 * @param string filters
	 * @return stdClass retults
	 */
	public static function search ($filters = '', $select = self::ALL, $just_one = false) {
		$class = get_called_class();
		self::$instance->loadinfo($class);

		if ($filters instanceof FabricoModelInstance) {
			$filters = $filters->getdata();
		}

		$filter = is_object($filters) || is_array($filters) ? self::obj2str($filters) : $filters;

		self::$instance->select($select);
		self::$instance->from(self::$instance->data[ $class ]->table);

		if ($filters) {
			self::$instance->where($filter);
		}

		if ($just_one) {
			self::$instance->limit(1);
		}

		$return = self::$instance->run_query();

		if (count($return)) {
			if ($just_one) {
				$data = (object) $return[ 0 ];
				$return = new FabricoModelInstance($data, $class);
			}
			else {
				foreach ($return as $index => $data) {
					$retdata = (object) $data;
					$return[ $index ] = new FabricoModelInstance($retdata, $class);
				}
			}
		}
		else {
			$return = false;
		}

		return $return;
	}

	/**
	 * @name del
	 * @param int record id
	 */
	public static function del ($id) {
		$class = get_called_class();
		self::$instance->loadinfo($class);

		self::$instance->remove(
			self::$instance->data[ $class ]->table, $id
		);

		self::$instance->run_query();
	}

	/**
	 * @name add
	 * @param array of data
	 * @return int last insert id
	 */
	public static function add ($data) {
		$class = get_called_class();
		self::$instance->loadinfo($class);
		$data = (array) $data;
		$prikey = self::$instance->data[ $class ]->primary_key;

		// save or update check
		if ($data[ $prikey ]) {
			$id = $data[ $prikey ];
			unset($data[ $prikey ]);

			self::$instance->update(
				self::$instance->data[ $class ]->table, $data,
				self::$instance->data[ $class ]->column_names
			);

			self::$instance->where(sprintf(self::FIELD, $prikey) . self::EQ . $id);
		}
		else {
			self::$instance->insert(
				self::$instance->data[ $class ]->table, $data,
				self::$instance->data[ $class ]->column_names
			);
		}

		self::$instance->run_query();
		return self::$instance->last_id();
	}

	/**
	 * @name save
	 * @param array of data
	 * @return int last insert id
	 * @see add
	 */
	public static function save ($data) {
		return static::add($data);
	}

	/**
	 * @name check
	 * @param array defaults to just_checking
	 * @return stdClass results
	 * @see just_checking
	 */
	public static function check ($data = null) {
		$result = static::search(
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

		if ($result === false) {
			$result = static::create(false);
		}

		return $result;
	}

	/**
	 * @name get_foreign_key
	 * @param string parent table name
	 * @return string child's table parent id
	 */
	private static function get_foreign_key ($table) {
		return "{$table}_id";
	}

	/**
	 * @name children
	 * @param int parent id
	 * @return array of children
	 */
	public static function children ($id = false) {
		$class = get_called_class();
		$table = self::$instance->data[ $class ]->table;
		$parent_table = self::$instance->data[ static::$has_many ]->table;
		$parent_id = self::get_foreign_key($table);

		self::$instance->select(self::ALL);
		self::$instance->from($parent_table);

		if ($id) {
			self::$instance->where("{$parent_id} = {$id}");
		}

		$return = self::$instance->run_query();

		foreach ($return as $key => $value)
			$return[ $key ] = (object) $value;

		return $return;
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
	protected $remove;
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
	protected $set;

	// show list
	const COLUMNS = 'columns';
	const PROCESSLIST = 'processlist';
	
	// keys
	const PRIMARY_KEY = 'PRI';
	const DEF_PRIMARY_KEY = 'id';

	// selects
	const ALL = '*';

	// filters
	const EQ = ' = ';
	const ANDS = ' and ';

	// delimeter
	const COMMA = ', ';
	const FIELD = '`%s`';
	const VALUE_STR = '"%s"';

	// cleanupdata
	public function cleanupdata (& $values, & $fields, & $columns) {
		foreach ($values as $key => $value) {
			if (is_null($value)) {
				unset($values[ $key ]);
				unset($fields[ $key ]);
			}
			else if (FabricoQuery::is_string_field($columns[ $fields[ $key ] ])) {
				$values[ $key ] = sprintf(FabricoQuery::VALUE_STR, $value);
			}
		}

		foreach ($fields as $index => $field)
			$fields[ $index ] = sprintf(self::FIELD, $field);
	}

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

	// update
	protected function update ($table, & $data, & $columns) {
		$fields = array_keys($data);
		$values = array_values($data);
		$updates = array();

		$this->cleanupdata($values, $fields, $columns);

		foreach ($fields as $index => $field) {
			$value = $values[ $index ];
			$updates[] = "{$field} = {$value}";
		}

		$this->update = "update {$table}";
		$this->set = 'set ' . implode(self::COMMA, $updates);
	}

	// insert
	protected function insert ($table, & $data, & $columns) {
		$fields = array_keys($data);
		$values = array_values($data);

		$this->cleanupdata($values, $fields, $columns);
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

	// remove
	protected function remove ($table, $id) {
		$this->remove = "delete from {$table} where id = {$id}";
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
			$noret = $this->insert || $this->remove || $this->update;
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
			$this->remove,
			$this->select,
			$this->insert,
			$this->update,
			$this->show,
			$this->from,
			$this->join,
			$this->set,
			$this->where,
			$this->group,
			$this->having,
			$this->order,
			$this->limit
		);

		foreach ($list as $index => $item)
			if (!$item)
				unset($list[ $index ]);

		return trim(implode($list, PHP_EOL));
	}

	// clear_query
	public function clear_query () {
		$this->remove = '';
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
		$this->set    = '';
	}
}

class FabricoModelInstance {
	private $data;
	private $name;

	public function __construct (stdClass $data, $name) {
		$this->data =& $data;
		$this->name = $name;
	}

	public function __get ($prop) {
		if (property_exists($this->data, $prop)) {
			return $this->data->{ $prop };
		}
	}

	public function __set ($prop, $val) {
		if (property_exists($this->data, $prop)) {
			return $this->data->{ $prop } = $val;
		}
		else {
			throw new Exception("invalid property {$prop} for model of instance {$this->name}");
		}
	}

	public function __call ($method, $args) {
		if (method_exists($this, $method)) {
			return call_user_func_array(array($this, $method), $args);
		}
		else if (method_exists($this->name, $method)) {
			return call_user_func(array($this->name, $method), $this->data);
		}
		else {
			throw new Exception("invalid method {$method} for model of instance {$this->name}");
		}
	}

	public function getdata () {
		return $this->data;
	}
}

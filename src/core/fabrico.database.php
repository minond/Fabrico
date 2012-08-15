<?php

namespace Fabrico;

trait Database {
	/**
	 * query helpers
	 */
	public static $COMMA = ',';

	/**
	 * table schema
	 *
	 * @var array
	 */
	public $table_schema;

	/**
	 * clause sections
	 *
	 * @var array
	 */
	protected $clause = array(
		'select' => '',
		'show' => '',
		'remove' => '',
		'from' => '',
		'update' => '',
		'set' => '',
		'join' => '',
		'where' => '',
		'having' => '',
		'order' => '',
		'group' => '',
		'limit' => ''
	);

	/**
	 * @var resource
	 */
	private $database_connection;

	/**
	 * @var string
	 */
	private $database_username;

	/**
	 * @var string
	 */
	private $database_password;

	/**
	 * @var string
	 */
	private $database_host;

	/**
	 * wraps a field name in quotes
	 *
	 * @param string field name
	 * @param string query safe field name
	 */
	public static function as_field ($field) {
		return "`{$field}`";
	}

	/**
	 * returns a concatenated sql statement
	 *
	 * @return string
	 */
	public function get_sql () {
		return implode("\n", array_filter(
			array_values($this->clause))
		);
	}

	/**
	 * cleans the sql clauses
	 */
	public function clear_sql () {
		foreach ($this->clause as $key => $value) {
			$this->clause[ $key ] = '';
		}
	}

	/**
	 * select clause setter
	 *
	 * @param array of fields
	 */
	public function clause_select ($fields = array()) {
		$this->clause['select'] = 'select ' . (!count($fields) ? '*' : implode(
			self::$COMMA,
			array_map(array('self', 'as_field'), $fields)
		));
	}

	/**
	 * from clause setter
	 *
	 * @param string from table name
	 */
	public function clause_from ($table) {
		$this->clause['from'] = "from {$table}";
	}

	/**
	 * where clause setter
	 *
	 * @param string of filters
	 */
	public function clause_where ($filters) {
		$this->clause['where'] = "where {$filters}";
	}

	/**
	 * where clause setter
	 *
	 * @param array of key/value pairs
	 */
	public function clause_where_equals ($filters) {
		
	}
}

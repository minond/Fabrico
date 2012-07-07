<?php

class FabricoModel {
	// singleton conneciton object
	private static $connection;

	// model's table
	protected $table;

	// query structure
	private $select;
	private $show;
	private $from;
	private $join;
	private $where;
	private $group;
	private $having;
	private $order;

	// info queries
	private $columns = 'show columns from %s';

	public function __construct () {
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

		$this->loadinfo();
	}

	public function search () {}

	public function loadinfo () {
		return self::$connection->query(sprintf($this->columns, $this->table));
	}

	public function getsql () {
		return "
		{$this->select}\n
		{$this->show}\n
		{$this->from}\n
		{$this->join}\n
		{$this->where}\n
		{$this->group}\n
		{$this->having}\n
		{$this->order}";
	}
}

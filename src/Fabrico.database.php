<?php

trait FabricoSingleDatabaseConnectionBase {
	/**
	 * @name connection
	 * @var database connection
	 */
	private $connection;

	/**
	 * @name server
	 * @var string server location
	 */
	private $server;

	/**
	 * @name username
	 * @var string server username
	 */
	private $username;

	/**
	 * @name password
	 * @var string server password
	 */
	private $password;

	/** 
	 * @name intance
	 * @var FabricoSingleDatabaseConnection
	 */
	private static $intance;

	/** 
	 * @name FabricoSingleDatabaseConnection
	 * @singleton
	 */
	private function __construct () {}

	/**
	 * @name request
	 * @return FabricoSingleDatabaseConnection singleton
	 */
	public static function request ($server, $username, $password) {
		if (!isset(self::$intance)) {
			self::$intance = new FabricoSingleDatabaseConnection;
		}

		self::$intance->set_info($server, $username, $password);
		return self::$intance;
	}

	/**
	 * @name set_info
	 * @param string server location
	 * @param string server username
	 * @param string server password
	 */
	public function set_info ($server, $username, $password) {
		$this->server = $server;
		$this->username = $username;
		$this->password = $password;
	}

	/**
	 * @name open
	 * @param string database to select
	 */
	public function open ($database) {
		if (!$this->connection) {
			$this->connection = mysql_connect(
				$this->server,
				$this->username,
				$this->password
			);
		}

		mysql_select_db($database, $this->connection);
	}

	/**
	 * @name close
	 */
	public function close () {
		if ($this->connection) {
			mysql_close($this->connection);
			$this->connection = null;
		}
	}

	/**
	 * @name query
	 * @param string sql query
	 * @param bool expect results from query
	 * @param string query response type
	 */
	public function query ($sql, $noret = false, $type = MYSQL_ASSOC) {
		$start = microtime();
		$results = mysql_query($sql, $this->connection);
		$end = microtime();
		$response = array();

		if (!$noret && $response !== false) {
			while ($row = mysql_fetch_array($results, $type)) {
				$response[] = $row;
			}
			
			mysql_free_result($results);
		}

		util::logquery($sql, $results, $end - $start);
		return $response;
	}

	/**
	 * @name last_id
	 * @return int last id added to the database
	 */
	public function last_id () {
		return mysql_insert_id($this->connection);
	}
}

class FabricoSingleDatabaseConnection {
	use FabricoSingleDatabaseConnectionBase;
}

class FabricoAdminDatabaseConnection {
	use FabricoSingleDatabaseConnectionBase;
}

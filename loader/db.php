<?php

class FabricoSingleDatabaseConnection {
	// connection information
	private $connection;
	private $server;
	private $username;
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

	public function set_info ($server, $username, $password) {
		$this->server = $server;
		$this->username = $username;
		$this->password = $password;
	}

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

	public function query ($sql, $noret = false, $type = MYSQL_ASSOC) {
		$start = microtime();
		$results = mysql_query($sql, $this->connection);
		$end = microtime();
		$response = array();

		if (!$noret && $response !== false) {
			while ($row = mysql_fetch_array($results, $type)) {
				$response[] = $row;
			}
		}

		util::logquery($sql, $results, $end - $start);
		return $response;
	}

	public function last_id () {
		return mysql_insert_id($this->connection);
	}
}

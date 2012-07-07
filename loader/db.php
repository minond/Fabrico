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

	public function query ($sql, $type = MYSQL_ASSOC) {
		$results = mysql_query($sql, $this->connection);
		$response = array();

		while ($row = mysql_fetch_array($results, $type)) {
			$response[] = $row;
		}

		$sep = "\n\t";
		util::log("query{$sep}sql: {$sql} ${sep}valid: " . ($results !== false ? 'yes' : 'no') . "{$sep}count: " . 
			($results === false ? 0 : count($results)));

		return $response;
	}
}

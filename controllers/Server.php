<?php

use fabrico\controller\Controller;
use fabrico\controller\CliAccess;
use fabrico\cli\CliSelfDocumenting;
use fabrico\cli\CliArgLoader;
use fabrico\cli\CliIo;

class Server extends Controller implements CliAccess {
	use CliSelfDocumenting, CliArgLoader, CliIo;

	/**
	 * server port number
	 * defaults to 8080
	 * @var string
	 */
	public $port = '8080';

	/**
	 * server host
	 * defaults to localhost
	 * @var string
	 */
	public $host = 'localhost';

	/**
	 * server routing file
	 * defaults to server.php in Fabrico/src
	 * @var string
	 */
	public $router = 'server.php';

	/**
	 * start php's http internal server
	 */
	public function http() {
		$this->cout(
			'Starting server: http://%s:%s, Ctrl+c to quit%s',
			$this->host, $this->port, PHP_EOL
		);

		`php -S {$this->host}:{$this->port} {$this->router}`;
	}
}

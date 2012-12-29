<?php

use fabrico\controller\Controller;
use fabrico\controller\CliAccess;
use fabrico\cli\CliSelfDocumenting;
use fabrico\cli\CliArgLoader;
use fabrico\cli\CliIo;

class Server extends Controller implements CliAccess {
	use CliSelfDocumenting, CliArgLoader, CliIo;

	/**
	 * default
	 */
	public function trigger($port = '8080', $host = 'localhost', $router = 'server.php') {
		$this->http($port, $host, $router);
	}

	/**
	 * start php's http internal server
	 */
	public function http($port = '8080', $host = 'localhost', $router = 'server.php') {
		$this->cout(
			'Starting server: http://%s:%s, Ctrl+c to quit%s',
			$host, $port, PHP_EOL
		);

		`php -S {$host}:{$port} {$router}`;
	}
}

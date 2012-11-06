<?php

/**
 * @package fabrico\logz
 */
namespace fabrico\logz;

/**
 * main log interface
 */
class Logz {
	/**
	 * log levels
	 */
	const DEBUG = 0;
	const INFO = 1;
	const WARN = 2;
	const ERROR = 3;
	const CRITICAL = 4;

	/**
	 * level names
	 * @param array
	 */
	public static $lvlnames = [
		self::INFO => 'INFORMATION',
		self::WARN => 'WARNING',
		self::ERROR => 'ERROR',
		self::DEBUG => 'DEBUG',
		self::CRITICAL => 'CRITICAL'
	];

	/**
	 * log identifier
	 * @var string
	 */
	private $name;

	/**
	 * output handlers
	 * @var array
	 */
	private $handlers = [];

	/**
	 * log format
	 * @var string
	 */
	private $format = '[%d] %n: %s';

	/**
	 * @param string $name
	 */
	public function __construct ($name) {
		$this->name = $name;
	}

	/**
	 * format setter
	 * @param string $format
	 */
	public function set_format ($format) {
		$this->format = $format;
	}

	/**
	 * add a handler
	 * @param LogzHandler $handler
	 */
	public function add_handler (handler\LogzHandler $handler) {
		$this->handlers[] = $handler;

		if (!$handler->format) {
			$handler->format = $this->format;
		}
	}

	/**
	 * find handlers of a level and send a message
	 * @param string $msg
	 * @param int $level
	 */
	private function find_and_send ($msg, $level) {
		$name = self::$lvlnames[ $level ];

		foreach ($this->handlers as & $handler) {
			if ($handler->handles($level)) {
				$message = $this->format_string($handler->format, $msg, $name);
				$handler->start();
				$handler->message($message);
			}

			unset($handler);
		}
	}

	/**
	 * formats a log string
	 * @param string $format
	 * @param string $msg
	 * @param string $name
	 * @return string
	 */
	private function format_string ($format, $msg, $name) {
		return str_replace(
			[ '%d', '%n', '%s' ],
			[ date('Y-m-d H:i:s'), $name, $msg ],
			$format
		);
	}

	/**
	 * calls stop on all of its handlers
	 */
	public function stop () {
		foreach ($this->handlers as & $handler) {
			$handler->stop();
			unset($handler);
		}
	}

	/**
	 * @param string $msg
	 */
	public function information ($msg) {
		$this->find_and_send($msg, self::INFO);
	}

	/**
	 * @param string $msg
	 */
	public function warning ($msg) {
		$this->find_and_send($msg, self::WARN);
	}

	/**
	 * @param string $msg
	 */
	public function error ($msg) {
		$this->find_and_send($msg, self::ERROR);
	}

	/**
	 * @param string $msg
	 */
	public function debug ($msg) {
		$this->find_and_send($msg, self::DEBUG);
	}

	/**
	 * @param string $msg
	 */
	public function critical ($msg) {
		$this->find_and_send($msg, self::CRITICAL);
	}
}

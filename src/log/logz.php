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
	const INFO = 0;
	const WARN = 1;
	const ERROR = 2;
	const DEBUG = 3;
	const CRITICAL = 4;

	/**
	 * level dictionary
	 * @var array
	 */
	private static $lvlmap = [
		'information' => self::INFO,
		'warning' => self::WARN,
		'error' => self::ERROR,
		'debug' => self::DEBUG,
		'critical' => self::CRITICAL
	];

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
	 * trigger a message of any type
	 * @param string $level
	 * @param array $args
	 */
	public function __call ($level, array $args) {
		if (array_key_exists($level, self::$lvlmap)) {
			$this->find_and_send($args[ 0 ], self::$lvlmap[ $level ]);
		}
	}
}

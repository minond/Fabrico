<?php

/**
 * @package fabrico\logging\handler
 */
namespace fabrico\logging\handler;

/**
 * handler interface
 */
abstract class LogzHandler {
	/**
	 * handler started
	 * @var boolean
	 */
	private $opened = false;

	/**
	 * log format, set by Logz if empty
	 * @var string
	 */
	public $format;

	/**
	 * message level
	 * @var int
	 */
	public $level;

	/**
	 * @param int $level
	 */
	public function __construct ($level) {
		$this->level = $level;
	}

	/**
	 * opens a handler is needed
	 */
	final public function start () {
		if (!$this->opened) {
			$this->open();
			$this->opened = true;
		}
	}

	/**
	 * closes a handler if opened
	 */
	final public function stop () {
		if ($this->opened) {
			$this->close();
		}
	}

	/**
	 * by default only works with messages of current type
	 * @param int $level
	 * @return boolean
	 */
	public function handles ($level) {
		return $this->level === $level;
	}

	/**
	 * virtual
	 * called when Logz is ready to start sending messages
	 */
	public function open () {}

	/**
	 * virtual
	 * called after Logz is done logging messages
	 */
	public function close () {}

	/**
	 * virtual
	 * writes to desired output
	 * @param string $msg
	 */
	abstract public function message ($msg);
}

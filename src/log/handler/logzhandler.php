<?php

/**
 * @package fabrico\logz\handler
 */
namespace fabrico\logz\handler;

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
	 * can I handle this message?
	 * by default only works with messages of current type
	 * @param int $level
	 * @return boolean
	 */
	public function handles ($level) {
		return $this->level === $level;
	}

	public function parse ($msg) {
		
	}

	/**
	 * called when Logz is ready to start sending messages
	 */
	abstract public function open ();

	/**
	 * called after Logz is done logging messages
	 */
	abstract public function close ();

	/**
	 * virtual method
	 * writes to desired output
	 * @param string $msg
	 */
	abstract public function message ($msg);
}

<?php

/**
 * @package fabrico\logz\handler
 */
namespace fabrico\logz\handler;

/**
 * log to a file
 */
class FileHandler extends LogzHandler {
	/**
	 * file to output logs to
	 * @var resource
	 */
	private $filehandler;

	/**
	 * file to output logs to
	 * @var string
	 */
	private $filename;

	/**
	 * @param int $level
	 * @param string $filename
	 */
	public function __construct ($level, $filename) {
		$this->level = $level;
		$this->filename = $filename;
	}

	/**
	 * opens the file
	 */
	public function open () {
		$this->filehandler = fopen($this->filename, 'a');
	}

	/**
	 * closes the file
	 */
	public function close () {
		fclose($this->filehandler);
	}

	/**
	 * writes to file
	 * @param string $msg
	 */
	public function message ($msg) {
		fwrite($this->filehandler, $msg . PHP_EOL);
	}
}

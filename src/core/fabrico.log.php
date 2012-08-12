<?php

namespace Fabrico;

class Logger {
	/**
	 * log types
	 */
	const ERROR = 'error';
	const BUILD = 'build';
	const QUERY = 'query';
	const DEBUG = 'debug';
	const REQUEST = 'request';

	/**
	 * log signature
	 *
	 * @var string
	 */
	public static $signature = "\n[#{guid} - #{time}] #{type} - ";

	/**
	 * append contents to a log file
	 *
	 * @param string file name
	 * @param string content
	 */
	private static function append ($file, $content) {
		$file = Project::get_log_file($file);
		$file = @fopen($file, 'a');

		if (is_resource($file)) {
			fwrite($file, $content);
			fclose($file);
		}
	}

	/**
	 * generates a new log signature
	 *
	 * @return string
	 */
	private static function signature ($type) {
		return Merge::parse(self::$signature, array(
			'guid' => Core::$configuration->state->guid,
			'time' => date('Y-m-d H:m:s'),
			'type' => str_pad(strtoupper($type), 7)
		));
	}

	/**
	 * standard message log
	 *
	 * @param string log type
	 * @param string log message
	 */
	private static function std_log ($type, $msg) {
		self::append($type, self::signature($type) . $msg);
	}

	/**
	 * debugging log
	 *
	 * @param string log message
	 */
	public static function debug ($msg) {
		self::std_log(self::DEBUG, $msg);
	}

	/**
	 * errror log
	 *
	 * @param string log message
	 */
	public static function error ($msg) {
		self::std_log(self::ERROR, $msg);
	}

	/**
	 * query log
	 *
	 * @param string log message
	 */
	public static function query ($msg) {
		self::std_log(self::QUERY, $msg);
	}

	/**
	 * build error/message log
	 *
	 * @param string log message
	 */
	public static function build ($msg) {
		self::std_log(self::BUILD, $msg);
	}

	/**
	 * request log
	 *
	 * @param string log message
	 */
	public static function request ($msg) {
		self::std_log(self::REQUEST, $msg);
	}
}

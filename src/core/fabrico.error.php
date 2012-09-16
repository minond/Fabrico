<?php

namespace Fabrico;

class Error {
	const TITLE_ERROR = 'Error';
	const TITLE_WARNINIG = 'Warninig';
	const TITLE_NOTICE = 'Notice';
	const TITLE_STRICT = 'Strict';
	const TITLE_EXCEPTION = 'Uncaught Exception';
	const TITLE_DEFAULT = 'Error Type: ';

	public static function message ($msg) {
		$db = debug_backtrace();
		$info = array_shift($db);
		self::output_error(E_WARNING, $msg, $info['file'], $info['line']);
	}

	/**
	 * handles errors, warnings, and notices
	 *
	 * @param integer error type
	 * @param string error message
	 * @param string file name
	 * @param integer line number
	 */
	public static function output_error ($type, $message, $file, $line) {
		switch ($type) {
			case E_ERROR:
			case E_USER_ERROR:
				$title = self::TITLE_ERROR;
				break;

			case E_WARNING:
			case E_USER_WARNING:
				$title = self::TITLE_WARNINIG;
				break;

			case E_NOTICE:
			case E_USER_NOTICE:
				$title = self::TITLE_NOTICE;
				break;

			case E_STRICT:
				$title = self::TITLE_STRICT;
				break;

			default:
				$title = self::TITLE_DEFAULT . $type;
				break;
		}

		self::output_to_view($title, $message, $file, $line);
		self::output_to_logs($title, $message, $file, $line);
	}

	/**
	 * handles uncaught exceoptions
	 *
	 * @param Exception error
	 */
	public static function output_exception ($error) {
		$title = self::TITLE_EXCEPTION;
		$message = $error->getMessage();
		$file = $error->getFile();
		$line = $error->getLine();
		$trace = $error->getTrace();
		$oldcontent = ob_get_clean();

		self::output_to_logs($title, $message, $file, $line);
		self::output_to_view($title, $message, $file, $line);

		echo implode('', Page::get_errors());
		echo html::pre([
			'content' => print_r(debug_backtrace(), true)
		]);
	}

	/**
	 * logs an error
	 *
	 * @param integer error type
	 * @param string error message
	 * @param string file name
	 * @param integer file line number
	 */
	private static function output_to_logs ($title, $message, $file, $line) {
		Logger::error("type: {$title}");
		Logger::error("message: {$message}");
		Logger::error("file: {$file}");
		Logger::error("line: {$line}");
	}

	/**
	* adds an error output to the page module
	*
	* @param integer error type
	* @param string error message
	* @param string file name
	* @param integer line number
	*/
	private static function output_to_view ($title, $message, $file, $line) {
		\view\element('error/error');
		ob_start();

		\view\error\error::generate([
			'title' => $title,
			'message' => $message,
			'file' => $file,
			'line' => $line
		]);

		Page::include_error_message(ob_get_clean());
	}

	/*
	public static function handle_fatal_error () {
		$sep = 'Fatal error';
		$errors = ob_get_clean();
		$errors = $sep . explode($sep, $errors, 2)[ 1 ];
		$errors = gzcompress($errors);
	}
	*/
}

set_error_handler('\Fabrico\Error::output_error');
set_exception_handler('\Fabrico\Error::output_exception');
// register_shutdown_function('\Fabrico\Error::handle_fatal_error');

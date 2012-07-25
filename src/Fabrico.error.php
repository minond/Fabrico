<?php

set_error_handler('FabricoError::output_error');
register_shutdown_function('FabricoError::output_shutdown');
set_exception_handler('FabricoError::output_exception');

class FabricoError {
	const DELIM = '|';

	private static $list = array();

	public static function getall () {
		$errors = '';

		if (count(self::$list)) {
			$errors = implode('', self::$list);
		}

		return $errors;
	}

	private static function output_to_logs ($type, $message, $file, $line, $trace = array()) {
		util::loglist(strtolower($type), array(
			'msg' => $message,
			'file' => $file,
			'line' => $line,
			'trace' => print_r($trace, true)
		), Fabrico::FILE_ERROR);
	}

	private static function output_to_view ($type, $message, $file, $line) {
		if (Fabrico::is_view_request()) {
			element('error/error');
			ob_start();

			error\error::merge(array(
				'type' => $type,
				'message' => $message,
				'file' => $file,
				'line' => $line
			));

			self::$list[] = ob_get_clean();
		}
	}

	private static function fatal_error_redirect ($error) {
		$error = self::error_encode($error);
		header('Location: /errorcaptured?' . Fabrico::$uri_query_error . "={$error}");
	}

	private static function error_encode ($error) {
		$error = (object) $error;
		$error = base64_encode(implode(self::DELIM, array(
			$error->line,
			$error->file,
			$error->message
		)));

		return $error;
	}

	public static function error_decode ($str) {
		$error = base64_decode($str);
		$error = explode(self::DELIM, $error);

		return $error;
	}

	public static function output_shutdown () {
		$error = error_get_last();

		if (!Fabrico::is_debugging()) {
			if (count($error)) {
				// @ob_get_clean();
				// self::fatal_error_redirect($error);
			}
		}
	}

	public static function output_error ($type, $message, $file, $line) {
		switch ($type) {
			case E_ERROR:
			case E_USER_ERROR:
				$title = 'Error';
				break;

			case E_WARNING:
			case E_USER_WARNING:
				$title = 'Warning';
				break;

			case E_NOTICE:
			case E_USER_NOTICE:
				$title = 'Notice';
				break;

			case E_STRICT:
				$title = 'Strict Use Warning';
				break;

			default:
				$title = 'Error Type: ' . $type;
				break;
		}

		self::output_to_logs($title, $message, $file, $line);
		self::output_to_view($title, $message, $file, $line);
	}

	public static function output_exception ($error) {
		$title = 'Uncaught Exception';
		$message = $error->getMessage();
		$file = $error->getFile();
		$line = $error->getLine();
		$trace = $error->getTrace();

//		self::output_to_logs($title, $message, $file, $line, $trace);
		self::output_to_view($title, $message, $file, $line);
		echo self::getall();
	}
}

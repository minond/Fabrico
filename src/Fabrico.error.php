<?php

set_error_handler('FabricoError::output_error');
set_exception_handler('FabricoError::output_exception');

class FabricoError {
	private static $list = array();

	public static function getall () {
		return implode('', self::$list);
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

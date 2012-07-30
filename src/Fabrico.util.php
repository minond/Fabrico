<?php

/**
 * generatal function helpers and shortcuts
 *
 * @name util
 */
class util {
	// output and log settings
	public static $out_delm = "\n\n";
	public static $out_wrap = '<pre>%s</pre>';
	public static $log_wrap = "%s ~ [%s.%s] %s project - %s\n";
	public static $log_date = 'Y-m-d H:i:s';

	/**
	 * @name can_log
	 * @return true if request can log data
	 */
	public static function can_log () {
		return Fabrico::is_debugging();
	}

	/**
	 * @name can_output
	 * @return bool true is request is able to output text
	 */
	public static function can_output () {
		return self::can_log() &&
		       !Fabrico::is_method_request() &&
		       !Fabrico::is_action_request();
	}

	/**
	 * returns a boolean in string form
	 *
	 * @name bool2string
	 * @param boolean value check
	 * @return string
	 */
	public static function bool2string ($val = false) {
		return isset($val) && $val === true ? 'true' : 'false';
	}

	/**
	 * @name prepare_output
	 * @param mixed* list of variable to output/log
	 * @return array of items
	 */
	private static function prepare_output () {
		$out = array();

		for ($i = 0, $max = func_num_args(); $i < $max; $i++) {
			$val = func_get_arg($i);

			if (is_array($val) || is_object($val)) {
				ob_start();
				var_dump($val);
				$out[] = ob_get_contents();
				ob_end_clean();
			}
			else {
				$out[] = print_r($val, true);
			}
		}

		return $out;
	}

	/** 
	 * @name cout
	 * @param mixed* output
	 * formats and outputs
	 */
	public static function cout () {
		if (!self::can_output()) {
			return false;
		}

		$out = call_user_func_array(
			array('self', 'prepare_output'),
			func_get_args()
		);

		$out = implode($out, self::$out_delm);
		echo html::el('pre', array(
			'content' => $out, 
			'style' => html::style(array(
				'cursor' => 'default',
				'white-space' => 'pre-wrap',
				'font-size' => '11px'
			))
		));
	}

	/**
	 * @name coutd
	 * @param mixed* output
	 * formats, outputs, then ends script
	 */
	public static function coutd () {
		if (!self::can_output()) {
			return false;
		}

		call_user_func_array(
			array('self', 'cout'),
			func_get_args()
		);

		die;
	}

	/**
	 * @name append
	 * @param filename
	 * @param output text
	 */
	private static function append ($filename, $output) {
		if (!self::can_log()) {
			return false;
		}

		$project = Fabrico::get_config()->project->info->name;
		$output = call_user_func_array(array('self', 'prepare_output'), $output);

		list(, $micro) = explode('.', microtime(true));
		$micro = str_pad($micro, 4, '0');

		if (file_exists($filename)) {
			$file = fopen($filename, 'a');
			$text = implode($output, self::$out_delm);
			$logtext = sprintf(self::$log_wrap, Fabrico::get_id(), date(self::$log_date), $micro, $project, $text);

			fwrite($file, $logtext);
			fclose($file);
		}
	}

	/**
	 * @name log
	 * @param mixed* log output
	 * outputs items to a log file
	 */
	public static function log () {
		self::append(
			Fabrico::get_log_file(Fabrico::FILE_LOG),
		func_get_args());
	}

	/**
	 * @name loglist
	 * @param string list title
	 * @param array list items
	 * @see log
	 */
	public static function loglist ($title, $items, $file = false) {
		$str = $title;

		foreach ($items as $key => $value) {
			$str .= "\n\t{$key}:\t{$value}";
		}

		self::append(Fabrico::get_log_file(
			$file ? $file : Fabrico::FILE_LOG
		), array($str));
	}

	/**
	 * @name log_query
	 * @param string sql query
	 * @param array query results
	 * @param int time
	 * @see log
	 */
	public static function logquery ($sql, & $results, $time) {
		$sql = str_replace(array("\n", "\r"), ' ', $sql);
		$valid = $results !== false;
		$count = $valid ? count($results) : 0;
		$valid = $valid ? 'yes' : 'no';
		$time = round($time, 7);

		self::loglist('query', array(
			'sql' => $sql,
			'valid' => $valid,
			'rows' => $count,
			'time' => $time
		), Fabrico::FILE_QUERY);
	}

	/**
	 * @name logfatalerror
	 * @see log
	 */
	public static function logfatalerror () {
		$error = FabricoError::error_decode(Fabrico::req(
			Fabrico::$uri_query_error
		));

		if (is_array($error)) {
			$log = array(
				'error' => $error[2],
				'file' => $error[1],
				'line' => $error[0]
			);

			self::loglist('fatal error', $log, Fabrico::FILE_ERROR);
		}
	}

	/**
	 * @name is_hash
	 * @param array reference
	 * @return boolean
	 */
	public static function is_hash (& $arr) {
		if (is_array($arr) || is_object($arr)) {
			foreach ($arr as $key => $value) {
				if (!is_int($key)) {
					return true;
				}
			}
		}

		return false;
	}
}

/**
 * session getter and setter using static methods
 *
 * @name session
 */
class session {
	public static function __callStatic ($name, $args) {
		if (count($args)) {
			$_SESSION[ $name ] = $args[ 0 ];
		}

		return Fabrico::ses($name);
	}
}

/**
 * request parameter getter and setter using static methods
 *
 * @name param
 */
class param {
	public static function __callStatic ($name, $args) {
		if (count($args)) {
			Fabrico::$req[ $name ] = $args[ 0 ];
		}

		return Fabrico::req($name);
	}
}

/**
 * encryption and decryption helper
 *
 * @name scrypt
 */
class scrypt {
	public static function en ($str, $key) {
		return base64_encode(
			mcrypt_encrypt(
				MCRYPT_RIJNDAEL_256, md5($key), $str, MCRYPT_MODE_CBC, md5(md5($key))
			)
		);
	}

	public static function de ($str, $key) {
		return rtrim(
			mcrypt_decrypt(
				MCRYPT_RIJNDAEL_256, md5($key), base64_decode($str), MCRYPT_MODE_CBC, md5(md5($key))
			), "\0"
		);
	}
}

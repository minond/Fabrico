<?php

Fabrico::check_debugging();

class util {
	// output and log settings
	public static $out_delm = "\n\n";
	public static $out_wrap = '<pre>%s</pre>';
	public static $log_wrap = "[%s.%s] %s project - %s\n";
	public static $log_date = 'Y-m-d H:i:s';

	private static function can_log () {
		return Fabrico::is_debugging();
	}

	private static function can_output () {
		return self::can_log() &&
		       !Fabrico::is_method_request() &&
		       !Fabrico::is_action_request();
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
		echo HTML::el('pre', array(
			'content' => $out, 
			'style' => HTML::style(array(
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
	 * @name log
	 * @param mixed* log output
	 * outputs items to a log file
	 */
	public static function log () {
		if (!self::can_log()) {
			return false;
		}

		$project = Fabrico::get_config()->project->info->name;
		$filename = Fabrico::get_log_file();
		list(, $micro) = explode('.', microtime(true));
		$micro = str_pad($micro, 4, '0');
		$out = call_user_func_array(
			array('self', 'prepare_output'),
			func_get_args()
		);

		if (file_exists($filename)) {
			$file = fopen($filename, 'a');
			$text = implode($out, self::$out_delm);
			$logtext = sprintf(self::$log_wrap, date(self::$log_date), $micro, $project, $text);
			fwrite($file, $logtext);
			fclose($file);
		}
	}

	/**
	 * @name loglist
	 * @param string list title
	 * @param array list items
	 * @see log
	 */
	public static function loglist ($title, $items) {
		$str = $title;

		foreach ($items as $key => $value) {
			$str .= "\n\t{$key}:\t{$value}";
		}

		self::log($str);
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
		));
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

<?php

class util {
	// output settings
	public static $out_delim = "\n\n";
	public static $out_wrap = '<pre>%s</pre>';

	// log settings
	public static $log_file;
	public static $log_wrap = "\n[%s:%s]: %s\n";
	public static $log_date = 'Y-m-d H:i:s';

	public static function prepare_output () {
		$out = array();

		for ($i = 0, $max = func_num_args(); $i < $max; $i++) {
			$out[] = print_r(func_get_arg($i), true);
		}

		return $out;
	}

	public static function cout () {
		$out = call_user_func_array(
			array('self', 'prepare_output'),
			func_get_args()
		);

		echo sprintf(self::$out_wrap, implode($out, self::$out_delim));
	}

	public static function coutd () {
		call_user_func_array(
			array('self', 'cout'),
			func_get_args()
		);

		die;
	}

	public static function log () {
		$project = mFrame::get_config()->project->name;
		$filename = mFrame::get_file_path(self::$log_file);
		$out = call_user_func_array(
			array('self', 'prepare_output'),
			func_get_args()
		);

		if (file_exists($filename)) {
			$file = fopen($filename, 'a');
			$text = implode($out, self::$out_delim);
			$logtext = sprintf(self::$log_wrap, $project, date(self::$log_date), $text);
			fwrite($file, $logtext);
			fclose($file);
		}
	}
}

// standard directory	
util::$log_file = "{$directory->logs}/debug.log";

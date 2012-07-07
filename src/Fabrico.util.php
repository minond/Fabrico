<?php

class util {
	// output settings
	public static $out_delim = "\n\n";
	public static $out_wrap = '<pre>%s</pre>';

	// log settings
	public static $log_wrap = "%s %s project: %s\n";
	public static $log_date = 'Y-m-d H:i:s';

	/**
	 * @name prepare_output
	 * @param mixed* list of variable to output/log
	 * @return array of items
	 */
	private static function prepare_output () {
		$out = array();

		for ($i = 0, $max = func_num_args(); $i < $max; $i++) {
			$out[] = print_r(func_get_arg($i), true);
		}

		return $out;
	}

	/** 
	 * @name cout
	 * @param mixed* output
	 * formats and outputs
	 */
	public static function cout () {
		if (!Fabrico::is_debugging()) {
			return false;
		}

		$out = call_user_func_array(
			array('self', 'prepare_output'),
			func_get_args()
		);

		$out = implode($out, self::$out_delim);
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
		if (!Fabrico::is_debugging()) {
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
		if (!Fabrico::is_debugging()) {
			return false;
		}

		$project = Fabrico::get_config()->project->info->name;
		$filename = Fabrico::get_log_file();
		$out = call_user_func_array(
			array('self', 'prepare_output'),
			func_get_args()
		);

		if (file_exists($filename)) {
			$file = fopen($filename, 'a');
			$text = implode($out, self::$out_delim);
			$logtext = sprintf(self::$log_wrap, date(self::$log_date), $project, $text);
			fwrite($file, $logtext);
			fclose($file);
		}
	}
}

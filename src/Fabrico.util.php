<?php

class util {
	// output settings
	public static $out_delim = "\n\n";
	public static $out_wrap = '<pre>%s</pre>';

	// log settings
	public static $log_wrap = "%s %s project: %s\n";
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

	public static function coutd () {
		call_user_func_array(
			array('self', 'cout'),
			func_get_args()
		);

		die;
	}

	public static function log () {
		$project = Fabrico::get_config()->project->name;
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

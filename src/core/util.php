<?php

namespace fabrico;

class util extends Module {
	/**
	 * prints arguments then kill script
	 * @param mixed $output*
	 */
	public static function dpre ($output) {
		echo '<pre>';

		foreach (func_get_args() as $arg) {
			print_r($arg);
		}

		echo '</pre>';
		die;
	}
}

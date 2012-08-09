<?php

namespace Fabrico;

class Page {
	/**
	 * starts capturing output on buffer
	 */
	public static function open () {
		ob_start();
	}

	/**
	 * outputs raw buffer
	 */
	public static function close () {
		$html = ob_get_clean();
		echo $html;
	}
}

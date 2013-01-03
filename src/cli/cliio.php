<?php

/**
 * @package fabrico\cli
 */
namespace fabrico\cli;

/**
 * provides console input and output
 */
trait CliIo {
	private $checkmark = '✔';
	private $crossmark = '❌';

	/**
	 * TODO: implement options
	 * std::cin
	 * @param string $message
	 * @param array $options
	 * @return mixed
	 */
	private function cin($message, array $options = null) {
		return fgets(STDIN);
	}

	/**
	 * std::cout
	 * @param string $message
	 */
	private function cout($message) {
		fwrite(STDOUT, call_user_func_array('sprintf', func_get_args()));
		echo PHP_EOL;
	}

	/**
	 * std::cout
	 * @param string $message
	 */
	private function out($message) {
		fwrite(STDOUT, call_user_func_array('sprintf', func_get_args()));
	}

	/**
	 * cli number of rows and columns
	 * @return object
	 */
	private function get_cli_dims() {
		return (object) [
			'rows' => (int) `tput lines`,
			'cols' => (int) `tput cols`,
		];
	}

	/**
	 * color output helper
	 * @param string $text
	 * @param string $color
	 * @return string
	 */
	private function c_color($text, $color) {
		return "\033[{$color}m{$text}\033[0m";
	}

	/**
	 * @param string $text
	 */
	private function cbold($text) {
		return $this->c_color($text, '1');
	}

	/**
	 * @param string $text
	 */
	private function cunderline($text) {
		return $this->c_color($text, '4');
	}

	/**
	 * @param string $text
	 */
	private function cfred($text) {
		return $this->c_color($text, '0;31');
	}

	/**
	 * @param string $text
	 */
	private function cfgreen($text) {
		return $this->c_color($text, '0;32');
	}

	/**
	 * @param string $text
	 */
	private function cfblue($text) {
		return $this->c_color($text, '0;34');
	}

	/**
	 * @param string $text
	 */
	private function cbred($text) {
		return $this->c_color($text, '41');
	}

	/**
	 * @param string $text
	 */
	private function cbgreen($text) {
		return $this->c_color($text, '42');
	}

	/**
	 * @param string $text
	 */
	private function cbblue($text) {
		return $this->c_color($text, '44');
	}
}

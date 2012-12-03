<?php

/**
 * @package fabrico\cli
 */
namespace fabrico\cli;

/**
 * provides console input and output
 */
trait CliIo {
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
	}
}

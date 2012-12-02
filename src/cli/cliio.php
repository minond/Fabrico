<?php

/**
 * @package fabrico\cli
 */
namespace fabrico\cli;

trait CliIo {
	private function cin($message, array $options = null) {
		return fgets(STDIN);
	}

	private function cout($message) {
		fwrite(STDOUT, call_user_func_array('sprintf', func_get_args()));
	}
}

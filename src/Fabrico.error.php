<?php

set_exception_handler('FabricoError::output');

class FabricoError {
	public function output ($error) {
		$log = array(
			'msg' => $error->getMessage(),
			'file' => $error->getFile(),
			'line' => $error->getLine(),
			'trace' => $error->getTrace()
		);
		util::cout('Error', $log);
	}
}

<?php

namespace fabrico;

class DepsLoader extends Loader {
	protected $files = [
		'yml' => [
			'sfYaml/sfYaml.php',
		]
	];

	public function __construct () {
		$this->format('yml', [ $this, 'conf_dep_file' ]);
	}

	public function conf_dep_file ($file) {
		print_r($this->core);
		die;
		return "../../../admin/php_include/{$file}";
	}
}

<?php

namespace fabrico;

class CoreLoader extends Loader {
	protected $files = [
		'core' => [
			'router',
			'configuration',
			'event',
			'project',
			'loader.deps'
		]
	];

	public function __construct () {
		$this->format('core', [ $this, 'std_core_file' ]);
	}

	public function std_core_file ($file) {
		return "fabrico.{$file}.php";
	}
}

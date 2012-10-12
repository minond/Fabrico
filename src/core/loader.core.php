<?php

namespace fabrico;

class CoreLoader extends Loader {
	protected $files = [
		'core' => [
			'configuration.items',
			'configuration.manager',
			'configuration.core',
			'router',
			'event',
			'project',
			'reader'
		]
	];

	public function __construct () {
		$this->format('core', [ $this, 'std_core_file' ]);
	}

	public function std_core_file ($file) {
		return "{$file}.php";
	}
}

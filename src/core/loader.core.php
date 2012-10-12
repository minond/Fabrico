<?php

namespace fabrico;

class CoreLoader extends Loader {
	protected $files = [
		'core' => [
			'configuration.item',
			'configuration.items',
			'configuration.manager',
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

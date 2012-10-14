<?php

namespace fabrico;

class CoreLoader extends Loader {
	protected $files = [
		'observer' => [
			'public',
			'observation'
		],
		'configuration' => [
			'item',
			'items',
			'manager',
		],
		'core' => [
			'router',
			'event',
			'project',
			'reader'
		]
	];

	public function core_format ($file) {
		return "core/{$file}.php";
	}

	public function configuration_format ($file) {
		return "configuration/{$file}.php";
	}

	public function observer_format ($file) {
		return "observer/{$file}.php";
	}
}

<?php

/**
 * @package fabrico\loader
 */
namespace fabrico\loader;

/**
 * core file loader
 */
class CoreLoader extends Loader {
	protected $files = [
		'observer' => [
			'public',
			'observation'
		],
		'configuration' => [
			'item',
			'items',
			'configuration',
		],
		'core' => [
			'router',
			'request',
			'event',
			'reader',
			'project'
		],
		'page' => [
			'tag',
			'build',
			'page',
			'lexer',
			'token',
			'propertytoken',
			'tagtoken',
			'mergetoken',
			'parser',
			'view'
		],
		'error' => [
			'exception'
		]
	];

	public function core_format ($file) {
		return "../core/{$file}.php";
	}

	public function configuration_format ($file) {
		return "../configuration/{$file}.php";
	}

	public function observer_format ($file) {
		return "../observer/{$file}.php";
	}

	public function page_format ($file) {
		return "../page/{$file}.php";
	}

	public function error_format ($file) {
		return "../error/{$file}.php";
	}
}

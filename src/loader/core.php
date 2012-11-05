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
			'response',
			'event',
			'reader',
			'project'
		],
		'page' => [
			'build',
			'page',
			'tag',
			'view',
			// NOTE: move these out
			'token',
			'tagtoken',
			// NOTE: this one is questionable
			'mergetoken'
		],
		'parse' => [
			'parser',
			'lexer',
			'token',
			'tagtoken',
			'propertytoken',
			'mergetoken'
		],
		'controller' => [
			'controller'
		],
		'error' => [
			'exception'
		],
		'log' => [
			'logz',
			'handler/logzhandler',
			'handler/filehandler'
		]
	];

	protected $autoload = ['core', 'configuration', 'error'];

	public function log_format ($file) {
		return "../log/{$file}.php";
	}

	public function core_format ($file) {
		return "../core/{$file}.php";
	}

	public function controller_format ($file) {
		return "../controller/{$file}.php";
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

	public function parse_format ($file) {
		return "../page/{$file}.php";
	}

	public function error_format ($file) {
		return "../error/{$file}.php";
	}
}

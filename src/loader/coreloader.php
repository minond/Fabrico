<?php

/**
 * @package fabrico\loader
 */
namespace fabrico\loader;

/**
 * core file loader
 */
class CoreLoader extends Loader {
	protected $autoload = [
		'core',
		'configuration',
		'error',
		'cache',
	];

	protected $files = [
		'configuration' => [
			'jsonreader',
			'routingrule',
			'item',
			'items',
			'configuration',
		],
		'core' => [
			'router',
			'request',
			'response',
			'event',
			'project'
		],
		'output' => [
			'outputcontent',
			'json'
		],
		'page' => [
			'page/page',
			'page/build',
			'page/tag',
			'page/view',
			// TODO: move into perse namespace
			'token/token',
			'token/tagtoken',
			'token/mergetoken'
		],
		'parse' => [
			'parser/parser',
			'parser/lexer',
			// TODO: move out of output namespace
			// 'token/token',
			// 'token/tagtoken',
			// 'token/mergetoken',
			'token/propertytoken'
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
		],
		'cache' => [
			'cache',
			'apc',
			'session'
		],
	];

	public function log_format ($file) {
		return sprintf('%slog/%s.php', $this->path_to, $file);
	}

	public function core_format ($file) {
		return sprintf('%score/%s.php', $this->path_to, $file);
	}

	public function controller_format ($file) {
		return sprintf('%scontroller/%s.php', $this->path_to, $file);
	}

	public function configuration_format ($file) {
		return sprintf('%sconfiguration/%s.php', $this->path_to, $file);
	}

	public function page_format ($file) {
		return sprintf('%soutput/%s.php', $this->path_to, $file);
	}

	public function output_format ($file) {
		return sprintf('%soutput/%s.php', $this->path_to, $file);
	}

	public function parse_format ($file) {
		return sprintf('%soutput/%s.php', $this->path_to, $file);
	}

	public function error_format ($file) {
		return sprintf('%serror/%s.php', $this->path_to, $file);
	}

	public function cache_format ($file) {
		return sprintf('%scache/%s.php', $this->path_to, $file);
	}
}

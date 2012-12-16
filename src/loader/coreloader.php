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
		'error',
		'cache',
		'configuration',
		'project',
	];

	protected $files = [
		'klass' => [
			'docparser',
			'nicestring',
		],
		'configuration' => [
			'configurationmanager',
			'configurationreader',
			'routingrule',
			'standarditem',
		],
		'core' => [
			'router',
			'request',
			'response',
			'event',
		],
		'project' => [
			'project',
			'filefinder',
		],
		'output' => [
			'outputcontent',
			'json',
			'html',
		],
		'page' => [
			'page/page',
			'page/build',
			'page/tag',
			'page/view',
			// TODO: move into perse namespace
			'token/token',
			'token/tagtoken',
			'token/mergetoken',
			'token/functiontoken',
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
			'controller',
			'cliaccess',
		],
		'error' => [
			'logged'
		],
		'log' => [
			'logz',
			'handler/logzhandler',
			'handler/filehandler'
		],
		'cache' => [
			'cache',
			'apc',
			'session',
			'runtimememory',
			'flash',
		],
		'model' => [
			'getset',
			'model',
			'abstractmodel',
			'webaccess',
			'sessionmodel',
			'runtimememorymodel',
			'modelform',
			'modelformfield',
		],
		'cli' => [
			'cliselfdocumenting',
			'cliargloader',
			'cliio',
		]
	];

	public function klass_format ($file) {
		return sprintf('%sklass/%s.php', $this->path_to, $file);
	}

	public function cli_format ($file) {
		return sprintf('%scli/%s.php', $this->path_to, $file);
	}

	public function log_format ($file) {
		return sprintf('%slog/%s.php', $this->path_to, $file);
	}

	public function project_format ($file) {
		return sprintf('%sproject/%s.php', $this->path_to, $file);
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

	public function model_format ($file) {
		return sprintf('%smodel/%s.php', $this->path_to, $file);
	}
}

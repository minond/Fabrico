<?php

/**
 * @package fabrico\loader
 */
namespace fabrico\loader;

/**
 * dependency loader
 */
class DepsLoader extends Loader {
	/**
	 * yml readers
	 */
	const YML = 'yml';

	/**
	 * @var array
	 */
	protected $files = [
		'yml' => [
			'sfYaml/sfYaml.php',
		]
	];

	/**
	 * deps file formatter
	 * @return string
	 */
	public function yml_format ($file) {
		return $this->path_to . $file;
	}
}

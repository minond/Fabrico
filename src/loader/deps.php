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
	 * deps base path
	 * @var string
	 */
	private $path_to;

	/**
	 * @var array
	 */
	protected $files = [
		'yml' => [
			'sfYaml/sfYaml.php',
		]
	];

	public function __construct () {
		$this->format('yml', [ $this, 'conf_dep_file' ]);
	}

	/**
	 * deps file formatter
	 * @return string
	 */
	public function conf_dep_file ($file) {
		return $this->path_to . $file;
	}

	/**
	 * deps path setter
	 * @param string $path
	 */
	public function set_path ($path) {
		$this->path_to = $path;
	}
}

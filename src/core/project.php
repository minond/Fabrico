<?php

/**
 * @package fabrico\core
 */
namespace fabrico\core;

/**
 * project manager
 */
class Project extends Module {
	/**
	 * file types
	 */
	const VIEW = 'views';
	const BUILD = 'build';
	const TEMPLATE = 'templates';
	const CONTROLLER = 'controllers';

	/**
	 * project root directory
	 * @string
	 */
	private $root;

	/**
	 * project's name
	 * @var string
	 */
	private $project_name;

	/**
	 * @param string $name
	 * @param string $root
	 */
	public function __construct ($name, $root) {
		$this->project_name = $name;
		$this->root = $root;
	}

	/** 
	 * @param string $type
	 * @return string
	 */
	private function dr ($type) {
		return $this->configuration->core->directory->{ $type };
	}

	/**
	 * @param string $type
	 * @return string
	 */
	private function ext ($type) {
		return $this->configuration->core->file->ext[ $type ];
	}

	/**
	 * @param string $name
	 * @param string $type
	 * @return string
	 */
	public function get_file ($name, $type) {
		return $this->root . $this->dr($type) .
		       $name . $this->ext($type);
	}

	/**
	 * @see get_file
	 * @param string $name
	 * @param string $type
	 * @return array [string, boolean]
	 */
	public function got_file ($name, $type) {
		$file = $this->get_file($name, $type);
		return [ $file, file_exists($file) ];
	}

	/**
	 * @param string $name
	 * @param string $type
	 * @return string
	 */
	public function get_build ($name, $type) {
		return $this->root . $this->dr(self::BUILD) .
		       $this->dr($type) . $name . $this->ext($type);
	}

	/**
	 * project root getter
	 * @return string
	 */
	public function get_project_root () {
		return $this->root;
	}

	/**
	 * project name getter
	 * @return string
	 */
	public function get_project_name () {
		return $this->project_name;
	}
}

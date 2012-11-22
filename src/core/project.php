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
	const JS = 'javascript';

	/**
	 * project root directory
	 * @var string
	 */
	private $root;

	/**
	 * project web root
	 * @var string
	 */
	private $webroot;

	/**
	 * project's name
	 * @var string
	 */
	private $project_name;

	/**
	 * @param string $name
	 * @param string $root
	 * @param string $webroot
	 */
	public function __construct ($name, $root, $webroot) {
		$this->project_name = $name;
		$this->root = $root;
		$this->webroot = $webroot;
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
		return property_exists($this->configuration->core->file->ext, $type) ?
			$this->configuration->core->file->ext->{ $type } : '';
	}

	public function get_resource ($name, $type) {
		return $this->webroot . $this->dr($type) .
			$name . $this->ext($type);
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

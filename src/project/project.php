<?php

/**
 * @package fabrico\project
 */
namespace fabrico\project;

use fabrico\core\Module;

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
	const CSS = 'css';
	const ELEMENT = 'elements';
	const MODEL = 'models';

	/**
	 * project root directory
	 * @var string
	 */
	private $root;

	/**
	 * framework's root directory
	 * @var string
	 */
	private $myroot;

	/**
	 * project web root
	 * @var string
	 */
	private $webroot;

	/**
	 * framework's web root
	 * @var string
	 */
	private $mywebroot;

	/**
	 * project's name
	 * @var string
	 */
	private $project_name;

	/**
	 * @param string $project_name
	 */
	public function set_project_name ($project_name) {
		$this->project_name = $project_name;
	}

	/**
	 * @param string $root
	 */
	public function set_root ($root) {
		$this->root = $root;
	}

	/**
	 * @param string $webroot
	 */
	public function set_webroot ($webroot) {
		$this->webroot = $webroot;
	}

	/**
	 * @param string $myroot
	 */
	public function set_myroot ($myroot) {
		$this->myroot = $myroot;
	}

	/**
	 * @param string $mywebroot
	 */
	public function set_mywebroot ($mywebroot) {
		$this->mywebroot = $mywebroot;
	}

	/**
	 * @return string
	 */
	public function get_fsroot() {
		return $this->fsroot;
	}

	/**
	 * @param string $fsroot
	 */
	public function set_fsroot($fsroot) {
		$this->fsroot = $fsroot;
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

	/**
	 * path to resource file (image, javascript, etc.)
	 * @param string $name
	 * @param string $type
	 * @param boolean $internal
	 * @return string
	 */
	public function get_resource ($name, $type, $internal = false) {
		$root = $internal ? $this->mywebroot : $this->webroot;
		return $root . $this->dr($type) .
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
	 * @param string $name
	 * @param string $type
	 * @param string $prefix
	 * @return string
	 */
	public function get_project_file ($name, $type, $prefix = '') {
		return $prefix . $this->dr($type) .
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
	 * @see got_file
	 * @param string $name
	 * @param string $type
	 * @return boolean
	 */
	public function has_file ($name, $type) {
		return file_exists($this->get_file($name, $type));
	}

	/**
	 * @see get_project_file
	 * @param string $name
	 * @param string $type
	 * @param string $prefix
	 * @return array [string, boolean]
	 */
	public function got_project_file ($name, $type, $prefix = '') {
		$file = $this->get_project_file($name, $type, $prefix);
		return [ $file, file_exists($file) ];
	}

	/**
	 * @see got_project_file
	 * @param string $name
	 * @param string $type
	 * @param string $prefix
	 * @return boolean
	 */
	public function has_project_file ($name, $type, $prefix = '') {
		return file_exists(
			$this->get_project_file($name, $type, $prefix)
		);
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
}

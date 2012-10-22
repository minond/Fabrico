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

	/**
	 * @return string
	 */
	private function root () {
		return $this->configuration->core->project->path;
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
		return $this->root() . $this->dr($type) .
		       $name . $this->ext($type);
	}

	/**
	 * @param string $name
	 * @param string $type
	 * @return string
	 */
	public function get_build ($name, $type) {
		return $this->root() . $this->dr(self::BUILD) .
		       $this->dr($type) . $name . $this->ext($type);
	}
}

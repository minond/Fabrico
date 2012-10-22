<?php

/**
 * @package fabrico\page
 */
namespace fabrico\page;

use fabrico\core\Module;
use fabrico\core\util;

/**
 * fabrico template builder
 */
class Build extends Module {
	/**
	 * @return boolean
	 */
	public function can_build () {
		return $this->configuration->core->templates->build;
	}

	/**
	 * @param string $file 
	 * @return int
	 */
	private function fmodt ($file) {
		return file_exists($file) ? filemtime($file) : 0;
	}

	/**
	 * @param array $raw
	 * @param string $build
	 * @return boolean
	 */
	public function should_build (array $raw, $build) {
		$newest = 0;

		foreach ($raw as $file) {
			$newest = max($newest, $this->fmodt($file));
		}

		return $this->fmodt($build) > $newest;
	}

	/**
	 * @param array $raw
	 * @param string $target
	 * @return boolean
	 */
	public function build (array $raw, $target) {
		$success = false;

		return $success;
	}
}

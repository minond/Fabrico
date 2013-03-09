<?php

namespace Fabrico\Request;

/**
 * represents the application's current request
 */
interface ApplicationRequest {
	/**
	 * should return true of current request object is ready to be loaded and
	 * is meeting every requirement by the project.
	 * @return boolean
	 */
	public function valid();

	/**
	 * should handle the application's current request. returns true if every
	 * thing was loaded/triggered successfully.
	 * @return boolean
	 */
	public function load();
}

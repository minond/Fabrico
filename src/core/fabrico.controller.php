<?php

namespace Fabrico;

class Controller {
	/**
	 * methods public to http requests
	 *
	 * @var array
	 */
	public $public_methods = array();

	/**
	 * constructor
	 */
	public function __construct () {}

	/**
	 * on view virtual method
	 */
	public function onview () {}

	/**
	 * on method virtual method
	 */
	public function onmethod () {}
}

<?php

namespace Fabrico\Response;

/**
 * base interface for a response types (ie. html, json, view file)
 */
interface Response {
	/**
	 * ready to send response identifier
	 * @return boolean
	 */
	public function ready();

	/**
	 * outputs data
	 * @return Fabrico\Output\Output
	 */
	public function send();
}

<?php

namespace Fabrico\Response;

use Fabrico\Output\TextOutput;

/**
 * responds to a browser
 */
class HttpResponse implements Response {
	/**
	 * @return boolean
	 */
	public function ready() {
		return true;
	}

	/**
	 * @return mixed
	 */
	public function send() {
		$out = new TextOutput;
		$out->setContent('hi');
		return $out;
	}
}

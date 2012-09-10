<?php

namespace Fabrico\Authentication;

/**
 * basic check and redirect authentication interface
 */
interface Basic {
	/**
	 * called when checking authentication
	 *
	 * @param boolean
	 */
	public function authenticate_basic ();

	/**
	 * called when authentication fails
	 * the request is redirected to the returned url unless
	 * the current view is the same as the redirect
	 *
	 * @return string
	 */
	public function authenticate_redirect ();

	/**
	 * called after authentication has been successful
	 * the request is redirected to the returned url
	 *
	 * @return string
	 */
	public function authenticate_homepage ();
}

/**
 * authentication check overwrite
 */
trait Noauth {
	public function authenticate_basic () {
		return true;
	}
}

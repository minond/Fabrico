<?php

namespace Fabrico\Request;

interface Request {
	/**
	 * @return boolean
	 */
	public function valid();

	/**
	 * data setter
	 * @param array $data
	 */
	public function setData(array & $data);

	/**
	 * data getter
	 * @return array
	 */
	public function getData();
}

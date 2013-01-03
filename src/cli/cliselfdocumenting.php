<?php

/**
 * @package fabrico\cli
 */
namespace fabrico\cli;

use fabrico\klass\DocParser;

/**
 * documents a controller with cli access
 */
trait CliSelfDocumenting {
	use DocParser;

	/**
	 * @return array
	 */
	public function generate_man_functions() {
		$me = get_class($this);
		$all_methods = get_class_methods($this);
		$my_methods = [];
		$my_file = new \ReflectionClass($me);
		$my_file = $my_file->getFileName();

		foreach ($all_methods as $method) {
			$metr = new \ReflectionMethod($me, $method);

			if (!$metr->isConstructor() &&
				!$metr->isDestructor() &&
				$metr->isPublic() &&
				$metr->class === $me &&
				$my_file === $metr->getFileName()) {
					$my_methods[] = $method;
			}
		}

		foreach ($my_methods as $method) {
			$doc = $this->func($me, $method);
			$man = [ 'comment' => '', 'params' => [] ];

			if (isset($doc['comment'])) {
				$man['comment'] = $doc['comment'];
			}

			if (isset($doc['param'])) {
				$man['params'] = $doc['param'];
			}

			$info[ $method ] = $man;
		}

		return $info;
	}
}

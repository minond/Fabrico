<?php

/**
 * @package fabrico\klass
 */
namespace fabrico\klass;

/**
 * PHPDoc comment parser
 */
trait DocParser {
	/**
	 * parses a class' comments
	 * @param string $class
	 * @return array
	 */
	public function klass($class) {
		$reflection = new \ReflectionClass($class);
		$comment = $reflection->getDocComment();
		return $this->parse($comment);
	}

	/**
	 * parses a function's comments
	 * @param string $class
	 * @param string $name
	 * @return array
	 */
	public function func($class, $name) {
		$reflection = new \ReflectionClass($class);
		$func = $reflection->getMethod($name);
		$comment = $func->getDocComment();
		return $this->parse($comment);
	}

	/**
	 * parses a property's comments
	 * @param string $class
	 * @param string $name
	 * @return array
	 */
	public function property($class, $name) {
		$reflection = new \ReflectionClass($class);
		$prop = $reflection->getProperty($name);
		$comment = $prop->getDocComment();
		return $this->parse($comment);
	}

	/**
	 * @param string $comment
	 * @return array
	 */
	private function parse($comment) {
		$sep = "\n";
		$text_label = 'comment';
		$lines = explode($sep, $comment);
		$info = [];
		$info[ $text_label ] = [];
		$last = & $info[ $text_label ];

		foreach ($lines as $index => $line) {
			$line = trim($line);
			$lines[ $index ] = preg_replace('/^\*/', '', $line);
		}

		// start and finish
		array_pop($lines);
		array_shift($lines);

		foreach ($lines as $line) {
			$line = trim($line);

			if (!strlen($line)) {
				continue;
			}

			if ($line[0] === '@') {
				$fspace = strpos($line, ' ');
				$pname = trim(substr($line, 1, $fspace));
				$notes = trim(substr($line, $fspace + 1));
				$info[ $pname ] = [ $notes ];
				$last = & $info[ $pname ];
			}
			else {
				$last[] = $line;
			}
		}

		foreach ($info as $key => $lines) {
			$info[ $key ] = implode($sep, $lines);
		}

		if (!strlen($info[ $text_label ])) {
			unset($info[ $text_label ]);
		}

		return $info;
	}
}

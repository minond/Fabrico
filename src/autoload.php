<?php

define('FABRICO_SRC_ROOT', getcwd());
define('FABRICO_NS_ROOT', 'Fabrico');
define('FABRICO_DEF_EXT', '.php');

spl_autoload_register(function ($class) {
	$parts = explode('\\', $class);
	$root = $parts[0];
	$rest = implode(DIRECTORY_SEPARATOR, array_slice($parts, 1));

	if ($root = FABRICO_NS_ROOT) {
		$file = FABRICO_SRC_ROOT . DIRECTORY_SEPARATOR . $rest . FABRICO_DEF_EXT;
		require $file;
	}
});

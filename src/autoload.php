<?php

// basic information about Fabrico's namespace and file structure
define('FABRICO_NS_ROOT', 'Fabrico');
define('FABRICO_DEF_EXT', '.php');
define('FABRICO_SRC_ROOT', dirname(__FILE__));

// everything should always be triggered from Fabrico's src directory
chdir(FABRICO_SRC_ROOT);

// request the vendor's autoload
require '../vendor/autoload.php';

// and create Fabrico's own autoload
spl_autoload_register(function ($class) {
	$parts = explode('\\', $class);
	$root = $parts[0];
	$rest = implode(DIRECTORY_SEPARATOR, array_slice($parts, 1));

	if ($root = FABRICO_NS_ROOT) {
		$file = FABRICO_SRC_ROOT . DIRECTORY_SEPARATOR . $rest . FABRICO_DEF_EXT;

		if (file_exists($file)) {
			require $file;
		}
	}
});

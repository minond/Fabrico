<?php

class FabricoTag {
	protected static $registry = array();

	public static function register ($namespace, $setter) {
		$registry = & self::$registry;
		$setter(function ($tagname, $info) use (& $registry, $namespace) {
			$registry[ $namespace ][] = $info;
		});
	}
}

FabricoTag::register('f', function ($reg) {
	$reg('form', array(
		'method', 
	));
});

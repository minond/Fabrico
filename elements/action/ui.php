<?php

namespace view\action;

class method extends \Fabrico\Element {
	/**
	 * errors
	 */
	const ERROR_DUPLICATE_ENV = 'duplicate enviroment argument found';

	/**
	 * standard properties
	 */
	const NAME = 'name';
	const ACTION = 'action';
	const CALLBACK = 'callback';
	const VALUE = 'value';
	const ASSIGNTO = 'assignto';
	const COMMA = ', ';
	const QUOTE = '"';
	const PROP = '": "';

	/**
	 * controller method call code
	 */
	const METHOD_CALL = <<<JS
var #{name} = function () {
	return Fabrico.controller.method(
		"#{method}",
		[ #{parameters} ],
		{ #{envargs} },
		#{callback}
	);
};
JS;

	public static function pregen (& $args) {
		$envargs = array();
		$uniqueenv = array();
		$parameters = array();

		if (isset($args[ self::A_PARAM ])) {
			foreach ($args[ self::A_PARAM ] as $param) {
				if (!isset($param[ self::ASSIGNTO ])) {
					$parameters[] = self::QUOTE . $param[ self::VALUE ] . self::QUOTE;
				}
				else {
					if (in_array($param[ self::ASSIGNTO ], $uniqueenv)) {
						\Fabrico\Error::message( self::ERROR_DUPLICATE_ENV );
					}
					else {
						$uniqueenv[] = $param[ self::ASSIGNTO ];
						$envargs[] = self::QUOTE . $param[ self::ASSIGNTO ] .
						             self::PROP . $param[ self::VALUE ] . self::QUOTE;
					}
				}
			}
		}

		\Fabrico\Page::include_javascript(
			\Fabrico\Merge::parse(self::METHOD_CALL, array(
				'name' => $args[ self::NAME ],
				'method' => $args[ self::ACTION ],
				'parameters' => implode(self::COMMA, $parameters),
				'envargs' => implode(self::COMMA, $envargs),
				'callback' => isset($args[ self::CALLBACK ]) ? 
				              $args[ self::CALLBACK ] : self::A_NULL
			)
		), true);

		return false;
	}
}

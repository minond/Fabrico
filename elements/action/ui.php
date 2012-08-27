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
	const ERRBACK = 'errback';
	const VALUE = 'value';
	const ASSIGNTO = 'assignto';
	const COMMA = ', ';
	const QUOTE = '"';
	const PROP = '": "';
	const UPDATES = 'update';
	const FORMDATA = 'formdata';
	const SELECTOR = 'selector';
	const ON = 'on';
	const DATASET = 'dataset';

	/**
	 * controller method call code
	 */
	const METHOD_CALL = <<<JS
Fabrico.controller.method(
	"#{method}",
	[ #{parameters} ],
	[ #{updates} ],
	{ #{envargs} },
	#{callback},
	#{errback}
);
JS;

	/**
	 * event definition
	 */
	const FUNC_DEF = <<<JS
var #{name} = function () {
	return #{method_call}
};
JS;


	/**
	 * event listener
	 */
	const EVENT_BIND = <<<JS
$("#{selector}").live("#{event}", function (ev) {
	ev.preventDefault();
	#{method_call}
});
JS;

	public static function pregen (& $args) {
		$fn = '';
		$onready = false;
		$envargs = [];
		$uniqueenv = [];
		$parameters = [];
		$updates = [];

		if (isset($args[ self::A_PARAM ])) {
			foreach ($args[ self::A_PARAM ] as $param) {
				if (isset($param[ self::DATASET ])) {
					$parameters[] = '$(' . 
						(isset($param[ self::SELECTOR ]) ? self::QUOTE . $param[ self::SELECTOR ] . self::QUOTE : 'this') .
						').data("' . $param[ self::DATASET ] . '")';
				}
				else if (isset($param[ self::FORMDATA ])) {
					$parameters[] = 'Fabrico.helper.form2args("#' . $param[ self::FORMDATA ] . '")';
				}
				else if (!isset($param[ self::ASSIGNTO ])) {
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

		if (isset($args[ self::UPDATES ])) {
			$updates = explode(',', $args[ self::UPDATES ]);

			array_walk($updates, function ($id, $index) use (& $updates) {
				$updates[ $index ] = self::QUOTE . trim($id) . self::QUOTE;
			});
		}

		if (isset($args[ self::A_CONTENT ]) && trim($args[ self::A_CONTENT ])) {
			$parameters[] = trim($args[ self::A_CONTENT ]);
		}

		$fn = \Fabrico\Merge::parse(self::METHOD_CALL, [
			'method' => $args[ self::ACTION ],
			'parameters' => implode(self::COMMA, $parameters),
			'updates' => implode(self::COMMA, $updates),
			'envargs' => implode(self::COMMA, $envargs),
			'callback' => isset($args[ self::CALLBACK ]) ? 
			              $args[ self::CALLBACK ] : self::A_NULL,
			'errback' => isset($args[ self::ERRBACK ]) ? 
			              $args[ self::ERRBACK ] : self::A_NULL
		]);

		if (isset($args[ self::NAME ])) {
			$fn = \Fabrico\Merge::parse(self::FUNC_DEF, [
				'name' => $args[ self::NAME ],
				'method_call' => $fn
			]);
		}
		else if (isset($args[ self::ON ]) && isset($args[ self::SELECTOR ])) {
			$onready = true;
			$fn = \Fabrico\Merge::parse(self::EVENT_BIND, [
				'selector' => $args[ self::SELECTOR ],
				'event' => $args[ self::ON ],
				'method_call' => $fn
			]);
		}

		\Fabrico\Page::include_javascript($fn, true, $onready);
		return false;
	}
}

<?php

namespace view\action;

class linkto extends \Fabrico\Element {
	protected static $tag = 'a';
	protected static $getopt = [ 'href' ];

	protected static function pregen (& $props) {
		if (!$props->href) {
			$props->href = '#';
			\Fabrico\Page::include_javascript('Fabrico.ui.no_action_links();', true, true);
		}
	}
}

class event extends \Fabrico\Element {
	const BIND = '$("#{selector}").#{on}(#{method});';
	const LIVE = '$("#{selector}").live("#{on}", #{method});';

	protected static $getopt = [
		'selector',
		'method',
		'live',
		'on'
	];

	protected static function pregen (& $props) {
		\Fabrico\Page::include_javascript(
			\Fabrico\Merge::parse(
				$props->live ? self::LIVE : self::BIND, [
					'selector' => $props->selector,
					'method' => $props->method,
					'on' => $props->on
				]
			), true, true
		);

		return false;
	}
}

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
	const PROP = '": ';
	const UPDATES = 'update';
	const FORMDATA = 'formdata';
	const SELECTOR = 'selector';
	const ON = 'on';
	const DATASET = 'dataset';
	const BINDTO = 'bindto';

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

		if (isset($args->param)) {
			foreach ($args->param as $param) {
				if (isset($param->dataset)) {
					$parameters[] = '$(' . 
						(isset($param->selector) ? self::QUOTE . $param->selector . self::QUOTE : 'this') .
						').data("' . $param->dataset . '")';
				}
				else if (isset($param->formdata)) {
					$parameters[] = 'Fabrico.helper.form2args("#' . $param->formdata . '")';
				}
				else if (!isset($param->assignto)) {
					if (isset($param->value)) {
						$parameters[] = self::QUOTE . $param->value . self::QUOTE;
					}
					else if (isset($param->bindto)) {
						$parameters[] = '$("' . $param->bindto . '").val()';
					}
				}
				else {
					if (in_array($param->assignto, $uniqueenv)) {
						\Fabrico\Error::message( self::ERROR_DUPLICATE_ENV );
					}
					else {
						$uniqueenv[] = $param->assignto;
						$envargs[] = self::QUOTE . $param->assignto . self::PROP . 
						             (isset($param->bindto) ?
									 	('$("' . $param->bindto . '").val()') :
						             	(self::QUOTE . $param->value . self::QUOTE)
									 );
					}
				}
			}
		}

		if (isset($args->update)) {
			$updates = explode(',', $args->update);

			array_walk($updates, function ($id, $index) use (& $updates) {
				$updates[ $index ] = self::QUOTE . trim($id) . self::QUOTE;
			});
		}

		if (isset($args->content) && trim($args->content)) {
			$parameters[] = trim($args->content);
		}

		$fn = \Fabrico\Merge::parse(self::METHOD_CALL, [
			'method' => $args->action,
			'parameters' => implode(self::COMMA, $parameters),
			'updates' => implode(self::COMMA, $updates),
			'envargs' => implode(self::COMMA, $envargs),
			'callback' => isset($args->callback) ? 
			              $args->callback : 'null',
			'errback' => isset($args->errback) ? 
			              $args->errback : 'null'
		]);

		if (isset($args->name)) {
			$fn = \Fabrico\Merge::parse(self::FUNC_DEF, [
				'name' => $args->name,
				'method_call' => $fn
			]);
		}
		else if (isset($args->on) && isset($args->selector)) {
			$onready = true;
			$fn = \Fabrico\Merge::parse(self::EVENT_BIND, [
				'selector' => $args->selector,
				'event' => $args->on,
				'method_call' => $fn
			]);
		}

		\Fabrico\Page::include_javascript($fn, true, $onready);
		return false;
	}
}

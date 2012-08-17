<?php

namespace input;

class form extends \Fabrico\Element {
	protected static $tag = 'form';

	protected static function pregen (& $args) {
		if (isset($args['model'])) {
			$model = & $args['model'];


			$schema = \ActiveRecord\Table::load(get_class($model));
			print_r($schema); die;

			$args['content'] = '<pre>' . print_r($model, true) . '</pre>';
			unset($args['model']);
		}
	}
}

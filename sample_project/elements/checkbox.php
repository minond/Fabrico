<?php

class checkbox extends FabricoElement {
	protected static $tag = 'input';
	protected static $type = 'checkbox';
	protected static $class = array('fancy_checkbox');

	protected static function pregen ($name, $label = false) {
		$id = self::gen_id($name);
		self::$elem->name = $name;
		self::$elem->value = $name;
		self::$elem->id = $id;

		if ($label) {
			self::$posthtml = html::el('label', array(
				'for' => $id,
				'content' => $label,
				'class' => 'fancy_checkbox_label'
			));
		}
	}
}

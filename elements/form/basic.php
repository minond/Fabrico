<?php

namespace form;

class blocklink extends \FabricoElement {
	protected static $tag = 'div';
	protected static $class = array('blocklinkholder');
	public static $onready = '$("#%id").click(function () {
		window.location.href = "%url";
	});';

	public static function pregen ($content, $linkto = false, $width = 400) {
		self::$elem->content = $content;
		self::$elem->style = "width: {$width}px;";
		self::$elem->id = self::gen_id(rand());

		if ($linkto) {
			self::$elem->style .= 'cursor: pointer;';
			self::$onready_vars = array(
				'id' => self::$elem->id,
				'url' => $linkto
			);
		}
		else {
			self::$parsejs = false;
		}
	}
}

class method extends \FabricoElement {
	protected static $tag = 'form';
	protected static $class = array('formbasic');
	public static $onready = 'Fabrico.ui.listen_submit_form("#%id", "%method");';

	protected static function pregen ($method, $content, $onpass = false, $onfail = false, $ajax = true) {
		self::$parsejs = $ajax;
		self::$elem->method = 'POST';
		self::$elem->id = self::gen_id(rand());
		self::$elem->content .= $content;

		// method
		self::$elem->content .= \html::el('input', array(
			'type' => 'hidden',
			'name' => \Fabrico::$uri_query_method,
			'value' => $method
		)); 

		// success url
		self::$elem->content .= \html::el('input', array(
			'type' => 'hidden',
			'name' => \Fabrico::$uri_query_success,
			'value' => $onpass
		)); 

		// failure url
		self::$elem->content .= \html::el('input', array(
			'type' => 'hidden',
			'name' => \Fabrico::$uri_query_fail,
			'value' => $onfail
		)); 

		self::$onready_vars = array(
			'id' => self::$elem->id,
			'method' => $method
		);
	}
}

class textfield extends \FabricoElement {
	protected static $tag = 'input';
	protected static $type = 'text';
	protected static $class = array('basictextfield');

	public static function pregen ($name, $label, $value = '') {
		self::$elem->value = $value;
		self::$elem->name = $name;
		self::$elem->id = self::gen_id($name);
		self::$elem->placeholder = $label;
		self::$elem->autocomplete = 'off';

		self::$prehtml = \html::el('label', array(
			'content' => $label,
			'for' => self::$elem->id
		));
	}
}

class hiddenfield extends \FabricoElement {
	protected static $tag = 'input';
	protected static $type = 'hidden';

	public static function pregen ($name, $value = '') {
		self::$elem->value = $value;
		self::$elem->name = $name;
		self::$elem->id = self::gen_id($name);
	}
}

class passwordfield extends textfield {
	protected static $type = 'password';
	protected static $class = array('basictextfield', 'basicpasswordfield');
}

class checkboxfield extends textfield {
	protected static $type = 'checkbox';
	protected static $class = array('basiccheckboxfield');

	public static function pregen ($name, $label, $checked = false) {
		parent::pregen($name, $label, '1');

		if ($checked)
			self::$elem->checked = $checked;

	}
}

class submitbutton extends \FabricoElement {
	protected static $tag = 'input';
	protected static $type = 'submit';
	protected static $class = array('basicbutton', 'basicsubmitbutton');

	public static function pregen ($label) {
		self::$elem->value = $label;
	}
}

class methodbutton extends \FabricoElement {
	protected static $tag = 'input';
	protected static $type = 'button';
	protected static $class = array('basicbutton', 'basicactionbutton');
	public static $onready = '$("#%id").click(function () {
		Fabrico.controller.method("%method", [%args]);
	});';

	public static function pregen ($label, $method, $args = array()) {
		self::$elem->id = self::gen_id(rand());
		self::$elem->value = $label;

		foreach ($args as $index => $arg) {
			$args[ $index ] = sprintf('"%s"', $arg);
		}

		$args = implode(', ', $args);

		self::$onready_vars = array(
			'id' => self::$elem->id,
			'method' => $method,
			'args' => $args
		);
	}
}

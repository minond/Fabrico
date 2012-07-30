<?php

namespace form;

class field extends \FabricoTemplateElement {
	protected static $templates = array(
		'password' => 'fields/password.php',
		'text' => 'fields/text.php',
		'hidden' => 'fields/hidden.php',
		'select' => 'fields/select.php',
		'multiselect' => 'fields/multiselect.php',
		'checkbox' => 'fields/checkbox.php',
		'radio' => 'fields/radio.php',
		'file' => 'fields/file.php',
		'button' => 'fields/button.php',
		'label' => 'fields/label.php',
		'formitem' => 'fields/formitem.php',
	);

	protected static $expecting = array('name', 'id', 'value', 'class',
		'placeholder', array('autocomplete', 'off'), array('checked', false), array('values', array()));
}

<?php

namespace form;

class field extends \FabricoTemplateElement {
	protected static $templates = array(
		'password' => 'fields/password.php',
		'text' => 'fields/text.php',
		'select' => 'fields/select.php',
		'multiselect' => 'fields/multiselect.php',
		'checkbox' => 'fields/checkbox.php',
		'radio' => 'fields/radio.php',
		'file' => 'fields/file.php',
		'button' => 'fields/button.php',
		'label' => 'fields/label.php',
		'formitem' => 'fields/formitem.php',
	);
}

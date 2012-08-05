<?php

namespace form;

class model extends \FabricoTemplateElement {
	protected static $templates = array(
		'edit' => 'form/editmodel.php'
	);

	protected static $expecting = array(
		array('order', array()),
		array('actions', array()),
		array('center', false)
	);
}

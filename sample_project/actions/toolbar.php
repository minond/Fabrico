<?php

function toolbar_item ($title) {
	$id = strtolower(preg_replace('/\W/', '', $title) . '_toolbar_item');
	$html = HTML::el('div', array(
		'content' => $title,
		'class' => 'toolbar_item',
		'id' => $id
	));

	Resource::onready("console.log('{$id}');");

	return $html;
}

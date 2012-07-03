<?php

require 'mFrame.init.php';

echo mHTML::el('div', array(
	'id' => 'test',
	'content' => mHTML::el('a', array(
		'href' => 'http://google.com',
		'content' => 'hi'
	))
));

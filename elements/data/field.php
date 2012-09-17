<?php

namespace view\data;

use Fabrico\Element;

class input extends Element {
	protected static $tag = 'input';
	protected static $classes = [ 'data_input' ];
}

class password extends input {
	protected static $type = 'password';
}

class button extends input {
	protected static $type = 'button';
	protected static $classes = [ 'data_button' ];
}

class submit extends button {
	protected static $type = 'submit';
}

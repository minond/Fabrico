<?php

namespace view\table;

class column extends \Fabrico\Element {
	protected static $tag = false;
	protected static $parameter = true;
	protected static $getopt = [ 'key', 'label', 'type', 'format' ];
}

<?php

class mHTML {
	private static $content = 'content';

	public static function el ($type, $props = array()) {
		$open = "<{$type}";
		$close = "</{$type}>";
		$text = '>';
		$html = '';

		foreach ($props as $prop => $value) {
			if ($prop === self::$content) {
				$text .= $value;
			}
			else {
				$html .= " {$prop}='{$value}'";
			}
		}

		return $open . $html . $text . $close;
	}
}

class mElement {
	private $tag_str;

	protected function tag ($str) {
		$this->tag_str = $str;
	}

	public function html () {
		
	}
}

class Div extends mElement {
	
}

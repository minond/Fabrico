<?php

namespace fabrico;

class FabricoLoader extends AutoLoader {
	public function __construct () {
		$this->register('core', [
			'router'
		]);

		$this->format('core', function ($file) {
			return "fabrico.{$file}.php";
		});
	}
}

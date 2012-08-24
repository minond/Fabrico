<?php

namespace view\data;

class table extends \Fabrico\Element {
	protected static $tag = 'table';
	protected static $classes = [ 'data_table' ];
	const A_COLUMN = 'column';

	// TODO: implement footer
	protected static function pregen (& $props) {
		// table make-up
		$header = '';
		$body = '';
		$footer = '';

		// table data
		$rows = [];
		$columns = [];

		// get columns
		$columns = array_filter($props[ self::A_PARAM ], function ($param) {
			if ($param[ self::A_NAME ] === self::A_COLUMN) {
				return $param;
			}
		});

		// get row data
		array_walk($props[ self::A_DATA ], function ($row, $index) use (& $rows, & $columns) {
			$rows[ $index ] = [];

			foreach ($columns as $column) {
				$rows[ $index ][] = isset($row[ $column[ self::A_KEY ] ]) ? $row[ $column[ self::A_KEY ] ] : '';
			}
		});

		// generate header
		foreach ($columns as $column) {
			$header .= \Fabrico\html::th([
				self::A_CONTENT => $column[ self::A_LABEL ]
			]);
		}

		$header = \Fabrico\html::thead([
			self::A_CONTENT => $header
		]);

		// generate the body
		foreach ($rows as $row) {
			$tr = '';

			foreach ($row as $content) {
				$tr .= \Fabrico\html::td([
					self::A_CONTENT => $content
				]);
			}

			$body .= \Fabrico\html::tr([
				self::A_CONTENT => $tr
			]);
		}

		$body = \Fabrico\html::body([
			self::A_CONTENT => $body
		]);

		// put it together
		$props[ self::A_CONTENT ] = $header . $body . $footer;
	}
}

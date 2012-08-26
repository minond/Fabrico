<?php

namespace view\data;

class table extends \Fabrico\Element {
	protected static $tag = 'table';
	protected static $classes = [ 'data_table' ];
	const A_COLUMN = 'column';
	const A_CAPTION = 'caption';
	const A_CELL_CLASS = 'data_cell_';

	// TODO: implement footer
	protected static function pregen (& $props) {
		// table make-up
		$header = '';
		$body = '';
		$footer = '';

		// table data
		$caption = [];
		$rows = [];
		$columns = [];

		// get columns
		if (isset($props[ self::A_PARAM ])) {
			array_walk($props[ self::A_PARAM ], function ($param) use (& $columns, & $caption) {
				if ($param[ self::A_NAME ] === self::A_COLUMN) {
					$columns[] = $param;
				}
			});
		}

		if (!count($columns)) {
			if ($props[ self::A_DATA ]) {
				foreach ($props[ self::A_DATA ][ 0 ] as $field => $value) {
					$columns[] = [
						'key' => $field,
						'label' => ucwords($field)
					];
				}
			}
		}

		// get row data
		array_walk($props[ self::A_DATA ], function ($row, $index) use (& $rows, & $columns) {
			$rows[ $index ] = [];

			if (is_array($row)) {
				$tmprow = & $row;
			}
			else if (is_object($row)) {
				$tmprow = (array) $row;
			}

			foreach ($columns as $column) {
				$rows[ $index ][] = isset($tmprow[ $column[ self::A_KEY ] ]) ? $tmprow[ $column[ self::A_KEY ] ] : '';
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
			// row content
			$tr = '';

			foreach ($row as $index => $content) {
				// content format
				$format_type = isset($columns[ $index ][ self::A_TYPE ]) ?
				               $columns[ $index ][ self::A_TYPE ] :
							   \Fabrico\Format::F_DEFAULT;

				$format_string = isset($columns[ $index ][ self::A_FORMAT ]) ?
				                 $columns[ $index ][ self::A_FORMAT ] : '';
			
				$formatted_content = \Fabrico\Format::format($content, $format_type, $format_string);
				$tr .= \Fabrico\html::td([
					self::A_CONTENT => $formatted_content,
					'class' => self::A_CELL_CLASS . $format_type
				]);
			}

			$body .= \Fabrico\html::tr([
				self::A_CONTENT => $tr
			]);
		}

		$body = \Fabrico\html::body([
			self::A_CONTENT => $body
		]);

		if (isset($props[ self::A_CAPTION ])) {
			$caption = \Fabrico\html::caption([
				self::A_CONTENT => $props[ self::A_CAPTION ]
			]);
		}
		else {
			$caption = '';
		}

		// put it together
		$props[ self::A_CONTENT ] = $caption . $header . $body . $footer;
	}
}

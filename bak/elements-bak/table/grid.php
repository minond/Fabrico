<?php

namespace view\table;

use Fabrico\html;
use Fabrico\Merge;
use Fabrico\Format;
use Fabrico\Element;
use Fabrico\Element\Param;

class grid extends Element {
	protected static $tag = 'table';
	protected static $classes = [ 'data_table' ];
	protected static $getopt = [ 'params', 'data', 'caption', 'nohighlight', 'noborder' ];
	protected static $ignore = [ 'noborder', 'nohighlight', 'params', 'data', 'caption' ];

	protected static function pregen (& $props) {
		$columns = [];
		$row_data = [];
		$header_data = [];
		$caption = $props->caption;

		if (!$props->nohighlight) {
			$props->class[] = 'data_table_reading';
		}

		if (!$props->noborder) {
			$props->class[] = 'show_border';
		}

		// parse parameters
		list($columns) = Param::run_reader('table', $props->param);

		// build the body
		if (is_array($columns) && count($columns)) {
			if (isset($props->data)) {
				foreach ($props->data as $row => $data) {
					$row_data[ $row ] = [];
	
					foreach ($columns as $column) {
						$row_data[ $row ][] = Param::run_writer('table_td', [ $data, $column ]);
					}
		
					$row_data[ $row ] = html::tr([ 'content' => implode('', $row_data[ $row ]) ]);
				}
			}

			// build the header
			foreach ($columns as $column) {
				$header_data[] = html::th([ 'content' => $column->label ]);
			}

			$props->content = Param::run_writer('table', [ $caption, $header_data, $row_data ]);
		}
	}
}

/**
 * checks for column and caption parameters
 */
Param::register_reader('table', function (& $params) {
	$columns = [];

	foreach ($params as $index => $param) {
		if ($param->classname === 'table_column') {
			$col = new \stdClass;

			$col->key = $param->key;
			$col->type = isset($param->type) ? $param->type : Format::F_STRING;
			$col->format = isset($param->format) ? $param->format : '';
			$col->label = isset($param->label) ? $param->label : ucwords($param->key);
			$col->content = isset($param->content) ? $param->content : '';

			$columns[] = $col;
		}
	}

	return [ $columns ];
});

/**
 * puts a table element together
 * TODO: implement footer
 */
Param::register_writer('table', function ($caption, $header, $body) {
	$body = html::tbody([ 'content' => implode('', $body) ]);
	$header = html::thead([ 'content' => implode('', $header) ]);

	if ($caption) {
		$caption = html::caption([ 'content' => $caption ]);
	}

	return $caption . $header . $body;
});

/**
 * builds cell elements
 */
Param::register_writer('table_td', function ($row, $column) {
	if (isset($column->key)) {
		$content = isset($row->{ $column->key }) ? $row->{ $column->key } : '';
		$content = Format::format($content, $column->type, $column->format);
	}
	else {
		$content = Merge::parse(
			$column->content, $row,
			Merge::PLACEHOLDER_SELECTOR
		);
	}

	return html::td([
		'content' => $content,
		'class' => 'data_cell_' . $column->format
	]);
});

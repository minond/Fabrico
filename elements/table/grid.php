<?php

namespace view\table;

class grid extends \Fabrico\Element {
	protected static $tag = 'table';
	protected static $classes = [ 'data_table' ];
	protected static $getopt = [ 'params', 'data' ];

	protected static function pregen (& $props) {
		$caption = '';
		$columns = [];
		$row_data = [];
		$header_data = [];

		// parse parameters
		list($columns, $caption) = \Fabrico\Element\Param::run_reader('table', $props->param);

		// build the body
		foreach ($props->data as $row => $data) {
			$row_data[ $row ] = [];

			foreach ($columns as $column) {
				$row_data[ $row ][] = \Fabrico\Element\Param::run_writer('table_td', [ $data, $column ]);
			}

			$row_data[ $row ] = \Fabrico\html::tr([
				'content' => implode('', $row_data[ $row ])
			]);
		}

		// build the header
		foreach ($columns as $column) {
			$header_data[] = \Fabrico\html::th([
				'content' => $column->label
			]);
		}

		$props->content = \Fabrico\Element\Param::run_writer('table', [ $caption, $header_data, $row_data ]);
	}
}

/**
 * checks for column and caption parameters
 */
\Fabrico\Element\Param::register_reader('table', function (& $params) {
	$columns = [];
	$caption = '';

	foreach ($params as $index => $param) {
		if ($param->classname === 'table_column') {
			$col = new \stdClass;
			$col->key = $param->key;
			$col->format = isset($param->format) ? $param->format : \Fabrico\Format::F_STRING;
			$col->formatstr = isset($param->formatstr) ? $param->formatstr : '';

			$col->label = isset($param->label) ? $param->label : ucwords($param->key);

			$columns[] = $col;
		}
		else if ($param->classname === 'caption') {
			$caption = $param->content;
		}
	}

	return [ $columns, $caption ];
});

/**
 * puts a table element together
 * TODO: implement footer
 */
\Fabrico\Element\Param::register_writer('table', function ($caption, $header, $body) {
	$body = \Fabrico\html::tbody([
		'content' => implode('', $body)
	]);

	$header = \Fabrico\html::thead([
		'content' => implode('', $header)
	]);

	if ($caption) {
		$caption = \Fabrico\html::caption([
			'content' => $caption
		]);
	}

	return $caption . $header . $body;
});

/**
 * builds cell elements
 */
\Fabrico\Element\Param::register_writer('table_td', function ($row, $column) {
	$content = isset($row->{ $column->key }) ? $row->{ $column->key } : '';
	$content = \Fabrico\Format::format($content, $column->format, $column->formatstr);

	return \Fabrico\html::td([
		'content' => $content,
		'class' => 'data_cell_' . $column->format
	]);
});

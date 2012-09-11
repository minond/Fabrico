<?php

namespace Fabrico;

class Page {
	/**
	 * css and javascript place holders
	 */
	const ERRORS = '<ERRORS />';
	const JAVASCRIPT = '<JAVASCRIPT />';
	const JAVASCRIPT_CODE = '<JAVASCRIPTCODE />';
	const CSS = '<CSS />';

	/**
	 * resource tags
	 * @var object
	 */
	public static $tag;

	/**
	 * list of javascript files to include
	 * @var array
	 */
	private static $javascript = [];

	/**
	 * list of javascript code to merge
	 * @var array
	 */
	private static $javascript_code = [
		'std' => [],
		'ready' => []
	];

	/**
	 * list of css files to include
	 * @var array
	 */
	private static $css = [];

	/**
	 * list of errors to display
	 * @var array
	 */
	private static $errors = [];

	/**
	 * errors getter
	 *
	 * @return array of error strings
	 */
	public static function get_errors () {
		return self::$errors;
	}

	/**
	 * include a javascript file
	 *
	 * @param string file source
	 */
	public static function include_javascript ($src, $code = false, $onready = false) {
		if ($code) {
			$loc = $onready ? 'ready' : 'std';

			if (!in_array($src, self::$javascript_code[ $loc ])) {
				self::$javascript_code[ $loc ][] = $src;
			}
		}
		else if (!in_array(sprintf(self::$tag->script, $src), self::$javascript)) {
			self::$javascript[] = sprintf(self::$tag->script, $src);
		}
	}

	/**
	 * include a css file
	 *
	 * @param string file href
	 */
	public static function include_css ($href) {
		if (!in_array($href, self::$css)) {
			self::$css[] = sprintf(self::$tag->css, $href);
		}
	}

	/**
	 * add an error to display
	 *
	 * @param string error message
	 */
	public static function include_error_message ($err) {
		self::$errors[] = $err;
	}

	/**
	 * starts capturing output on buffer
	 */
	public static function open () {
		ob_start();
	}

	/**
	 * outputs raw buffer
	 * checks builds as well
	 *
	 * @param boolean soft build
	 * @param boolean include javascript
	 */
	public static function close ($soft = false, $include_js = true) {
		$html = self::put_together(ob_get_clean(), $soft, $include_js);

		Build::view(
			Core::$configuration->state->uri, $html
		);

		return $html;
	}

	/**
	 * merges in link and script tags into the html string
	 *
	 * @param string html content
	 * @param boolean soft build
	 * @param boolean include js
	 */
	private static function put_together ($content, $soft = false, $include_js = true) {
		if (!$soft) {
			$jsstr = implode("\n", self::$javascript);
			$cssstr = implode("\n", self::$css);
			$jsstr = $jsstr ? "\n" . $jsstr : $jsstr;
			$cssstr = $cssstr ? "\n" . $cssstr : $cssstr;

			$jscode_basic = implode("\n", self::$javascript_code['std']);
			$jscode_ready = implode("\n", self::$javascript_code['ready']);
			$jscode = $jscode_ready . $jscode_basic ? sprintf(self::$tag->script_code, $jscode_basic, $jscode_ready) : '';

			if (count(self::$errors)) {
				$errorstr = implode("\n", self::$errors);
				$errorstr = $errorstr ? $errorstr . "\n" : $errorstr;
				
				return $errorstr;
			}

			return str_replace([
				self::JAVASCRIPT,
				self::JAVASCRIPT_CODE,
				self::CSS
			], [ $include_js ? $jsstr : '', $include_js ? $jscode : '', $cssstr ], $content);
		}
		else {
			return self::$tag->start_html . self::CSS .
			       self::get_body_tag() . $content . self::$tag->end_body . "\n" .
				   self::JAVASCRIPT . "\n" . self::JAVASCRIPT_CODE . self::$tag->end_html;
		}
	}

	/**
	 * builds a view file
	 */
	public static function build () {
		// NOTE: the build process/order needs to be redone
		self::open();
		echo file_get_contents(\view\template('seeing', true));
		echo file_get_contents(Core::$configuration->state->view);
		echo file_get_contents(\view\template('saw', true));
		self::close(true);

		// stard the buffer for the build file
		self::open();
	}

	/**
	 * returns a body tag with merged css classes
	 *
	 * @param string
	 */
	private static function get_body_tag () {
		return sprintf(self::$tag->start_body, '');
	}

	/**
	 * builds and returns the path the the template file
	 *
	 * @param string template file name
	 * @return string template build file path
	 */
	public static function get_template ($name) {
		Build::template($name);
		return Project::get_template_build_file($name);
	}
}

Page::$tag = (object) [
	'script_code' => "\n<script type=\"text/javascript\">\n%s\njQuery(function () {\n%s\n});\n</script>",
	'script' => '<script type="text/javascript" src="%s"></script>',
	'css' => '<link type="text/css" rel="stylesheet" href="%s" />',
	'start_html' => "<!doctype html>\n<html>\n\t<head>",
	'start_body' => "\n\t</head>\n\t<body class='%s'>",
	'end_body' => "\n\t</body>",
	'end_html' => "\n</html>"
];

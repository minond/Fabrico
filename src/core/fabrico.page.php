<?php

namespace Fabrico;

class Page {
	/**
	 * css and javascript place holders
	 */
	const ERRORS = '<ERRORS />';
	const JAVASCRIPT = '<JAVASCRIPT />';
	const CSS = '<CSS />';

	/**
	 * resource tags
	 *
	 * @var object
	 */
	public static $tag;

	/**
	 * list of javascript files to include
	 *
	 * @var array
	 */
	private static $javascript = array();

	/**
	 * list of css files to include
	 *
	 * @var array
	 */
	private static $css = array();

	/**
	 * list of errors to display
	 *
	 * @var array
	 */
	private static $errors = array();

	/**
	 * include a javascript file
	 *
	 * @param string file source
	 */
	public static function include_javascript ($src) {
		if (!in_array($src, self::$javascript)) {
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
	 */
	public static function close ($soft = false) {
		$html = self::put_together(ob_get_clean(), $soft);

		Build::view(
			Core::$configuration->state->uri, $html
		);

		return $html;
	}

	/**
	 * merges in link and script tags into the html string
	 *
	 * @param string html content
	 * @param bolean soft build
	 */
	private static function put_together ($content, $soft = false) {
		if (!$soft) {
			$jsstr = implode("\n", self::$javascript);
			$cssstr = implode("\n", self::$css);
			$errorstr = implode("\n", self::$errors);
			$cssstr = $cssstr ? "\n" . $cssstr : $cssstr;
			$jsstr = $jsstr ? "\n" . $jsstr : $jsstr;
			$errorstr = $errorstr ? $errorstr . "\n" : $errorstr;

			return str_replace(array(
				self::JAVASCRIPT,
				self::CSS,
				self::ERRORS
			), array($jsstr, $cssstr, $errorstr), $content);
		}
		else {
			return self::$tag->start_html . self::CSS .
			       self::get_body_tag() . self::ERRORS . $content .
			       self::$tag->end_body . self::JAVASCRIPT . self::$tag->end_html;
		}
	}

	private static function get_body_tag () {
		return sprintf(self::$tag->start_body, '');
	}
}

Page::$tag = (object) array(
	'script' => '<script type="text/javascript" src="%s"></script>',
	'css' => '<link type="text/css" rel="stylesheet" href="%s" />',
	'start_html' => "<!doctype html>\n<html>\n\t<head>",
	'start_body' => "\n\t</head>\n\t<body class='%s'>\n\n",
	'end_body' => "\n\t</body>",
	'end_html' => "\n</html>"
);

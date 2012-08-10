<?php

namespace Fabrico;

class Page {
	/**
	 * end tags used for merging link and script tags
	 */
	const END_HEAD = '</head>';
	const END_BODY = '</body>';
	const START_BODY = '<body>';
	const NL = "\n";

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
	 * include a javascript file
	 *
	 * @param string file source
	 */
	public static function include_javascript ($src) {
		if (!in_array($src, self::$javascript)) {
			self::$javascript[] = $src;
		}
	}

	/**
	 * include a css file
	 *
	 * @param string file href
	 */
	public static function include_css ($href) {
		if (!in_array($href, self::$css)) {
			self::$css[] = $href;
		}
	}

	/**
	 * starts capturing output on buffer
	 */
	public static function open () {
		ob_start();
	}

	/**
	 * outputs raw buffer
	 */
	public static function close () {
		echo self::put_together(ob_get_clean());
	}

	/**
	 * merges in link and script tags into the html string
	 *
	 * @param string html content
	 * @param string html with resources
	 */
	private static function put_together ($content) {
		$html = self::$tag->top . $content . self::$tag->bottom;
		$jslist = array();
		$csslist = array();

		foreach (self::$javascript as $file) {
			$jslist[] = sprintf(self::$tag->script, $file);
		}

		foreach (self::$css as $file) {
			$csslist[] = sprintf(self::$tag->css, $file);
		}

		$jsstr = implode(self::NL, $jslist);
		$cssstr = implode(self::NL, $csslist);

		$html = str_replace(self::END_HEAD, $cssstr . "\n\t" . self::END_HEAD, $html);
		$html = str_replace(self::END_BODY, self::END_BODY . self::NL . $jsstr, $html);
		return $html;
	}
}

Page::$tag = (object) array(
	'script' => '<script type="text/javascript" src="%s"></script>',
	'css' => '<link type="text/css" rel="stylesheet" href="%s" />',
	'top' => "<!doctype html>\n<html>\n\t<head>\n</head>\n\t<body class='%s'>\n",
	'bottom' => "\t</body>\n</html>"
);

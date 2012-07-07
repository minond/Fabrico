<?php

/**
 * @name Template
 * page template helpder functions
 */
class Template {
	/**
	 * @name os
	 * @var array
	 * used for detecting the operating system
	 */
	private static $os = array('android', 'blackberry', 'iphone', 'palm', 'linux', 'macintosh', 'windows');

	/**
	 * @name browser
	 * @var array
	 * used for detecting the browser
	 */
	private static $browser = array('chrome', 'firefox', 'msie', 'msie7', 'msie8', 'msie9', 'opera', 'safari', 'webkit');

	/**
	 * @name body_tab
	 * @return array os, browser, agent
	 */
	private static function body_tab () {
		$os = '';
		$browser = array();
		$agent = str_replace(' ', '', strtolower($_SERVER['HTTP_USER_AGENT']));

		foreach (self::$os as $system) {
			if (strpos($agent, $system) !== false) {
				$os = $system;
				break;
			}
		}

		foreach (self::$browser as $system) {
			if (strpos($agent, $system) !== false) {
				$browser[] = $system;
			}
		}

		return array(
			$os,
			implode($browser, ' '),
			$agent
		);
	}

	/**
	 * @name start
	 * @return string start of view page html
	 */
	public static function start () {
		return "<!doctype html>\n<html>\n\t<head>\n\n";
	}

	/**
	 * @name content
	 * @return string start of view page content html
	 */
	public static function content () {
		list($os, $browser, ) = self::body_tab();
		return "\n\n\t</head>\n\t<body class='{$os} {$browser}'>\n";
	}

	/**
	 * @name done
	 * @return string end of view page content and html
	 */
	public static function done () {
		return "\n\t</body>\n</html>";
	}

	/**
	 * @name scripts
	 * @return string script tags for requested javascript files
	 */
	public static function scripts () {
		return PHP_EOL . implode(Resource::$scripts, PHP_EOL) . PHP_EOL;
	}
}


/**
 * @name Resource
 * resource file helper class
 */
class Resource {
	/**
	 * @name extension
	 * @var regex string used to get a file's extension
	 */
	private static $extension = '/^.+\.(.+)$/';

	/**
	 * @name scripts
	 * @var array
	 * javascript files requested
	 */
	public static $scripts = array();

	/**
	 * @name add
	 * @param files* to load/include
	 */
	public static function add () {
		for ($i = 0, $max = func_num_args(); $i < $max; $i++) {
			$url = func_get_arg($i);
			preg_match(self::$extension, $url, $extension);
			$extension = $extension[ 1 ];
			$url = Fabrico::get_resource_file($url, $extension);

			switch ($extension) {
				case 'js':
					self::$scripts[] = HTML::el('script', array(
						'type' => 'text/javascript',
						'src' => $url
					));
					break;

				case 'css':
					echo HTML::el('link', array(
						'rel' => 'stylesheet',
						'type' => 'text/css',
						'href' => $url
					));
					break;

				default:
					echo HTML::el('link', array(
						'rel' => 'alternative',
						'type' => 'text',
						'href' => $url
					));
					break;
			}
		}

		return '';
	}
}

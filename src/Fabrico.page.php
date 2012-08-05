<?php

define('__HTML__', Fabrico::get_id());
define('__CSS__', '<link rel="other" />');
define('__ERROR__', '<span class="errorlist"></span>');

/**
 * @name FabricoPage
 * page template helpder functions
 */
class FabricoPage {
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

	public static function autocompletedata ($data, $id, $text, $description = '') {
		$options = array();

		if (is_array($data) && count($data)) {
			foreach ($data as $temp) {
				$result = new stdClass;
				$item = $temp instanceof FabricoModelInstance ? $temp->getdata() : $temp;

				$result->text = FabricoParser::merge($text, $item);
				$result->desc = FabricoParser::merge($description, $item);
				$result->id = FabricoParser::merge($id, $item);

				$options[] = $result;
			}
		}

		return $options;
	}

	/**
	 * @name start
	 * @return string start of view page html
	 */
	public static function start () {
		ob_start();
		echo "<!doctype html>\n<html>\n\t<head>\n\n" . __CSS__;
	}

	/**
	 * @name content
	 * @return string start of view page content html
	 */
	public static function content () {
		list($os, $browser, ) = self::body_tab();
		echo "\n\n\t</head>\n\t<body class='{$os} {$browser}'>\n" . __ERROR__;
	}

	/**
	 * @name done
	 * @return string end of view page content and html
	 */
	public static function done () {
		$errors = FabricoError::getall();
		$scripts = self::scripts();
		$styles = self::links();

		echo "{$scripts}\n\t</body>\n</html>";
		$body = ob_get_clean();
		$body = str_replace(__CSS__, self::links(), $body);
		$body = str_replace(__ERROR__, $errors, $body);
		echo $body;
	}

	/**
	 * @name links
	 * @return string of link elements to include in page's head
	 */
	public static function links () {
		return PHP_EOL . implode(FabricoPageResource::$styles, PHP_EOL) . PHP_EOL;
	}

	/**
	 * @name scripts
	 * @return string script tags for requested javascript files
	 */
	public static function scripts () {
		$files = PHP_EOL . implode(FabricoPageResource::$scripts, PHP_EOL) . PHP_EOL;
		$code = PHP_EOL . implode(FabricoPageResource::$onreadylist, PHP_EOL) . PHP_EOL;
		$onready = html::el('script', array(
			'type' => 'text/javascript',
			'content' => <<<JS

$(function () {
{$code}
});

JS
		));

		return $files . (trim($code) ? $onready : '');
	}
}

/**
 * @name FabricoPageResource
 * resource file helper class
 */
class FabricoPageResource {
	/**
	 * @name EXT_JS
	 * @constant string
	 */
	const EXT_JS = 'js';

	/**
	 * @name EXT_CSS
	 * @constant string
	 */
	const EXT_CSS = 'css';

	/**
	 * @name EXT_IMG
	 * @constant string
	 */
	const EXT_IMG = 'img';

	/**
	 * @name extension
	 * @var regex string used to get a file's extension
	 */
	private static $extension = '/^.+\.(.+)$/';

	/**
	 * @name styles
	 * @var array
	 * css files requested
	 */
	public static $styles = array();

	/**
	 * @name scripts
	 * @var array
	 * javascript files requested
	 */
	public static $scripts = array();

	/**
	 * @name onreadylist
	 * @var array of code
	 */
	public static $onreadylist = array();

	/**
	 * @name onready
	 * @param string code
	 */
	public static function onready ($code) {
		self::$onreadylist[] = $code;
	}

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
				case self::EXT_JS:
					self::$scripts[] = html::el('script', array(
						'type' => 'text/javascript',
						'src' => $url
					));
					break;

				case self::EXT_CSS:
					self::$styles[] = html::el('link', array(
						'rel' => 'stylesheet',
						'type' => 'text/css',
						'href' => $url
					));
					break;

				default:
					self::$styles[] = html::el('link', array(
						'rel' => 'alternative',
						'type' => 'text',
						'href' => $url
					));
					break;
			}
		}
	}

	/**
	 * @name internal
	 * @param string file name
	 * @return string internal file name
	 */
	public static function internal ($file) {
		return Fabrico::PATH_INTERNAL_STR . $file;
	}
}

/**
 * @name template
 * @param string template name
 * @return string template file path
 */
function template ($template) {
	return Fabrico::get_template_file($template);
}

/**
 * @name action
 * @param string action name
 */
function action () {
	for ($i = 0, $max = func_num_args(); $i < $max; $i++) {
		require_once Fabrico::get_action_file(func_get_arg($i));
	}
}

/**
 * @name element
 * @param string element name
 */
function element () {
	for ($i = 0, $max = func_num_args(); $i < $max; $i++) {
		require_once Fabrico::get_element_file(
			str_replace('\\', DIRECTORY_SEPARATOR, func_get_arg($i))
		);
	}
}

/**
 * @name redirect
 * @param string view file path
 * @param boolean include query in redirect
 */
function redirect ($file, $include_query = false) {
	$query = array();

	if ($include_query) {
		foreach (Fabrico::$req as $key => $value) {
			if (in_array($key, array(Fabrico::$uri_query_file))) {
				continue;
			}

			$query[] = $key . '=' . $value;
		}
	}

	if (count($query)) {
		$file .= '?' . implode('&', $query);
	}

	header("Location: {$file}");
}

/**
 * @name content
 * @return string
 */
function content () {
	return FabricoPage::content();
}

/**
 * @name req
 * @param string query key
 * @return string query value
 * @see Fabrico::req
 */
function req ($key) {
	return Fabrico::req($key);
}

/**
 * @name imgsrc
 * @param string image name
 * @return string image URL
 */
function imgsrc ($loc) {
	return Fabrico::get_resource_file($loc, FabricoPageResource::EXT_IMG);
}

/**
 * create a sequence of br tags
 *
 * @name br
 * @param int numer of br tags to create
 */
function br ($num = 1) {
	$str = '';

	for ($i = 0; $i < $num; $i++)
			$str .= '<br />';

	return $str;
}

/**
 * create a sequence of spaces
 *
 * @name space
 * @param int numer of spaces to create
 */
function space ($num = 1) {
	$str = '';

	for ($i = 0; $i < $num; $i++)
			$str .= '&nbsp;';

	return $str;
}

/**
 * @name corejsfile
 * @param string* file source
 * @see FabricoPageResource::add
 */
function corejsfile ($src) {
	$files = func_get_args();

	foreach ($files as $index => $file)
		$files[ $index ] = corefile($file);

	call_user_func_array(array('FabricoPageResource', 'add'), $files);
}

/**
 * @name jsfile
 * @param string* file source
 * @see FabricoPageResource::add
 */
function jsfile ($src) {
	call_user_func_array(array('FabricoPageResource', 'add'), func_get_args());
}

/**
 * @name corecssfile
 * @param string* file href
 * @see FabricoPageResource::add
 */
function corecssfile ($href) {
	$files = func_get_args();

	foreach ($files as $index => $file)
		$files[ $index ] = corefile($file);

	call_user_func_array(array('FabricoPageResource', 'add'), $files);
}

/**
 * @name cssfile
 * @param string* file href
 * @see FabricoPageResource::add
 */
function cssfile ($href) {
	call_user_func_array(array('FabricoPageResource', 'add'), func_get_args());
}

/**
 * @name corefile
 * @param string file name
 * @return string internal file identifier
 * @see FabricoPageResource::internal
 */
function corefile ($name) {
	return FabricoPageResource::internal($name);
}

/**
 * @name startpage
 * @see FabricoPage::start
 */
function startpage () {
	FabricoPage::start();
}

/**
 * @name endpage
 * @see FabricoPage::done
 */
function endpage () {
	FabricoPage::done();
}

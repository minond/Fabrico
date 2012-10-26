<?php

/**
 * @package fabrico\page
 */
namespace fabrico\page;

use fabrico\core\Module;
use fabrico\page\MergeToken;

/**
 * page manager
 * @uses MergeToken
 */
class Page extends Module {
	/**
	 * @var string
	 */
	private $html;

	/**
	 * @var View
	 */
	public $view;

	/**
	 * page template
	 * @var string
	 */
	public static $template;

	/**
	 * title
	 * @var string
	 */
	public $title;

	/**
	 * content
	 * @var string
	 */
	public $content;

	/**
	 * css files
	 * @var array
	 */
	private $css_file = [];

	/**
	 * css code
	 * @var array
	 */
	private $css_code = [];

	/**
	 * js files
	 * @var array
	 */
	private $js_file = [];

	/**
	 * js code
	 * @var array
	 */
	private $js_code = [];

	/**
	 * @return string
	 */
	private function get_css_file () {
		return implode('', $this->css_file);
	}

	/**
	 * @return string
	 */
	private function get_css_code () {
		return implode('', $this->css_code);
	}

	/**
	 * @return string
	 */
	private function get_js_file () {
		return implode('', $this->js_file);
	}

	/**
	 * @return string
	 */
	private function get_js_code () {
		return implode('', $this->js_code);
	}

	/**
	 * merges the template with user data
	 * @return string
	 */
	public function render () {
		return MergeToken::merge(self::$template, [
			'title' => $this->title,
			'content' => $this->content,
			'css-file' => $this->get_css_file(),
			'css-code' => $this->get_css_code(),
			'js-file' => $this->get_js_file(),
			'js-code' => $this->get_js_code()
		]);
	}
}

Page::$template = <<<HTML
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>#{title}</title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width">
		#{css-file}
		#{css-code}
	</head>
	<body>
		#{content}
		#{js-file}
		#{js-code}
	</body>
</html>
HTML;

<?php

/**
 * @package fabrico\page
 */
namespace fabrico\page;

use fabrico\core\Module;

/**
 * page manager
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

	public static $template;

	public function render () {
		return self::$template;
		return $this->html;
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
		#{css}
	</head>
	<body>
		#{content}
		#{js-src}
		#{js-code}
	</body>
</html>
HTML;

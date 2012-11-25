<?php

/**
 * @package fabrico\output
 */
namespace fabrico\output;

use fabrico\core\util;
use fabrico\core\Module;
use fabrico\core\Project;
use fabrico\output\MergeToken;

/**
 * page manager
 * @uses MergeToken
 */
class Page extends OutputContent {
	/**
	 * variables types
	 */
	const NONE = 0;
	const STR = 1;
	const JSON = 2;

	/**
	 * title
	 * @var string
	 */
	private $title = '';

	/**
	 * content
	 * @var string
	 */
	private $content = '';

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
	 * js code
	 * @var array
	 */
	private $js_load = [];

	/**
	 * templates
	 * @var array
	 */
	private static $template_map = [];

	/**
	 * add a template
	 * @param string $name
	 * @param string $content
	 * @param boolean $allow_overwrite
	 */
	public static function set_template ($name, $content, $allow_overwrite = false) {
		if (!isset(self::$template_map[ $name ]) || $allow_overwrite) {
			self::$template_map[ $name ] = $content;
		}
	}

	/**
	 * @param array $what
	 * @return string
	 */
	private function stdjoin (array $what) {
		return implode("\n", $what);
	}

	/**
	 * @return string
	 */
	private function get_css_file () {
		return $this->stdjoin($this->css_file);
	}

	/**
	 * @return string
	 */
	private function get_css_code () {
		return $this->stdjoin($this->css_code);
	}

	/**
	 * @return string
	 */
	private function get_js_file () {
		return $this->stdjoin($this->js_file);
	}

	/**
	 * @return string
	 */
	private function get_js_code () {
		return $this->stdjoin($this->js_code);
	}

	/**
	 * @return string
	 */
	private function get_js_load () {
		return $this->stdjoin($this->js_load);
	}

	/**
	 * @param string $href
	 */
	public function add_css_file ($href) {
		$this->css_file[] = "<link rel='stylesheet' href='{$href}' />";
	}

	/**
	 * @param string $css
	 */
	public function add_css_code ($css) {
		$this->css_code[] = $css;
	}

	/**
	 * @param string $src
	 */
	public function add_js_file ($src) {
		$this->js_file[] = "<script type='text/javascript' src='{$src}'></script>";
	}

	/**
	 * @param string $js
	 */
	public function add_js_code ($js) {
		$this->js_code[] = $js;
	}

	/**
	 * @param string $js
	 */
	public function add_js_load ($js) {
		$this->js_load[] = $js;
	}

	/**
	 * title getter
	 * @return string
	 */
	public function get_title () {
		return $this->title;
	}

	/**
	 * title setter
	 * @param string $title
	 */
	public function set_title ($title) {
		$this->title = $title;
	}

	/**
	 * content getter
	 * @return string
	 */
	public function get_content () {
		return $this->content;
	}

	/**
	 * content setter
	 * @param string $content
	 */
	public function set_content ($content) {
		$this->content = $content;
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @param string $type
	 */
	public function declare_var ($name, $value, $type = self::NONE) {
		switch ($type) {
			case self::JSON:
				$value = json_encode($value);
				$value = "({$value})";
				break;

			case self::STR:
				$value = addslashes($value);
				$value = "\"{$value}\"";
				break;
		}

		$this->js_code[] = "window.{$name} = {$value};";
	}

	/**
	 * @param string $view
	 */
	public function load ($view) {
		$this->content = $this->view->get($view, Project::VIEW);
	}

	/**
	 * merges the template with user data
	 * @return string
	 */
	public function render ($type) {
		return MergeToken::merge(self::$template_map[ $type ], [
			'title' => $this->title,
			'content' => $this->content,
			'css-file' => $this->get_css_file(),
			'css-code' => $this->get_css_code(),
			'js-file' => $this->get_js_file(),
			'js-code' => $this->get_js_code(),
			'js-load' => $this->get_js_load()
		]);
	}

	/**
	 * standard page build preparation
	 * manages tokens and check the generated html
	 * @param strign $content
	 * @return string
	 */
	public function prepare ($content) {
		$this->core->loader->load('parse');
		$project = $this->core->project;
		$conf = $this->configuration;
		$parser = new Parser;
		$lexer = new Lexer;

		$lexer->set_string($content);
		$lexer->add_token(new TagToken);
		$lexer->add_token(new MergeToken);

		return $parser->parse($lexer, function ($orig, & $html, $tokens) use (& $project, & $conf) {
			$includes = [];

			foreach ($tokens as & $token) {
				if ($token instanceof TagToken) {
					$elfile = implode(DIRECTORY_SEPARATOR, [
						$token->package,
						$token->namespace,
						$token->name
					]);

					list($projectfile, $in_project) = $project->got_file($elfile, Project::ELEMENT);
					list($fabricofile, $in_fabrico) = $project->got_project_file(
						$elfile, Project::ELEMENT,
						$conf->core->file->to->elements
					);

					if ($in_project) {
						$includes[] = $projectfile;
					}
					else if ($in_fabrico) {
						$includes[] = $fabricofile;
					}
					else {
						// not found
					}
				}

				unset($token);
			}

			$includes = array_unique($includes);
			foreach ($includes as $index => $file) {
				$includes[ $index ] = sprintf('include_once "%s";', $file);
			}

			$includes = implode("\n", $includes);
			$html = <<<HTML
<?php
{$includes}
?>{$html}
HTML;
		});
	}
}

Page::set_template('txt', '#{content}');
Page::set_template('html', <<<HTML
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width">
		#{css-file}
		<title>#{title}</title>
		<style type="text/css">#{css-code}</style>
	</head>
	<body class="no-js">
		#{content}#{js-file}<script type="text/javascript">
		// clear no-js class
		document.body.className = document.body.className.replace("no-js", "js");
		#{js-code}
		if (window.jQuery) {
			$(function () {
				#{js-load}
			});
		}
		</script>
	</body>
</html>
HTML
);

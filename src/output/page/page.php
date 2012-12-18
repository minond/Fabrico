<?php

/**
 * @package fabrico\output
 */
namespace fabrico\output;

use fabrico\core\util;
use fabrico\core\Module;
use fabrico\project\Project;
use fabrico\output\Tag;
use fabrico\output\TagToken;
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
	 * set by the parser
	 * @var string
	 */
	private $raw_content = '';

	/**
	 * set by the parser
	 * @var string
	 */
	private $parsed_content = '';

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
	 * @param string $raw
	 * @param string $parsed
	 */
	public function set_contents($raw, $parsed) {
		$this->raw_content = $raw;
		$this->parsed_content = $parsed;
	}

	/**
	 * @return array
	 */
	public function get_contents() {
		return [ $this->raw_content, $this->parsed_content ];
	}

	/**
	 * @param array $what
	 * @return string
	 */
	private function stdjoin (array $what) {
		return implode("\n", array_unique($what));
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
		return util::merge(self::$template_map[ $type ], [
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
		$page = $this;
		$project = $this->core->project;
		$conf = $this->configuration;
		$parser = new Parser;
		$lexer = new Lexer;

		// remove comments
		$content = preg_replace('/<!--.+?-->/ms', '', $content);

		$lexer->set_string($content);
		$lexer->add_token(new TagToken);
		$lexer->add_token(new MergeToken);
		$lexer->add_token(new FunctionToken);

		return $parser->parse($lexer, function ($orig, & $html, $tokens) use (& $page, & $project, & $conf) {
			$includes = [];
			$page->set_contents($orig, $html);

			foreach ($tokens as & $token) {
				if ($token instanceof TagToken) {
					$infile = Tag::load_project_file([
						$token->package,
						$token->namespace,
						$token->name
					]);

					if ($infile) {
						$includes[] = $infile;
					}

					$tag = Tag::getclass(
						$token->package,
						$token->namespace,
						$token->name
					);

					$tag = new $tag(
						$token->property_token->properties,
						$token->type
					);

					// real tag?
					if ($tag instanceof Tag) {
						if ($token->type === TagToken::OPEN) {
							$tag->sets($token->property_token->properties);
						}

						$replace = $tag->assemble();

						if (strlen($replace)) {
							$html = str_replace(
								$token->replacement,
								$replace, $html
							);
						}
					}

					unset($tag);
				}

				unset($token);
			}

			$includes = array_unique($includes);
			foreach ($includes as $index => $file) {
				$includes[ $index ] = sprintf('include_once "%s";', $file);
			}

			if (count($includes)) {
				$includes = implode("\n", $includes);
				$html = <<<HTML
<?php
{$includes}
?>{$html}
HTML;
			}
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

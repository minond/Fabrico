<?php

/**
 * @package fabrico\page
 */
namespace fabrico\page;

use fabrico\core\util;
use fabrico\core\Module;
use fabrico\core\Project;
use fabrico\page\MergeToken;

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
	public $title = '';

	/**
	 * content
	 * @var string
	 */
	public $content = '';

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
	 * @param array $what
	 * @return string
	 */
	private function stdjoin (array $what) {
		return "\n" . implode("\n", $what);
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
	public function get ($view) {
		$this->content = $this->view->get($view, Project::VIEW);
	}

	/**
	 * merges the template with user data
	 * @return string
	 */
	public function render ($type) {
		return MergeToken::merge($this->configuration->core->templates->{ $type }, [
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
		$this->core->core->load('parse');
		$parser = new Parser;
		$lexer = new Lexer;

		$lexer->set_string($content);
		$lexer->add_token(new TagToken);
		$lexer->add_token(new MergeToken);

		return $parser->parse($lexer);
	}
}

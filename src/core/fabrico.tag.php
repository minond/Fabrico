<?php

namespace Fabrico;

class Tag {
	/**
	 * parse errors
	 */
	const ERROR_UNKNOWN_TAG = 'unknown tag';

	/**
	 * tag format
	 */
	const ROOT = 'f';
	const CUSTOM = 'c';
	const SEPARATOR = ':';
	const MAX_ITERATIONS = 1000;

	/**
	 * tag to method conversion
	 */
	const METHOD_PREFIX = 'view\\';
	const METHOD_SINGLE_SUFFIX = '::generate';
	const METHOD_OPEN_SUFFIX = '::open';
	const METHOD_CLOSE_SUFFIX = '::close';

	/**
	 * tag types
	 */
	const TAG_SINGLE = 1;
	const TAG_OPEN = 2;
	const TAG_CLOSE = 3;

	/**
	 * tag matching regular expressions
	 */
	const TAG_MATCH_CLOSE = '/\<\/%s:(.+?):(.+?)\>/';
	const TAG_MATCH_OPEN = '/\<%s:(.+?):(.+?[^\/]?)\>/';
	const TAG_MATCH_SINGLE = '/\<%s:(.+?):(.+?)\/\>/';
	const TAG_MATCH_METHOD = '/\<%s:(\w+?)\s(.+?)\/\>/';
	const TAG_MATCH_METHOD_EMPTY = '/\<%s:(\w+?)(\s{0,})\/\>/';

	/**
	 * expected match count
	 */
	const EXPECTED_MATCHES = 3;

	/**
	 * @see $includes
	 */
	const INCLUDE_FILE = 'include ';

	/**
	 * custom tags
	 * @var array
	 */
	private static $custom_tags = [];

	/**
	 * methods that return a file path to be included
	 * @var array
	 */
	public static $includes = [ 'template' ];

	/**
	 * tracks declared tags and their settings
	 * @var array
	 */
	public static $tags = [];

	/**
	 * tag namespace <root:namespace:name />
	 * @var string
	 */
	public $namespace;

	/**
	 * tag name <root:namespace:name />
	 * @var string
	 */
	public $name;

	/**
	 * full tag name <root:namespace:name />
	 * @var string
	 */
	public $tag;

	/**
	 * builds tag string
	 */
	private function build () {
		if (!$this->name) {
			$this->name = explode('\\', get_class($this));
			$this->name = $this->name[ count($this->name) - 1 ];
		}

		$this->tag = self::ROOT . self::SEPARATOR;

		if ($this->namespace) {
			$this->tag .= $this->namespace . self::SEPARATOR;
		}
		
		$this->tag .= $this->name;

		if (!array_key_exists($this->tag, self::$tags)) {
			self::$tags[ $this->tag ] = & $this;
		}
	}

	/**
	 * creates a new tag and saves it to the tag list
	 *
	 * @param string array of properties
	 */
	public static function register ($props) {
		$tag = new self;
		
		foreach ($props as $prop => $value) {
			$tag->{ $prop } = $value;
		}

		$tag->build();
	}

	/**
	 * checks if tag name has been declared
	 *
	 * @param string tag name
	 * @return boolean valid tag name
	 */
	public static function valid ($tag) {
		return array_key_exists(trim($tag), self::$tags);
	}

	/**
	 * parses an html string for custom tags and returns the same
	 * string with the appropriate php function calls/code
	 *
	 * @param string raw html
	 * @param string clean html
	 */
	public static function parse ($html) {
		// signature trackers and clean tag data
		$start_time = time();
		$taginfo = [];

		// parse for fabrico tags
		self::scan_string($html, sprintf(self::TAG_MATCH_METHOD_EMPTY, self::ROOT), self::TAG_SINGLE, $taginfo);
		self::scan_string($html, sprintf(self::TAG_MATCH_METHOD, self::ROOT), self::TAG_SINGLE, $taginfo);
		self::scan_string($html, sprintf(self::TAG_MATCH_SINGLE, self::ROOT), self::TAG_SINGLE, $taginfo);
		self::scan_string($html, sprintf(self::TAG_MATCH_OPEN, self::ROOT), self::TAG_OPEN, $taginfo);
		self::scan_string($html, sprintf(self::TAG_MATCH_CLOSE, self::ROOT), self::TAG_CLOSE, $taginfo);

		// parse for custom tags
		self::scan_string($html, sprintf(self::TAG_MATCH_SINGLE, self::CUSTOM), self::TAG_SINGLE, $taginfo, true);
		self::scan_string($html, sprintf(self::TAG_MATCH_OPEN, self::CUSTOM), self::TAG_OPEN, $taginfo, true);
		self::scan_string($html, sprintf(self::TAG_MATCH_CLOSE, self::CUSTOM), self::TAG_CLOSE, $taginfo, true);

		// merge tags into the html code
		return self::clean_string($html, $taginfo, $start_time);
	}

	/**
	 * returns an html string with merged tags and build signature
	 *
	 * @param string raw html
	 * @param array parsed tag information
	 * @param int parse start time
	 * @return clean html string
	 */
	private static function clean_string ($html, $taginfo, $start_time) {
		list($html, $invalidtags) = self::merge_string($html, $taginfo);
		return $html . self::build_signature($start_time, $invalidtags);
	}

	/**
	 * merges custom tags into an html string
	 *
	 * @param string raw html
	 * @param array parsed tag information
	 * @return array clean html, invalid tags found
	 */
	private static function merge_string ($html, $taginfo) {
		$invalidtags = 0;
		$mergedlist = [];

		foreach ($taginfo as $info) {
			if (!$info->valid) {
				$invalidtags++;
			}

			if (!in_array($info->match_string, $mergedlist)) {
				$html = str_replace(
					$info->match_string,
					$info->replacement_string,
					$html
				);

				$mergedlist[] = $info->match_string;
			}
		}
	
		return [ $html, $invalidtags ];
	}

	/**
	 * parses an html string for custom fabrico tags
	 * while parsing the string, matching tag information is saved
	 * so that it can be merged into the document
	 *
	 * @param string html to parse
	 * @param string tag match regular expression
	 * @param int expected matched from regular expression
	 * @param int tag type search
	 * @param array storage reference
	 */
	private static function scan_string ($html, $match_regex, $tag_type, & $tag_storage, $custom = false) {
		$lastoffset = 0;

		for ($i = 0; $i < self::MAX_ITERATIONS; $i++) {
			preg_match($match_regex, $html, $matches, PREG_OFFSET_CAPTURE, $lastoffset);

			if (count($matches) === self::EXPECTED_MATCHES) {
				$lastoffset = strlen($matches[ 0 ][ 0 ]) + $matches[ 0 ][ 1 ];
				$tag_storage[] = self::build_tag($matches, $tag_type, $custom);
			}
			else if (!count($matches)) {
				break;
			}
		}
	}

	/**
	 * parses a string of attributes and separates
	 * them into attribute value pais
	 *
	 * @param string
	 * @return string
	 */
	private static function separate_attributes ($attr_str) {
		$attr_str = trim($attr_str);
		$parts = explode('=', $attr_str);
		$attrs = [];
		$max = count($parts) - 1;
		$attrsep = '=';

		foreach ($parts as $index => $part) {
			// first attribute check
			if (!$index) {
				$attrs[ $index ] = trim($part);
			}

			// last value check
			else if ($index === $max) {
				$attrs[ $index - 1 ] .= trim($attrsep . $part);
			}

			// in between
			else {
				$sepspace = strrpos($part, ' ');
				$last_value = substr($part, 0, $sepspace);
				$next_attr = substr($part, $sepspace, strlen($part));

				$attrs[ $index - 1 ] .= trim($attrsep . $last_value);
				$attrs[ $index ] = trim($next_attr);
			}
		}

		return $attrs;
	}

	/**
	 * given an array of string tag matches it returns needed information
	 * to parse the tag into an html document
	 *
	 * @param array tag match information
	 * @param integer tag type (open, close, single)
	 * @param boolean custom string tag
	 * @return object tag replacement information
	 */
	private static function build_tag ($matchinfo, $type, $custom = false) {
		$is_method = count(explode(':', explode(' ', $matchinfo[ 0 ][ 0 ], 2)[ 0 ])) === 2;
		$tag = new \stdClass;
		$rawtag = $matchinfo[ 0 ][ 0 ];
		$attrstring = explode(' ', $matchinfo[ 2 ][ 0 ], 2);
		
		if ($is_method) {
			$attrs = self::separate_attributes(
				$matchinfo[ 2 ][ 0 ]
			);

			$tagname = $matchinfo[ 1 ][ 0 ];
		}
		else {
			$attrs = self::separate_attributes(
				count($attrstring) === 2 ?
				$attrstring[ 1 ] : ''
			);

			if ($custom) {
				$namespace = $matchinfo[ 1 ][ 0 ];
				$tagname = $attrstring[ 0 ];
			}
			else {
				$tagname = self::ROOT . self::SEPARATOR .
				           $matchinfo[ 1 ][ 0 ] . self::SEPARATOR .
						   $attrstring[ 0 ];
			}
		}


		$tag->tag = $tagname;
		$tag->valid = self::valid($tagname);
		$tag->attrs = self::build_attributes($attrs);
		$tag->match_string = $rawtag;

		$tag->replacement_comment = self::tag2comment(
			$rawtag, $type,
			self::ERROR_UNKNOWN_TAG
		);

		if (!$custom) {
			$tag->replacement_string = self::method2code(
				self::tag2method($tagname, $type, $is_method) .
				self::args2string($tag->attrs, $type, $is_method),
				$is_method
			);
		}
		else {
			$tag->replacement_string = self::build_custom_tag(
				$namespace,
				$tagname,
				$type,
				$tag->attrs
			);
		}

		return $tag;
	}

	/**
	 * builds a custom tag
	 *
	 * @param string tag namespace
	 * @param string tag name
	 * @param integer tag type
	 * @param array of attributes
	 * @return string tag string
	 */
	public static function build_custom_tag ($namespace, $tagname, $type, $attrs = []) {
		if (!self::tag_exist($namespace, $tagname)) {
			return self::invalid_tag_error($namespace, $tagname);
		}

		return call_user_func_array(
			self::$custom_tags[ $namespace ][ $tagname ],
			[ $type, $attrs, function ($label, $clean = false) use (& $attrs) { return self::attr_val($attrs, $label, $clean); } ]
		);
	}

	/**
	 * generates an invalid tag error
	 * 
	 * @param string tag namespace
	 * @param string tag name
	 * @return string invalid tag error
	 */
	public static function invalid_tag_error ($namespace, $tagname) {
		return self::error("Invalid tag {$namespace}:{$tagname}");
	}

	/**
	 * generates an error tag
	 *
	 * @param string message
	 * @return string error tag
	 */
	public static function error ($msg) {
		return "<? throw new Exception('Tag Error: {$msg}'); ?>";
	}

	/**
	 * check if a custom tag has been registered
	 *
	 * @param string tag namespace
	 * @param string tag name
	 * @return boolean tag is registered
	 */
	private static function tag_exist ($namespace, $tagname) {
		return isset(self::$custom_tags[ $namespace ]) &&
		       isset(self::$custom_tags[ $namespace ][ $tagname ]);
	}

	/**
	 * resisters a custom tag
	 *
	 * @param string tag namespace
	 * @param string tag name
	 * @param callable tag builder
	 */
	public static function register_tag ($namespace, $tagname, $builder) {
		if (!isset(self::$custom_tags[ $namespace ])) {
			self::$custom_tags[ $namespace ] = [];
		}

		self::$custom_tags[ $namespace ][ $tagname ] = $builder;
	}

	/**
	 * parses a string of attributes and returns a list
	 * of attribute information
	 *
	 * @param array of raw attribute string
	 * @return array of clean attribute info
	 */
	private static function build_attributes ($attrs) {
		$list = [];

		foreach ($attrs as $attr) {
			if (!$attr) {
				continue;
			}

			$attrinfo = explode('=', $attr);
			$attribute = new \stdClass;

			if (count($attrinfo) == 1) {
				$attrinfo[ 1 ] = 'true';
			}

			$attribute->label = $attrinfo[ 0 ];
			$attribute->value = self::parse_value($attrinfo[ 1 ]);
			$list[] = $attribute;
		}

		return $list;
	}

	/**
	 * parses a string value and returns a value that can
	 * be more easily used in php
	 *
	 * @param string val
	 * @return midex
	 */
	private static function parse_value ($val) {
		// number
		$n_val = str_replace([ '"', '\'' ], '', $val);
		if (is_numeric($n_val)) {
			$val = $n_val;
			return $val;
		}

		// boolean
		switch ($val) {
			case '\'true\'':
			case '"true"':
				return 'true';
			case '\'false\'':
			case '"false"':
				return 'false';
			default:
				return Merge::output_controller_placeholder($val, true);
		}
	}

	/**
	 * build signature
	 *
	 * @param int parse start time
	 * @param int invalid tag count
	 * @return string
	 */
	private static function build_signature ($start, $invalid) {
		$date = date('Y-m-d H:i:s');
		$time = time() - $start;
		return "\n\n<!-- build date: {$date} -->" .
		       "\n<!-- build time: {$time} sec -->";
			   // ignore invalid tags since nothing has loaded and
			   // they're all tags as invalid anyway.
		       // "\n<!-- invalid tags: {$invalid} -->";
	}

	/**
	 * wraps an html line in html comment tags
	 *
	 * @param string tag
	 * @param int tag type
	 * @param string parse error message
	 * @return string comment
	 */
	private static function tag2comment ($tag, $type, $message) {
		$msg = "parse error: {$message}";

		switch ($type) {
			case self::TAG_CLOSE:
			case self::TAG_SINGLE:
			case self::TAG_OPEN:
				return "<!-- ({$msg}) {$tag} -->";
		}
	}

	/**
	 * converts an array of arguments into a string 
	 * representing the same array
	 *
	 * @param array of arguments
	 * @param int tag type
	 * @param boolean is method tag flag
	 * @return string valid php array representation
	 */
	private static function args2string ($args, $tagtype, $is_method) {
		$props = [];
		$arg_list = true;

		if ($tagtype === self::TAG_CLOSE) {
			return '()';
		}

		foreach ($args as $arg) {
			if ($is_method && $arg->label === '_') {
				$props[] = "{$arg->value}";
			}
			else {
				$props[] = "'{$arg->label}' => {$arg->value}";
				$arg_list = false;
			}
		}

		$props = implode(', ', $props);
		return $arg_list ? "({$props})" : "((object) [ {$props} ])";
	}

	/**
	 * takes a tag name and returns the Element method call
	 *
	 * @param string tag name
	 * @param integer tag type (open, close, single)
	 * @param boolean method tag
	 * @return string method name
	 */
	private static function tag2method ($tagname, $type, $is_method) {
		$tagname = str_replace(
			[ self::ROOT . self::SEPARATOR, self::SEPARATOR ],
			[ '', '\\' ], $tagname
		);

		if (!$is_method) {
			switch ($type) {
				case self::TAG_SINGLE:
					$tagname .= self::METHOD_SINGLE_SUFFIX;
					break;

				case self::TAG_OPEN:
					$tagname .= self::METHOD_OPEN_SUFFIX;
					break;

				case self::TAG_CLOSE:
					$tagname .= self::METHOD_CLOSE_SUFFIX;
					break;
			}
		}

		return (in_array($tagname, self::$includes) ? self::INCLUDE_FILE : '') . self::METHOD_PREFIX . $tagname;
	}

	/**
	 * returns the value of an attribute
	 *
	 * @param array of attributes
	 * @param string attribute label
	 * @param boolean clean string
	 * @return string attribute value
	 */
	public static function attr_val (& $attrs, $label, $clean = false) {
		foreach ($attrs as $index => $attr) {
			if ($attr->label === $label) {
				if ($clean) {
					return preg_replace([ '/^"|\'/', '/"|\'$/' ], '', $attr->value);
				}
				else {
					return $attr->value;
				}
			}
		}

		return null;
	}

	/**
	 * returns a php line wrapped in php tags
	 *
	 * @param string method
	 * @param is method tag
	 * @return string code
	 */
	private static function method2code ($method, $is_method) {
		return $is_method ? "<? {$method} ?>" : "<?= {$method} ?>";
	}

	/**
	 * @see method2code
	 *
	 * @param string method
	 * @return string code
	 */
	public static function code ($method) {
		return self::method2code($method, true);
	}

	/**
	 * @see method2code
	 *
	 * @param string method
	 * @return string code
	 */
	public static function output ($method) {
		return self::method2code($method, false);
	}
}


Tag::register_tag('fn', 'loop', function ($type, $attrs, $attr) {
	switch ($type) {
		case Tag::TAG_SINGLE:
			return Tag::error('Loop tags cannot be self-closing');

		case Tag::TAG_CLOSE:
			return Tag::code('endforeach');

		case Tag::TAG_OPEN:
			if ($attr('index')) {
				return Tag::code(Merge::parse('foreach (#{data} as $#{index} => $#{key}):', [
					'data' => $attr('over'),
					'index' => $attr('index'),
					'key' => $attr('key')
				]));
			}
			else {
				return Tag::code(Merge::parse('foreach (#{data} as $#{key}):', [
					'data' => $attr('over'),
					'key' => $attr('key')
				]));
			}
	}
});

Tag::register_tag('fn', 'conditional', function ($type, $attrs, $attr) {
	switch ($type) {
		case Tag::TAG_CLOSE:
			return Tag::code('endif');

		case Tag::TAG_SINGLE:
		case Tag::TAG_OPEN:
			$tag = Tag::TAG_OPEN ? 'if' : 'elseif';

			if ($attr('yes')) {
				return Tag::code(Merge::parse('#{tag} (#{check}):', [
					'check' => $attr('yes'),
					'tag' => $tag
				]));
			}
			else if ($attr('no')) {
				return Tag::code(Merge::parse('#{tag} (!#{check}):', [
					'check' => $attr('no'),
					'tag' => $tag
				]));
			}
			else if ($type === Tag::TAG_SINGLE) {
				return Tag::code('else:');
			}
			else {
				return Tag::error('Conditional tag (no self-closing) requires a check type');
			}
	}
});

Tag::register_tag('js', 'obj', function ($type, $attrs, $attr) {
	if ($attr('_')) {
		return Tag::output("json_encode({$attr('_')})");
	}
	else {
		$obj = [];

		foreach ($attrs as $index => $attr) {
			$obj[ $attr->label ] = Tag::output($attr->value);
		}

		return json_encode($obj, JSON_UNESCAPED_SLASHES);
		return Tag::output("json_encode({$obj})");
	}
});

Tag::register_tag('page', 'controller', function ($type, $attrs, $attr) {
	$name = $attr('use', true);
	$file = Project::get_controller_file($name);
	$page = strtolower($name);

	return Tag::code(Merge::parse('
/* controller re-set start */
require "#{file}";
$_controller = new #{controller}Controller;
\Fabrico\State::load($_controller);
\Fabrico\Core::$controller = & $_controller;
$_controller->initialize();
$_controller->onview();
\Fabrico\Page::include_javascript("Fabrico.controller.std.destination = \"#{page}\"", true);
\Fabrico\Page::include_javascript("Fabrico.controller.DESTINATION = \"#{page}\"", true);
/* controller re-set end */
', [
		'controller' => $name,
		'file' => strtolower($file),
		'page' => $page
	]));
});

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
	const SEPARATOR = ':';
	const MAX_ITERATIONS = 1000;

	/**
	 * tag to method conversion
	 */
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
	 * tracks declared tags and their settings
	 *
	 * @var array
	 */
	public static $tags = array();

	/**
	 * tag namespace <root:namespace:name />
	 *
	 * @var string
	 */
	public $namespace;

	/**
	 * tag name <root:namespace:name />
	 *
	 * @var string
	 */
	public $name;

	/**
	 * full tag name <root:namespace:name />
	 *
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
		$taginfo = array();

		// parse for custom tags
		self::scan_string($html, sprintf(self::TAG_MATCH_METHOD_EMPTY, self::ROOT), self::TAG_SINGLE, $taginfo);
		self::scan_string($html, sprintf(self::TAG_MATCH_METHOD, self::ROOT), self::TAG_SINGLE, $taginfo);
		self::scan_string($html, sprintf(self::TAG_MATCH_SINGLE, self::ROOT), self::TAG_SINGLE, $taginfo);
		self::scan_string($html, sprintf(self::TAG_MATCH_OPEN, self::ROOT), self::TAG_OPEN, $taginfo);
		self::scan_string($html, sprintf(self::TAG_MATCH_CLOSE, self::ROOT), self::TAG_CLOSE, $taginfo);

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
		$mergedlist = array();

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
	
		return array($html, $invalidtags);
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
	private static function scan_string ($html, $match_regex, $tag_type, & $tag_storage) {
		$lastoffset = 0;

		for ($i = 0; $i < self::MAX_ITERATIONS; $i++) {
			preg_match($match_regex, $html, $matches, PREG_OFFSET_CAPTURE, $lastoffset);

			if (count($matches) === self::EXPECTED_MATCHES) {
				$lastoffset = strlen($matches[ 0 ][ 0 ]) + $matches[ 0 ][ 1 ];
				$tag_storage[] = self::build_tag($matches, $tag_type);
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
		$attrs = array();
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
	 * @return object tag replacement information
	 */
	private static function build_tag ($matchinfo, $type) {
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
		
			$tagname = self::ROOT . self::SEPARATOR .
			           $matchinfo[ 1 ][ 0 ] . self::SEPARATOR .
					   $attrstring[ 0 ];
		}


		$tag->tag = $tagname;
		$tag->valid = self::valid($tagname);
		$tag->attrs = self::build_attributes($attrs);
		$tag->match_string = $rawtag;

		$tag->replacement_comment = self::tag2comment(
			$rawtag, $type,
			self::ERROR_UNKNOWN_TAG
		);

		$tag->replacement_string = self::method2code(
			self::tag2method($tagname, $type, $is_method) .
			self::args2string($tag->attrs, $type)
		);

		return $tag;
	}

	/**
	 * parses a string of attributes and returns a list
	 * of attribute information
	 *
	 * @param array of raw attribute string
	 * @return array of clean attribute info
	 */
	private static function build_attributes ($attrs) {
		$list = array();

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
		preg_match('/^"#{.+}"$/', $val, $matches_variable);
		preg_match('/^"%{.+}"$/', $val, $matches_method);

		if (count($matches_variable)) {
			$val = '$' . preg_replace(array('/^"#{/', '/}"$/'), '', $val);
		}

		if (count($matches_method)) {
			$val = preg_replace(array('/^"%{/', '/}"$/'), '', $val);
		}

		switch ($val) {
			case '\'true\'':
			case '"true"':
				return 'true';
			case '\'false\'':
			case '"false"':
				return 'false';
			default:
				return $val;
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
		       "\n<!-- build time: {$time} sec -->" .
		       "\n<!-- invalid tags: {$invalid} -->";
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
	 * returns a php line wrapped in php tags
	 *
	 * @param string method
	 * @return string code
	 */
	private static function method2code ($method) {
		return "<?= {$method} ?>";
	}

	/**
	 * converts an array of arguments into a string 
	 * representing the same array
	 *
	 * @param array of arguments
	 * @param int tag type
	 * @return string valid php array representation
	 */
	private static function args2string ($args, $tagtype) {
		$props = array();

		if ($tagtype === self::TAG_CLOSE) {
			return '()';
		}

		foreach ($args as $arg) {
			$props[] = "'{$arg->label}' => {$arg->value}";
		}

		$props = implode(', ', $props);
		$props = strlen($props) ? " {$props} " : '';
		return "(array({$props}))";
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
			array(self::ROOT . self::SEPARATOR, self::SEPARATOR),
			array('', '\\'), $tagname
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

		return $tagname;
	}
}

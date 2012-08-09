<?php

namespace Fabrico;

class Tag {
	/**
	 * tag types
	 */
	const TAG_SINGLE = 1;
	const TAG_OPEN = 2;
	const TAG_CLOSE = 3;

	/**
	 * tag to method conversion
	 */
	const METHOD_SINGLE_SUFFIX = '::generate';
	const METHOD_OPEN_SUFFIX = '::open';
	const METHOD_CLOSE_SUFFIX = '::close';

	/**
	 * tag matching regular expressions
	 */
	const TAG_MATCH_CLOSE = '/\<\/f:(.+?):(.+?)\>/';
	const TAG_MATCH_OPEN = '';
	const TAG_MATCH_SINGLE = '/\<f:(.+?):(.+?)\s\/\>/';
	// const TAG_SINGLE_ATTR = '/\<f:(.+?):(.+?)\s(.+?)\s\/\>/';

	/**
	 * expected match count
	 */
	const EXPECT_SINGLE = 3;

	/**
	 * tag format
	 */
	const ROOT = 'f';
	const SEPARATOR = ':';
	const MAX_ITERATIONS = 1000;

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
	public function build () {
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
	public static function create ($props) {
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
		// signature trackers
		$start_time = time();
		$invalidtags = 0;

		// clean tag data
		$taginfo = array();

		// parse for custom tags
		self::scan_string($html, self::TAG_MATCH_SINGLE, self::EXPECT_SINGLE, self::TAG_SINGLE, $taginfo);

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

		foreach ($taginfo as $info) {
			$html = str_replace(
				$info->match_string,
				$info->valid ? $info->replacement_string :
				$info->replacement_comment, $html
			);

			if (!$info->valid) {
				$invalidtags++;
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
	private static function scan_string ($html, $match_regex, $match_expects, $tag_type, & $tag_storage) {
		$lastoffset = 0;

		for ($i = 0; $i < self::MAX_ITERATIONS; $i++) {
			preg_match($match_regex, $html, $matches, PREG_OFFSET_CAPTURE, $lastoffset);

			if (count($matches) === $match_expects) {
				$lastoffset = strlen($matches[ 0 ][ 0 ]) + $matches[ 0 ][ 1 ];
				$tag_storage[] = self::build_tag($matches, $tag_type);
			}
			else if (!count($matches)) {
				break;
			}
		}
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
		$tag = new \stdClass;
		$attrs = explode(' ', $matchinfo[ 2 ][ 0 ]);
		$rawtag = $matchinfo[ 0 ][ 0 ];
		$tagname = self::ROOT . self::SEPARATOR .
		           $matchinfo[ 1 ][ 0 ] . self::SEPARATOR .
				   array_shift($attrs);

		$tag->tag = $tagname;
		$tag->valid = self::valid($tagname);
		$tag->attrs = self::build_attributes($attrs);
		$tag->match_string = $rawtag;
		$tag->replacement_comment = self::tag2comment($rawtag);
		$tag->replacement_string = self::method2code(
			self::tag2method($tagname, $type) .
			self::args2string($tag->attrs)
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
			$attrinfo = explode('=', $attr);
			$attribute = new \stdClass;

			if (count($attrinfo) == 1) {
				$attrinfo[ 1 ] = true;
			}

			$attribute->label = $attrinfo[ 0 ];
			$attribute->value = $attrinfo[ 1 ];
			$list[] = $attribute;
		}

		return $list;
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
		return "\n<!-- build date: {$date} -->" .
		       "\n<!-- build time: {$time} sec -->" .
		       "\n<!-- invalid tags: {$invalid} -->";
	}

	/**
	 * wraps an html line in html comment tags
	 *
	 * @param string tag
	 * @return string comment
	 */
	private static function tag2comment ($tag) {
		return "<!-- (build comment) {$tag} -->";
	}

	/**
	 * returns a php line wrapped in php tags
	 *
	 * @param string method
	 * @return string code
	 */
	private static function method2code ($method) {
		return "<? /* build parser */ {$method} ?>";
	}

	/**
	 * converts an array of arguments into a string 
	 * representing the same array
	 *
	 * @param array of arguments
	 * @return string valid php array representation
	 */
	private static function args2string ($args) {
		$props = array();

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
	 * @return string method name
	 */
	private static function tag2method ($tagname, $type) {
		$tagname = str_replace(
			array(self::ROOT . self::SEPARATOR, self::SEPARATOR),
			array('', '\\'), $tagname
		);

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

		return $tagname;
	}
}









class script extends Tag {
	public $namespace = 'resource';
}

class toolbar extends Tag {
	public $namespace = 'std';
}

class style extends Tag {
	public $namespace = 'resource';
}


Tag::create(array(
	'name' => 'sidebar',
	'namespace' => 'std'
));


(new script)->build();
(new toolbar)->build();
(new style)->build();

$html = <<<'HTML'
<body>
	<input type='text' />
	<f:ns:tag attr1='v1' attr2='v2'></f:ns:tag>
	<f:resource:script src="~fabrico.ui.js" core />
	<f:resource:script />
	<f:resource:script src="~fabrico.ui.js" />
	<f:resource:style src=$a />
	<f:ns:tag attr1='v1' attr2='v2' />
	<input type='text' />
	<f:ns:tag />
	<f:ns:tag/>
	<input type='text' />
	<input type='text' />
</body>
HTML;


die(Tag::parse($html));
print_r(Tag::$tags);

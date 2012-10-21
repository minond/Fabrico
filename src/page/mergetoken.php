<?php

/**
 * @package fabrico\page
 */
namespace fabrico\page;

use fabrico\core\util;
use fabrico\page\Token;

/**
 * merge field token
 */
class MergeToken extends Token {
	/**
	 * merge field patterns:
	 *  @{merge_field_name}  -> [controller]->merge_field_name
	 *  @{merge_field:name}  -> [controller]->merge_field->name
	 *  @{merge_field:name!} -> [controller]->merge_field->name()
	 *  #{merge_field_name}  -> $merge_field_name
	 *  #{merge_field:name}  -> $merge_field->name
	 *  #{merge_field:name!} -> $merge_field->name()
	 * @var string
	 */
	public static $pattern = '/([\\#|@])\{(.+)?\}/';

	/**
	 * special merge field characters and replacements
	 * @var array
	 */
	public static $special = [
		'prop' => [
			'find' => ':',
			'replace' => '->'
		],
		'func' => [
			'find' => '!',
			'replace' => '()'
		]
	];

	/**
	 * merge field type
	 * @var array
	 */
	public static $types = [
		'@' => '$',
		'#' => '$Core->controller->'
	];

	/**
	 * include php tags in replacement
	 * @var boolean
	 */
	public $in_tag = true;

	/**
	 * @var string
	 */
	public static $tag = '<?php echo %type%merge; ?>';

	/**
	 * @var string
	 */
	public static $str = '{%type%merge}';

	/**
	 * @see Token::parse
	 */
	public function parse (array $raw) {
		list($find, $replace) = $this->getspecial();
		$type = self::$types[ $raw[ 1 ][ 0 ] ];
		$merge = str_replace($find, $replace, $raw[ 2 ][ 0 ]);
		$holder = $this->in_tag ? self::$tag : self::$str;
		$this->replacement = str_replace(['%type', '%merge'], [$type, $merge], $holder);
	}

	/**
	 * special character find and replacement arrays
	 * @return array[array]
	 */
	private function getspecial () {
		static $find = [];
		static $replace = [];

		if (!$find || !$replace) {
			foreach (self::$special as $name => $info) {
				$find[] = $info['find'];
				$replace[] = $info['replace'];
			}
		}

		return [ $find, $replace ];
	}
}

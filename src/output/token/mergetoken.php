<?php

/**
 * @package fabrico\output
 */
namespace fabrico\output;

/**
 * merge field token
 */
class MergeToken extends Token {
	/**
	 * holder types
	 */
	const IN_PHP = 'php';
	const IN_STR = 'str';

	/**
	 * merge field patterns:
	 *  #{merge_field_name}  = <Controller>->merge_field_name
	 *  #{merge_field:name}  = <Controller>->merge_field->name
	 *  #{merge_field:name!} = <Controller>->merge_field->name()
	 *  @{merge_field_name}  = $merge_field_name
	 *  @{merge_field:name}  = $merge_field->name
	 *  @{merge_field:name!} = $merge_field->name()
	 * @var string
	 */
	public static $pattern = '/([\\#|@]){(.+?)}/';

	/**
	 * special merge field characters and replacements
	 * @var array
	 */
	public static $special = [
		'underscore' => [
			'find' => ' ',
			'replace' => '_'
		],
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
		'#' => '$controller->'
	];

	/**
	 * holder name
	 * @var string
	 */
	public static $holder = self::IN_PHP;

	/**
	 * possible holders
	 * @var string
	 */
	public static $holders = [
		self::IN_PHP => '<?php echo %merge; ?>',
		self::IN_STR => '{%merge}'
	];

	/**
	 * @see Token::parse
	 */
	public function parse (array $raw) {
		list($find, $replace) = self::getspecial();
		$type = self::$types[ $raw[ 1 ][ 0 ] ];
		$merge = str_replace($find, $replace, $raw[ 2 ][ 0 ]);
		$holder = self::$holders[ self::$holder ];
		$this->replacement = self::mergeholder($type.$merge, self::$holder);
	}

	/**
	 * merge merge field into holder
	 * @param string $merge
	 * @param string $type
	 * @return string
	 */
	public static function mergeholder ($merge, $type) {
		return str_replace('%merge', $merge, self::$holders[ $type ]);
	}

	/**
	 * special character find and replacement arrays
	 * @return array[array]
	 */
	public static function getspecial () {
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

	/**
	 * simple merge field parser
	 * @param string $tmpl
	 * @param mixed $data
	 * @return string
	 */
	public static function merge ($tmpl, $data) {
		foreach ($data as $field => $value) {
			if (is_scalar($value)) {
				$tmpl = str_replace("#{{$field}}", $value, $tmpl);
			}
		}

		return $tmpl;
	}

	/**
	 * clean up a merge variable
	 * @param string $var
	 * @return string
	 */
	public static function clean_var ($var) {
		$type = $var[0];

		return array_key_exists($type, self::$types) ?
			self::$types[ $type ] . substr($var, 1) : $var;
	}
}

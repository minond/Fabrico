<?php

/**
 * @package fabrico\page
 */
namespace fabrico\page;

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
	 *  @{merge_field_name}  -> [controller]->merge_field_name
	 *  @{merge_field:name}  -> [controller]->merge_field->name
	 *  @{merge_field:name!} -> [controller]->merge_field->name()
	 *  #{merge_field_name}  -> $merge_field_name
	 *  #{merge_field:name}  -> $merge_field->name
	 *  #{merge_field:name!} -> $merge_field->name()
	 * @var string
	 */
	public static $pattern = '/([\\#|@]){(.+?)}/';

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
		'#' => '$core->controller->'
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
		self::IN_PHP => '<?php echo %type%merge; ?>',
		self::IN_STR => '{%type%merge}'
	];

	/**
	 * @see Token::parse
	 */
	public function parse (array $raw) {
		list($find, $replace) = $this->getspecial();
		$type = self::$types[ $raw[ 1 ][ 0 ] ];
		$merge = str_replace($find, $replace, $raw[ 2 ][ 0 ]);
		$holder = self::$holders[ self::$holder ];
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
}

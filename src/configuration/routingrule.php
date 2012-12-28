<?php

/**
 * @package fabrico\core
 */
namespace fabrico\configuration;

/**
 * represents a routing item
 */
class RoutingRule implements ConfigurationReader {
	/**
	 * merge field format
	 */
	const REGEXP = '/\\#{(.+?)}/';

	/**
	 * routing rule's label
	 * @var string
	 */
	private $label;

	/**
	 * url format
	 * @var string
	 */
	private $raw_url;

	/**
	 * "compiled" raw url
	 * @var string
	 */
	private $cooked_url;

	/**
	 * url pointing to view file
	 * @var string
	 */
	private $view_url;

	/**
	 * parameter labels
	 * @var array
	 */
	private $labels = [];

	/**
	 * @param string $label
	 * @param string $url
	 */
	public function __construct ($label = '', $url = '') {
		$this->label = $label;
		$this->raw_url = $url;

		if ($label && $url) {
			$this->compile_own_url();
		}
	}

	/**
	 * parses its own url into a regular expression
	 * it can use to match the requested url
	 */
	private function compile_own_url () {
		preg_match_all(self::REGEXP, $this->raw_url, $matches);
		$map = $this->build_matches($matches);
		list($this->cooked_url, $this->view_url, $this->labels) = $this->apply_merge_map($map, $this->raw_url);
	}

	/**
	 * creates the cooked url
	 * @param stdClass[] $map
	 * @param string $raw_url
	 * @return array
	 */
	private function apply_merge_map (array & $map, $raw_url) {
		$view_url = $raw_url;
		$raw_url = str_replace('/', '\\/', $raw_url);
		$labels = [];

		foreach ($map as & $param) {
			$raw_url = str_replace($param->raw, $param->regex, $raw_url);
			$view_url = str_replace('/' . $param->raw, '', $view_url);
			$labels[] = $param->label;
			unset($param);
		}

		return ["/$raw_url/", $view_url, $labels];
	}

	/**
	 * reformats matches into a format we can use to merge
	 * into the raw url and use later for parameter storage
	 * @return array $matches
	 * @return stdClass[]
	 */
	private function build_matches (array & $matches) {
		$map = [];

		if (count($matches)) {
			$info = $matches[ 1 ];
			$fields = $matches[ 0 ];

			foreach ($fields as $index => $raw) {
				$parts = explode(':', $info[ $index ]);
				$param = new \stdClass;
				$param->raw = $raw;
				$param->clean = $info[ $index ];
				$param->label = $parts[ 0 ];
				$param->regex = isset($parts[ 1 ]) ? "({$parts[ 1 ]}?)" : '(.+?)';
				$map[] = $param;
				unset($param);
			}
		}

		return $map;
	}

	/**
	 * label getter
	 * @return string
	 */
	public function get_label () {
		return $this->label;
	}

	/**
	 * raw url getter
	 * @return string
	 */
	public function get_raw_url () {
		return $this->raw_url;
	}

	/**
	 * cooked url getter
	 * @return string
	 */
	public function get_cooked_url () {
		return $this->cooked_url;
	}

	/**
	 * @param string $url
	 * @param array $storage
	 * @return boolean
	 */
	public function try_reading ($url, array & $storage = null) {
		$match = false;
		preg_match($this->cooked_url, $url, $matches);

		if (count($matches)) {
			$match = true;

			if (is_array($storage)) {
				array_shift($matches);

				for ($i = 0, $len = count($matches); $i < $len; $i++) {
					$storage[ $this->labels[ $i ] ] = $matches[ $i ];
				}

				$storage[ '_file' ] = $this->view_url;
			}
		}

		return $match;
	}

	/**
	 * @return RoutingRule[]
	 */
	public function load ($json) {
		$routes = [];

		foreach ($json as $name => $route) {
			$routes[] = new self($name, $route);
		}

		return $routes;
	}
}

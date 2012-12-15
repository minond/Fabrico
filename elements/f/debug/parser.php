<?php

/**
 * @package fabrico\output\f
 */
namespace fabrico\output\f\debug;

use fabrico\output\Tag;
use fabrico\core\Mediator;

/**
 * while, for, foreach loops
 */
class Parser extends Tag {
	use Mediator;

	public function assemble() {
		list($raw, $parsed) = $this->core->response->outputcontent->get_contents();
		$raw = htmlspecialchars($raw);
		$parsed = htmlspecialchars($parsed);

		return <<<HTML
<textarea style="height: 1000px; width: 500px">{$raw}</textarea>
<textarea style="height: 1000px; width: 500px">{$parsed}</textarea>
HTML;
	}
}

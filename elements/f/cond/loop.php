<?php

/**
 * @package fabrico\output\f
 */
namespace fabrico\output\f\cond;

use fabrico\output\Tag;
use fabrico\output\TagToken;
use fabrico\output\MergeToken;
use fabrico\core\Mediator;

/**
 * while, for, foreach loops
 */
class Loop extends Tag {
	use Mediator;

	/**
	 * data
	 * @var string
	 */
	public $over;

	/**
	 * variable
	 * @var string
	 */
	public $as;

	public function assemble () {
		$this->over = MergeToken::clean_var($this->over);

		switch ($this->get_type()) {
			case TagToken::OPEN:
				return "<?php foreach ({$this->over} as \${$this->as}): ?>";
				break;

			case TagToken::CLOSE:
				return "<?php endforeach ?>";
				break;
		}
	}
}

<?php

/**
 * @package fabrico\output\f
 */
namespace fabrico\output\f\data;

use fabrico\output\Tag;
use fabrico\output\TagToken;
use fabrico\output\PropertyToken;
use fabrico\output\MergeToken;

/**
 * while, for, foreach loops
 */
class Cond extends Tag {
	/**
	 * data
	 * @var string
	 */
	public $if;

	public function assemble () {
		// TODO: create another parser function for this type of use
		$this->if = PropertyToken::parse_value($this->if);
		$this->if = substr($this->if, 1, -1);

		switch ($this->get_type()) {
			case TagToken::OPEN:
				return "<?php if ({$this->if}): ?>";
				break;

			case TagToken::CLOSE:
				return "<?php endif ?>";
				break;
		}
	}
}

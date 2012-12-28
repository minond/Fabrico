<?php

/**
 * @package fabrico\output\f
 */
namespace fabrico\output\f\cond;

use fabrico\output\Tag;
use fabrico\output\TagToken;
use fabrico\output\PropertyToken;

/**
 * if, else if, else statements
 */
class Check extends Tag {
	/**
	 * data
	 * @var string
	 */
	public $if;

	/**
	 * @see Tag::assemble
	 */
	public function assemble () {
		// TODO: create another parser function for this type of use
		$this->if = PropertyToken::parse_value($this->if);
		$this->if = substr($this->if, 1, -1);

		switch ($this->get_type()) {
			case TagToken::OPEN:
				return "<?php if ({$this->if}): ?>";
				break;

			case TagToken::SINGLE:
				return $this->if ? "<?php elseif ({$this->if}): ?>" : "<?php else: ?>";
				break;

			case TagToken::CLOSE:
				return "<?php endif ?>";
				break;
		}
	}
}
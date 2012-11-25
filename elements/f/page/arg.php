<?php

/**
 * @package fabrico\output\f
 */
namespace fabrico\output\f\page;

/**
 * tag argument
 * <code>
 * <data:table data="{table_data!}">
 *   <page:arg name="controller-columns" value="{table_columns!}" />
 * </data:table>
 * </code>
 */
class Arg extends \fabrico\output\Tag {
	/**
	 * @see Tag::$arg
	 */
	protected static $arg = true;

	/**
	 * argument name
	 * @var string
	 */
	public $name;

	/**
	 * argument value
	 * @var mixed
	 */
	public $value;
}

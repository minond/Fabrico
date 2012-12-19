<?php

/**
 * @package fabrico\output\f
 */
namespace fabrico\output\f\modal;

/**
 * modal popup
 */
class Popup extends \fabrico\output\Tag {
	protected static $tag = 'div';

	/**
	 * @see Tag::initialize
	 */
	protected function initialize () {
		$header_html = '';
		$footer_html = '';
		$body_html = '';

		$header = $this->find_arg('f\modal\Header');
		$footer = $this->find_arg('f\modal\Footer');
		$body = $this->find_arg('f\modal\Body');

		if ($header) {
			$header_html = $this->html('div', [
				'class' => 'modal_header'
			], $header->get_content());
		}

		if ($footer) {
			$footer_html = $this->html('div', [
				'class' => 'modal_footer'
			], $footer->get_content());
		}

		if ($body) {
			$height = $body->height;
			$width = $body->width;
			$body = $body->get_content();
		}
		else {
			$height = 'auto';
			$width = 'auto';
			$body = $this->get_content();
		}

		$body_html = $this->html('div', [
			'class' => 'modal_body',
			'style' => "height: {$height}; width: {$width}"
		], $body);

		$content = $this->html('div', [
			'class' => 'modal_content'
		], $header_html . $body_html . $footer_html);

		$inner = $this->html('div', [
			'class' => 'modal_inner'
		], $content);

		$outer = $this->html('div', [
			'class' => 'modal_outer'
		], $inner);

		$background = $this->html('div', [
			'class' => 'modal_background'
		]);

		$this->set_content($background . $outer);
		$this->add_class('modal');
	}
}

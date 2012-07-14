<?php

class PostsController extends MainController {
	public function __construct () {
		parent::__construct();
		$this->register('add_post', 'delete_post');
	}

	public function add_post () {
		$post_id = Post::add(
			Post::create('subject', 'body')
		);

		Fabrico::handle_success(array(
			'post_id' => $post_id
		));
	}

	public function delete_post ($post_id) {
		Post::del($post_id);
		Fabrico::handle_success();
	}
}

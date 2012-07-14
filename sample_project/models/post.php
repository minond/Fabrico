<?php

class Post extends FabricoModel {
	public static $has_many = Comment;
}

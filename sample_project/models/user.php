<?php

class User extends FabricoModel {
	protected static $just_checking = array(
		'email',
		'password'
	);

	protected static $send_back = array(
		'first_name',
		'last_name',
		'email',
		'permission_groups'
	);
}

<?php

function _filelog_ () {
	call_user_func_array(
		array('util', 'log'),
		func_get_args()
	);

	return 1;
}

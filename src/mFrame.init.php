<?php

require_once 'mFrame.constants.php';
require_once 'mFrame.util.php';
require_once 'mFrame.main.php';
require_once 'mFrame.html.php';
require_once 'mFrame.connection.php';

!mFrame::init() ?
	mFrame::redirect() :
	require mFrame::get_file_requested();

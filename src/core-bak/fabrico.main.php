<?php

require_once 'fabrico.core.php';

Fabrico\Core::request_pre_check();
Fabrico\Core::load_core_files();
Fabrico\Core::load_core_configuration();
Fabrico\Core::load_project_configuration();
Fabrico\Core::load_core_setup($_REQUEST);
Fabrico\Core::start_active_record();
Fabrico\Core::start_session();
Fabrico\Core::handle_request();

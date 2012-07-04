<?php

/**
 * @name directory
 * @global
 *
 * standard project directory structure
 */
$directory = new stdClass;
$directory->logs = '_logs/';
$directory->actions = '_actions/';
$directory->resources = '_resources/';
$directory->templates = '_templates/';
$directory->controllers = '_controllers/';

/**
 * @name service
 * @global
 *
 * service loader/manager scripts
 */
$service = new stdClass;
$service->database = '../loader/db.php';

/**
 * @program
 * @global
 *
 * current program -> framework structure
 */
$program = new stdClass;
$program->controller = null;

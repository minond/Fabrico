<?php

/**
 * @package fabrico\status
 */
namespace fabrico\status;

/**
 * access statuses
 */
class ControllerStatus extends StatusManager {
	const UNKNOWN_C = 'unknown_controller';
	const PRIVATE_C = 'private_controller';
	const UNKNOWN_M = 'unknown_method';
	const PRIVATE_M = 'private_method';
}

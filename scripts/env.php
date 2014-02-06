<?php

// default configuration
if (getenv('APP_ENV') === false) {
    putenv('NICE_ERRORS=1');
    putenv('APP_ENV=development');
}


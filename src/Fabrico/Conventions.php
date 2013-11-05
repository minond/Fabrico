<?php

namespace Fabrico;

abstract class Conventions
{
    const INIT_CONFIG_FILE = 'init/config.php';

    const DIR_CONFIG = 'config';
}

abstract class Events
{
    const VIEW_FIND_FILE = 0;
    const VIEW_RENDER_FILE = 1;
}


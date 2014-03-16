<?php

namespace Fabrico\Initializer\StdInitializers;

use Fabrico\Initializer\JitInitializer;

/**
 * configuration object modifications
 * "@" represents imported project flag
 */
class Config extends JitInitializer
{
    public function initialize()
    {
        $conf =& $this->conf;

        // @ProjectBase, @Crud: lib/Crud/configuration
        // from configuration
        $conf->registerPathParser('/^@(\w+)/', function ($match, $path) use (& $conf) {
            return str_replace(
                $match[0],
                sprintf('../lib/%s/' . $conf->getDirectory(), $match[1]),
                $path
            );
        });
    }
}

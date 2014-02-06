<?php

namespace Fabrico\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Efficio\Configurare\Configuration;

/**
 * allows application configuration to be passed to command
 */
abstract class Command extends BaseCommand
{
    /**
     * @var Configuration
     */
    protected $conf;

    /**
     * @param Configuration $conf
     */
    public function setConfiguration(Configuration $conf)
    {
        $this->conf = $conf;
    }
}

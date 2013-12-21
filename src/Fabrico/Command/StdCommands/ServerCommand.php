<?php

namespace Fabrico\Command\StdCommands;

use Fabrico\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * start php's built in server
 */
class ServerCommand extends Command
{
    protected function configure()
    {
        $this->setName('server');
        $this->setDescription('Start php\'s built in server');
        $this->addOption('addr', 'ar', InputOption::VALUE_OPTIONAL, 'Server address', '0.0.0.0');
        $this->addOption('port', 'p', InputOption::VALUE_OPTIONAL, 'Server port', '8080');
        $this->addOption('router', 'r', InputOption::VALUE_OPTIONAL, 'Router script',
            sprintf('%s/../scripts/router.php', __dir__));
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $addr = $input->getOption('addr');
        $port = $input->getOption('port');
        $router = $input->getOption('router');
        `php -S $addr:$port $router`;
    }
}


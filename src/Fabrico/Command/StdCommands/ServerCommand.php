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
        $this->addOption(
            'router',
            'r',
            InputOption::VALUE_OPTIONAL,
            'Router script',
            sprintf('%s/../../../../scripts/server.php', __dir__)
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = getenv('APP_ENV');
        $addr = $input->getOption('addr');
        $port = $input->getOption('port');
        $router = $input->getOption('router');
        $relfile = str_replace(__dir__, '', $router);

        $output->writeln('=> starting php built in server');
        $output->writeln("=> environment is <info>$env</info>");
        $output->writeln("=> server bound to <info>$addr</info>:<info>$port</info>");
        $output->writeln("=> router file <info>$relfile</info>");

        // to get stdout
        passthru("php -S $addr:$port $router");
    }
}

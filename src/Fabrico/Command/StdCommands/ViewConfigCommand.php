<?php

namespace Fabrico\Command\StdCommands;

use Fabrico\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Yaml\Yaml;

/**
 * view configuration items
 */
class ViewConfigCommand extends Command
{
    protected function configure()
    {
        $this->setName('config:view');
        $this->setDescription('View application configuration');

        $this->addArgument(
            'get',
            InputArgument::OPTIONAL,
            'Retrive a configuration value'
        );

        $this->addOption(
            'php', 'p',
            InputOption::VALUE_NONE,
            'Output as php'
        );

        $this->addOption(
            'json', 'j',
            InputOption::VALUE_NONE,
            'Output as json'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $get = $input->getArgument('get');

        if ($get) {
            $val = $this->conf->get($get);

            if ($input->getOption('php')) {
                $val = var_export($val, true);
            } else if ($input->getOption('json')) {
                $val = json_encode($val, JSON_PRETTY_PRINT);
            } else {
                $val = Yaml::dump($val, 100, 2);
                preg_match("/\n/", $val, $matches);
                $output->write("Value for <info>{$get}</info>: ");

                if (count($matches)) {
                    $output->writeln('');
                    $output->writeln('');
                }
            }

            $output->writeln(trim($val));
        }
    }
}


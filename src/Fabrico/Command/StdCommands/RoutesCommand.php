<?php

namespace Fabrico\Command\StdCommands;

use Fabrico\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableHelper;

/**
 * list application routes
 */
class RoutesCommand extends Command
{
    protected function configure()
    {
        $this->setName('routes');
        $this->setDescription('View application routes');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getApplication();
        $table = $app->getHelperSet()->get('table');

        foreach ($this->conf->get('routes') as $url => $params) {
            $method = '*';

            if (isset($params['method'])) {
                $method = $params['method'];
                unset($params['method']);
            }

            $table->addRow([ $method, $url, json_encode($params) ]);
        }

        $table->setHeaders([ 'method', 'url', 'params' ]);
        $table->render($output);
    }
}


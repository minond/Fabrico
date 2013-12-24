<?php

namespace Fabrico\Command\StdCommands;

use Fabrico\Command\Command;
use Efficio\Http\RuleBook;
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
        $table->setLayout(TableHelper::LAYOUT_BORDERLESS);

        $rules = new RuleBook;
        $rules->load($this->conf->get('routes'), true);

        // foreach ($this->conf->get('routes') as $url => $params) {
        foreach ($rules->all() as $rule) {
            $template = $rule->getTemplate();
            $params = $rule->getInformation();

            $method = '*';
            $resource = '';
            $generator = '';

            if (isset($params['method'])) {
                $method = $params['method'];
                unset($params['method']);
            }

            if (isset($params['_resource'])) {
                $resource = $params['_resource'];
                unset($params['_resource']);
            }

            if (isset($params['_generator'])) {
                $generator = $params['_generator'];
                unset($params['_generator']);
            }

            // json string clean up
            $info = str_replace(
                [ '":"', '","', '{"', '"}' ],
                [ ': ', ', ', '', '' ],
                json_encode($params, JSON_UNESCAPED_SLASHES)
            );

            $table->addRow([ $generator, strtolower($method), $template, $info ]);
        }

        $table->render($output);
    }
}


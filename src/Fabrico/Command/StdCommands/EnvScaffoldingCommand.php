<?php

namespace Fabrico\Command\StdCommands;

use Fabrico\Information;
use Fabrico\Command\Command;
use Efficio\Utilitatis\Merger;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * enviroment configuration scaffold generator
 */
class EnvScaffoldingCommand extends Command
{
    protected function configure()
    {
        $this->setName('generate:env');
        $this->setDescription('Generated template enviroment configuration');

        $this->addOption(
            'apache', 'a',
            InputOption::VALUE_NONE,
            'Generate Apache configuration'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $merger = new Merger;
        $apache = $input->getOption('apache');

        if ($apache) {
            $output->write($config = $merger->merge(
                $this->getTemplate('apache.conf'),
                [
                    'directory' => basename(getcwd()),
                    'version' => Information::VERSION,
                    'date' => date('Y-m-d H:i:s'),
                ],
                false
            ));
        }
    }

    /**
     * return a template file's content
     * @param string $file
     * @return string
     */
    protected function getTemplate($file)
    {
        return file_get_contents(sprintf('%s/templates/%s', __dir__, $file));
    }
}


<?php

namespace Fabrico\Command\StdCommands;

use Fabrico\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * initializes a new application
 * - renames default namespaced directory in the app and tests dirs
 * - replaces all 'MyApplication' namespaces
 * - updates build.xml file's project name
 * - updates app.yml configuration file
 */
class InitializeNewApplication extends Command
{
    /**
     * default namespace used in configation, php files, and directories
     */
    const DEFAULT_NAMESPACE = 'MyApplication';

    protected function configure()
    {
        $this->setName('init:newapp');
        $this->setDescription('Setup an new application created using Fabricor for Fabrico');
        $this->addArgument('name', InputArgument::REQUIRED, 'Application name');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ns = static::DEFAULT_NAMESPACE;
        $name = $input->getArgument('name');
        $files = [];

        $files = array_merge($files, [ 'build.xml', 'composer.json' ]);
        $files = array_merge($files, glob('config/*'));
        $files = array_merge($files, glob('app/*/*'));
        $files = array_merge($files, glob('tests/*/*'));

        foreach ($files as $file) {
            file_put_contents($file, str_replace($ns, $name, file_get_contents($file)));
        }

        foreach ([ 'app', 'tests' ] as $dir) {
            rename("$dir/$ns", "$dir/$name");
        }
    }
}

<?php

namespace Fabrico\Command\StdCommands;

use Fabrico\Information;
use Fabrico\Command\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * generates model classes and views
 */
class ModelScaffoldingCommand extends GeneratorCommand
{
    /**
     * defaults
     */
    const DEFAULT_TYPE = 'string';

    /**
     * field types
     */
    const TYPE_ARRAY = 'array';

    protected function configure()
    {
        $this->setName('generate:model');
        $this->setDescription('Generates model classes and views');

        $this->addArgument(
            'name',
            InputArgument::REQUIRED,
            'Model name'
        );

        $this->addArgument(
            'fields',
            InputArgument::IS_ARRAY,
            'Model fields'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $single = $input->getArgument('name');
        $fields = $input->getArgument('fields');

        $ns = $this->conf->get('app:namespace');
        $clazz = $this->word->classicalCase($name);
        $plural = $this->word->pluralize($name);
        $clazzes = $this->word->pluralize($clazz);

        // controller class file
        $output->writeln('Generating controller');
        $this->createFile(
            sprintf('src/%s/Controller/%s.php', $ns, $clazzes),
            $this->generateController($ns, $clazzes, $clazz, $single, $plural),
            $output
        );

        // model class file
        $output->writeln('');
        $output->writeln('Generating model');
        $this->createFile(
            sprintf('src/%s/Model/%s.php', $ns, $clazz),
            $this->generateModel($ns, $clazz, $fields),
            $output
        );

        // model class file
        $output->writeln('');
        $output->writeln('Generating views');
        $this->createDirectory(
            sprintf('views/%s', $plural),
            $output
        );

        // index page
        $this->createFile(
            sprintf('views/%s/index.html.twig', $plural),
            $this->generateView('index', $clazzes, $name, $plural),
            $output
        );

        // index json page
        $this->createFile(
            sprintf('views/%s/index.json.php', $plural),
            $this->generateView('index.json', $clazzes, $name, $plural),
            $output
        );

        // add page
        $this->createFile(
            sprintf('views/%s/add.html.twig', $plural),
            $this->generateView('add', $clazzes, $name, $plural),
            $output
        );

        // edit page
        $this->createFile(
            sprintf('views/%s/edit.html.twig', $plural),
            $this->generateView('edit', $clazzes, $name, $plural),
            $output
        );
    }

    /**
     * @param string $view, ie: index, edit, etc.
     * @param string $title, ie: Posts
     * @param string $single, ie: post
     * @param string $plural, ie: posts
     */
    protected function generateView($view, $title, $single, $plural)
    {
        return $this->merger->merge($this->getTemplate("views/$view.view"), [
            'title' => $title,
            'single' => $single,
            'plural' => $plural,
        ]);
    }

    /**
     * @param string $namespace, Project
     * @param string $clazzes, Posts
     * @param string $clazz, Post
     * @param string $single, post
     * @param string $plural, posts
     */
    protected function generateController($namespace, $clazzes, $clazz, $single, $plural)
    {
        return $this->merger->merge(
            $this->getTemplate('classes/controller.class'), [
                'package' => $namespace,
                'clazzes' => $clazzes,
                'clazz' => $clazz,
                'singular' => $single,
                'plural' => $plural,
            ]
        );
    }

    /**
     * @param string $name
     * @param array $fields
     */
    protected function generateModel($namespace, $name, $fields)
    {
        $properties = array_map(function($field) {
            $info = explode(':', $field);
            $field = array_shift($info);
            $type = count($info) ? array_shift($info) : static::DEFAULT_TYPE;

            switch ($type) {
                case static::TYPE_ARRAY:
                    $value = '[]';
                    break;

                default:
                    $value = 'null';
                    break;
            }

            return $this->merger->merge(
                $this->getTemplate('classes/protected.property'), [
                    'property' => $field,
                    'value' => $value,
                    'var' => $type,
                ]
            );
        }, $fields);

        return $this->merger->merge(
            $this->getTemplate('classes/model.class'), [
                'package' => $namespace,
                'clazz' => $name,
                'properties' => trim(implode(PHP_EOL, $properties)),
            ]
        );
    }
}


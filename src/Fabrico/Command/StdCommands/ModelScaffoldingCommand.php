<?php

namespace Fabrico\Command\StdCommands;

use Fabrico\Command\GeneratorCommand;
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
        $clazzes = ucwords($clazzes);

        // controller class file
        $this->createFile(
            sprintf('app/controllers/%s.php', $clazzes),
            $this->generateController($ns, $clazzes, $clazz, $single, $plural),
            $output
        );

        // model class file
        $this->createFile(
            sprintf('app/models/%s.php', $clazz),
            $this->generateModel($ns, $clazz, $fields),
            $output
        );

        // views
        $this->createDirectory(sprintf('app/views/%s', $plural), $output);

        // index page
        $this->createFile(
            sprintf('app/views/%s/index.html.twig', $plural),
            $this->generateView('index', $clazzes, $name, $plural),
            $output
        );

        // index json page
        $this->createFile(
            sprintf('app/views/%s/index.json.php', $plural),
            $this->generateView('index.json', $clazzes, $name, $plural),
            $output
        );

        // add page
        $this->createFile(
            sprintf('app/views/%s/add.html.twig', $plural),
            $this->generateView('add', $clazzes, $name, $plural),
            $output
        );

        // edit page
        $this->createFile(
            sprintf('app/views/%s/edit.html.twig', $plural),
            $this->generateView('edit', $clazzes, $name, $plural),
            $output
        );

        // form template for add and edit pages
        $this->createFile(
            sprintf('app/views/%s/_form.html.twig', $plural),
            $this->generateView('_form', $clazzes, $name, $plural, [
                'fields' => $this->generateFormFields($name, $fields),
            ]),
            $output
        );
    }

    /**
     * @param string $view, ie: index, edit, etc.
     * @param string $title, ie: Posts
     * @param string $single, ie: post
     * @param string $plural, ie: posts
     * @param array $extra
     */
    protected function generateView($view, $title, $single, $plural, array $extra = [])
    {
        return $this->merger->merge($this->getTemplate("views/$view.view"), array_merge([
            'title' => $title,
            'single' => $single,
            'plural' => $plural,
        ], $extra));
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
            $this->getTemplate('classes/controller.class'),
            [
                'package' => $namespace,
                'clazzes' => $clazzes,
                'clazz' => $clazz,
                'singular' => $single,
                'plural' => $plural,
            ]
        );
    }

    /**
     * @param array $single
     * @param array $fields
     * @return string
     */
    protected function generateFormFields($single, array $fields)
    {
        $html = [];
        $labels = [];
        $inputs = [];

        // model fields
        foreach ($fields as $field) {
            $info = explode(':', $field);
            $field = array_shift($info);
            $type = count($info) ? array_shift($info) : static::DEFAULT_TYPE;

            $info = [
                'type' => 'text',
                'single' => $single,
                'name' => $field,
                'label' => ucwords($this->word->humanCase($field)),
            ];

            // field html
            switch ($type) {
                default:
                    $fhtml = $this->getTemplate('fields/text.field');
                    break;
            }

            // label
            $lhtml = $this->getTemplate('fields/label.field');
            $lhtml = $this->merger->merge($lhtml, $info);
            $fhtml = $this->merger->merge($fhtml, $info);

            $labels[] = trim($lhtml);
            $inputs[] = trim($fhtml);
        }

        $html[] = '<table>';
        foreach ($fields as $index => $field) {
            $html[] = "\n    <tr>";
            $html[] = "\n        <td>";
            $html[] = "\n            {$labels[ $index ]}";
            $html[] = "\n        </td>";
            $html[] = "\n        <td>";
            $html[] = "\n            {$inputs[ $index ]}";
            $html[] = "\n        </td>";
            $html[] = "\n    </tr>";
        }
        $html[] = "\n</table>\n";

        // id field
        $html[] = $this->merger->merge(
            $this->getTemplate('fields/text.field'),
            array_merge($info, [
                'type' => 'hidden',
                'name' => 'id',
            ])
        );

        return implode('', $html);
    }

    /**
     * @param string $namespace
     * @param string $name
     * @param array $fields
     */
    protected function generateModel($namespace, $name, array $fields)
    {
        $properties = array_map(function ($field) {
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
                $this->getTemplate('classes/protected.property'),
                [
                    'property' => $field,
                    'value' => $value,
                    'var' => $type,
                ]
            );
        }, $fields);

        return $this->merger->merge(
            $this->getTemplate('classes/model.class'),
            [
                'package' => $namespace,
                'clazz' => $name,
                'properties' => trim(implode(PHP_EOL, $properties)),
            ]
        );
    }
}

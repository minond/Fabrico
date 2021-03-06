<?php

namespace Fabrico\Initializer\StdInitializers;

use Fabrico\Initializer\JitInitializer;
use Fabrico\Controller\CrudController as Crud;
use Efficio\Utilitatis\Word;
use Twig_Environment;
use Twig_SimpleFunction;

/**
 * adds helper twig functions
 */
class Twig extends JitInitializer
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var Word
     */
    protected $word;

    /**
     * @param Word $word
     */
    public function __construct(Word $word = null)
    {
        $this->word = $word ?: new Word;
    }

    /**
     * creates path/url generators for each model
     */
    public function initialize()
    {
        foreach (glob('app/models/*.php') as $model) {
            preg_match('/app\/models\/(.+).php/', $model, $model);
            $model = strtolower(array_pop($model));
            $models = $this->word->pluralize($model);

            // add_task_path()
            $this->twig->addFunction(
                $this->urlGenerator(Crud::ADD_ACTION, $model, $models)
            );

            // edit_task_path(task)
            $this->twig->addFunction(
                $this->urlGenerator(Crud::EDIT_ACTION, $model, $models, true)
            );

            // create_task_path
            $this->twig->addFunction(
                $this->urlGenerator(Crud::CREATE_ACTION, $model, $models)
            );

            // update_task_path(task)
            $this->twig->addFunction(
                $this->urlGenerator(Crud::UPDATE_ACTION, $model, $models, true)
            );

            // delete_task_path(task)
            $this->twig->addFunction(
                $this->urlGenerator(Crud::DELETE_ACTION, $model, $models, true)
            );

            // tasks_path
            $this->twig->addFunction(new Twig_SimpleFunction(
                sprintf('%s_path', $models),
                function () use ($models) {
                    return sprintf('/%s', $models);
                }
            ));
        }
    }

    /**
     * generate functions that generates a url like: /tasks/edit/3
     * @param string $action
     * @param string $model
     * @param string $models
     * @param boolean $id, default: false, include id in url flag
     * @return Twig_SimpleFunction
     */
    protected function urlGenerator($action, $model, $models, $id = false)
    {
        return new Twig_SimpleFunction(
            sprintf('%s_%s_path', $action, $model),
            $id ? function ($obj) use ($action, $models) {
                return sprintf('/%s/%s/%s', $models, $action, $obj->id);
            } : function () use ($action, $models) {
                return sprintf('/%s/%s', $models, $action);
            }
        );
    }
}

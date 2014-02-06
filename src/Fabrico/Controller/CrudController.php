<?php

namespace Fabrico\Controller;

abstract class CrudController extends BaseController
{
    /**
     * add model page
     */
    const ADD_ACTION = 'add';

    /**
     * view model page or request
     */
    const EDIT_ACTION = 'edit';

    /**
     * save model request
     */
    const CREATE_ACTION = 'create';

    /**
     * update model request
     */
    const UPDATE_ACTION = 'update';

    /**
     * delete model request
     */
    const DELETE_ACTION = 'delete';

    /**
     * lists entities
     */
    public function index()
    {
        $model = static::$model;
        $this->resource($model::all());
    }

    /**
     * add page for creating a new entity
     */
    public function add()
    {
        $model = static::$model;
        $this->resource(new $model);
    }

    /**
     * edit page for modifying an existing entity
     */
    public function edit()
    {
        $model = static::$model;
        $this->resource($model::find($this->req->param->id));
    }

    /**
     * create action for adding a new entity
     */
    public function create()
    {
        $model = static::$model;
        $entity = $model::create($this->req->param->{ static::$param });
        $entity->save();
        $this->redirectTo(static::$index);
    }

    /**
     * update action for modifying an existing entity
     */
    public function update()
    {
        $model = static::$model;
        $entity = $model::find($this->req->param->id);
        $entity->update($this->req->param->{ static::$param });
        $entity->save();
        $this->redirectTo(static::$index);
    }

    /**
     * delete action for removing an existing entity
     */
    public function delete()
    {
        $model = static::$model;
        $entity = $model::find($this->req->param->id);
        $entity->delete();
        $this->redirectTo(static::$index);
    }
}

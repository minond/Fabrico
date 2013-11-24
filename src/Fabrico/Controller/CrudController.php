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
}


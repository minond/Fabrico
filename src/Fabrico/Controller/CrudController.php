<?php

namespace Fabrico\Controller;

abstract class CrudController extends BaseController
{
    /**
     * crud actions
     */
    const CREATE_ACTION = 'create';
    const RETRIEVE_ACTION = 'retrieve';
    const UPDATE_ACTION = 'update';
    const DELETE_ACTION = 'delete';
}


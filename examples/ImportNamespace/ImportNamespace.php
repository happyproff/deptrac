<?php

namespace ImportNamespace\Models;

class Model1
{
}

namespace ImportNamespace\Controllers;

use ImportNamespace\Models;

class Controller1
{
    private $model;

    public function __construct()
    {
        $this->model = new Models\Model1();
    }

}

<?php

namespace Fabrico\Model;

use PDO;
use Exception;
use Fabrico\Application;
use Efficio\Dataset\Model as BaseModel;
use Efficio\Dataset\Storage\Model\Storage;
use Efficio\Dataset\Storage\Model\DatabaseStorage;
use Efficio\Dataset\Storage\Model\FileStorage;

// --------------------------------------
// lasciate ogne speranza, voi ch'intrate
// --------------------------------------

Application::call(function() {
    $key = 'db:' . getenv('APP_ENV');

    switch ($this->conf->get("$key:type")) {
        case 'file':
            class Model extends BaseModel
            {
                use FileStorage;
            }

            Model::setDirectory(
                $this->conf->get("$key:flat")
            );

            break;

        case 'pdo':
            class Model extends BaseModel
            {
                use DatabaseStorage;
            }

            Model::setConnection(
                new PDO($this->conf->get("$key:dsn"), null, null, [
                    // todo cannot be hard-coded
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ])
            );

            break;

        default:
            throw new Exception('Invalid db configuration');
    }
});


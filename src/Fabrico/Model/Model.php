<?php

namespace Fabrico\Model;

use PDO;
use Exception;
use Fabrico\Application;
use Efficio\Dataset\Model as BaseModel;
use Efficio\Dataset\Storage\Model\DatabaseStorage;
use Efficio\Dataset\Storage\Model\FileStorage;

// --------------------------------------
// lasciate ogne speranza, voi ch'intrate
// --------------------------------------

Application::call(function () {
    $key = 'db:' . getenv('APP_ENV');

    // class declaration in conditional statement? yeah. I do what ever the
    // fuck I want, bitch
    switch ($this->conf->get("$key:type")) {
        case 'file':
            require sprintf('%s/ModelFileStorage.php', __DIR__);
            Model::setDirectory($this->conf->get("$key:flat"));
            break;

        case 'pdo':
            require sprintf('%s/ModelDatabaseStorage.php', __DIR__);
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

<?php

namespace Fabrico\Model;

use Efficio\Dataset\Model as BaseModel;
use Efficio\Dataset\Storage\Model\DatabaseStorage;

class Model extends BaseModel
{
    use DatabaseStorage;
}

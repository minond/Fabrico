<?php

namespace Fabrico\Initializer\StdInitializers;

use Fabrico\Initializer\JitInitializer;
use Efficio\Utilitatis\Word;

/**
 * add configuration macros:
 * #resource Model, adds crud routes for model
 * #import path, import a configuaiton file
 */
class ConfigMacros extends JitInitializer
{
    public function initialize()
    {
        $conf =& $this->conf;

        // yaml route shortcut
        $conf->registerMacroPreParser('/#resource (.+)/', function($matches, $raw) {
            $word = new Word;
            $models = (array) array_pop($matches);
            $macros = (array) array_pop($matches);

            foreach ($models as $index => $resource) {
                $model = $word->pluralize($resource);
                $model = strtolower($model);
                $clazz = ucwords($model);

                $template = <<<ROUTE
/$model.?{format?}:
  controller: $clazz
  action: index
  method: GET
  _resource: $resource
  _generator: {$model}_path

/$model/add:
  controller: $clazz
  action: add
  method: GET
  _resource: $resource
  _generator: add_{$model}_path

/$model/edit/{id}:
  controller: $clazz
  action: edit
  method: GET
  _resource: $resource
  _generator: edit_{$model}_path

/$model/create:
  controller: $clazz
  action: create
  method: POST
  _resource: $resource
  _generator: create_{$model}_path

/$model/update/{id}:
  controller: $clazz
  action: update
  _resource: $resource
  _generator: update_{$model}_path

/$model/delete/{id}:
  controller: $clazz
  action: delete
  _resource: $resource
  _generator: delete_{$model}_path
ROUTE;


                $raw = str_replace($macros[ $index ], $template, $raw);
            }

            return $raw;
        });

        // yaml import file comments
        $conf->registerMacroPreParser('/#import (.+)/', function($matches, $raw) {
            $confs = [];

            foreach ($matches[0] as $index => $macro) {
                $import = explode(' ', $matches[1][ $index ], 2);

                if (count($import) > 1) {
                    $data = explode(',', $import[1]);
                    $import = $import[0];

                    foreach ($data as $index => $row) {
                        list($key, $val) = explode(':', $row);
                        $data[ trim($key) ] = trim($val);
                        unset($data[ $index ]);
                    }
                } else {
                    $import = $import[0];
                    $data = [];
                }

                $confs[ $import ] = $data;
                $raw = str_replace($macro, '', $raw);
            }

            return !count($confs) ? $raw : sprintf('~imports: %s%s%s',
                json_encode($confs), PHP_EOL, $raw);
        });

        // import extras
        $conf->registerMacroPostParser(function(& $obj) {
            if (isset($obj['~imports'])) {
                $imports = $obj['~imports'];
                unset($obj['~imports']);

                foreach ($imports as $import => $mergedata) {
                    $data = $this->load($import, $mergedata);
                    $obj = array_merge($obj, $data);
                }
            }

            return $obj;
        });
    }
}


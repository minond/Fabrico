<?php

namespace Fabrico\Initializer;

use Fabrico\Initializer;
use Efficio\Configurare\Configuration;

abstract class JitInitializer implements Initializer
{
    /**
     * @var Configuration
     */
    protected $conf;

    /**
     * @param array $prop
     */
    public function setProperties(array $props = [])
    {
        foreach ($props as $key => & $prop) {
            if (property_exists($this, $key)) {
                $this->{ $key } = $prop;
            }
        }
    }

    /**
     * @param Configuration $conf
     */
    public function setConfiguration(Configuration $conf)
    {
        $this->conf = $conf;
    }

    /**
     * because they may not all need to be terminated
     */
    public function terminate()
    {
    }
}

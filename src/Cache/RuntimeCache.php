<?php

namespace Fabrico\Cache;

/**
 * base Cache class
 */
class RuntimeCache extends Cache
{
    /**
     * internal cache
     */
    private $data = [];

    /**
     * @inheritdoc
     */
    public function set($key, $val)
    {
        $this->data[ $key ] = $val;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        return $this->data[ $key ];
    }

    /**
     * @inheritdoc
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * @inheritdoc
     */
    public function del($key)
    {
        return unset($this->data[ $key ]);
    }
}

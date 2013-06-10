<?php

namespace Fabrico\Cache;

use Fabrico\Core\Application;

/**
 * save everything into a flat file
 */
class FileCache extends RuntimeCache
{
    /**
     * for fs system failure testing
     * @var string
     */
    protected static $file_exists = 'file_exists';

    /**
     * for fs system failure testing
     * @var string
     */
    protected static $is_resource = 'is_resource';

    /**
     * for fs system failure testing
     * @var string
     */
    protected static $mkdir = 'mkdir';

    /**
     * for fs system failure testing
     * @var string
     */
    protected static $touch = 'touch';

    /**
     * files in use
     * @var array
     */
    private static $locks = [];

    /**
     * name of tmp file
     * @var string
     */
    private $filename;

    /**
     * @var resource (file)
     */
    protected $file;

    /**
     * dump needed flag
     * @var boolean
     */
    protected $changed = false;

    /**
     * @param string $file
     * @return FileCache
     */
    public static function create($file)
    {
        return array_key_exists($file, self::$locks) ?
            self::$locks[ $file ] : new self($file);
    }

    /**
     * @param string $file
     * @param int $tmp
     * @throws \Exception
     */
    public function __construct($file, $tmp = false)
    {
        // functions
        $file_exists = static::$file_exists;
        $is_resource = static::$is_resource;
        $touch = static::$touch;
        $mkdir = static::$mkdir;

        if ($tmp) {
            $ds = DIRECTORY_SEPARATOR;
            $app = Application::getInstance();
            $conf = $app->getConfiguration();
            $path = sys_get_temp_dir() . $ds . FABRICO_NAMESPACE . $ds;
            $file = $path . $conf->get('project:namespace') . $ds . $file;
        }

        $this->filename = $file;
        if (array_key_exists($file, self::$locks)) {
            throw new \Exception("{$file} is already in use.");
        }

        $dir = dirname($file);

        if (!$file_exists($dir) && !$mkdir($dir, 0777, true)) {
            throw new \Exception("Error creating cache file directory: {$dir}");
        } else if ($file_exists($file) || $touch($file)) {
            self::$locks[ $file ] =& $this;
            $content = file_get_contents($file);
            $file = fopen($file, 'r+');

            if ($is_resource($file)) {
                $this->file = $file;

                if ($content) {
                    $this->data = json_decode($content, true);
                }
            } else {
                throw new \Exception("Error opening {$file} cache file.");
            }
        } else {
            throw new \Exception("Error finding {$file} cache file.");
        }
    }

    /**
     * saves $data to $file
     */
    public function __destruct()
    {
        if ($this->changed) {
            is_resource($this->file) && fwrite($this->file,
                json_encode($this->data));
        }

        unset(self::$locks[ $this->filename ]);
    }

    /**
     * @inheritdoc
     */
    public function set($key, $val)
    {
        $this->data[ $key ] = $val;
        $this->changed = true;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function del($key)
    {
        unset($this->data[ $key ]);
        $this->changed = true;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $this->data = [];
        $this->changed = true;
    }

    /**
     * cache file name getter
     * @return string
     */
    public function getFileName()
    {
        return $this->filename;
    }
}

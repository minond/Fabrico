<?php

namespace Fabrico\Cache;

use Fabrico\Core\Application;

/**
 * save everything into a flat file
 */
class FileCache extends RuntimeCache
{
    /**
     * files in use
     * @var array
     */
    private static $locks = [];

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
        if ($tmp) {
            $ds = DIRECTORY_SEPARATOR;
            $app = Application::getInstance();
            $conf = $app->getConfiguration();
            $path = sys_get_temp_dir() . $ds . FABRICO_NAMESPACE . $ds;
            $file = $path . $conf->get('project:namespace') . $ds . $file;
        }

        if (array_key_exists($file, self::$locks)) {
            throw new \Exception("{$file} is already in use.");
        }

        $dir = dirname($file);

        if (!file_exists($dir) && !mkdir($dir, 0777, true)) {
            throw new \Exception("Error creating cache file directory: {$dir}");
        } else if (file_exists($file) || touch($file)) {
            self::$locks[ $file ] =& $this;
            $content = file_get_contents($file);
            $file = fopen($file, 'r+');

            if (is_resource($file)) {
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
        $this->dump();
    }

    /**
     * saves $data to $file
     * @return boolean
     */
    public function dump()
    {
        $ok = false;

        if ($this->changed) {
            $ok = is_resource($this->file) && fwrite($this->file,
                json_encode($this->data));
        } else {
            $ok = true;
        }

        return $ok;
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
}

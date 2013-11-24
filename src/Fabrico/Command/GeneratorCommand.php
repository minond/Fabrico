<?php

namespace Fabrico\Command;

use Efficio\Utilitatis\Merger;
use Efficio\Utilitatis\Word;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * provides access to templates and other functions shared by commands that
 * are for generating files
 */
class GeneratorCommand extends Command
{
    /**
     * output templates
     */

    /**
     * @var Word
     */
    protected $word;

    /**
     * @var Merger
     */
    protected $merger;

    /**
     * @param string $name
     */
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->word = new Word;
        $this->merger = new Merger;
    }

    /**
     * return a template file's content
     * @param string $file
     * @return string
     */
    protected function getTemplate($file)
    {
        return file_get_contents(sprintf('%s/templates/%s', __dir__, $file));
    }

    /**
     * @param string $path
     * @param string $data
     * @param OutputInterface $output
     */
    public function createFile($path, $data, OutputInterface $output = null)
    {
        if (touch($path)) {
            if (file_put_contents($path, $data) !== false) {
                $this->ok($path, $output);
            } else {
                $this->err("writing to $path", $output);
            }
        } else {
            $this->err("creating $path", $output);
        }
    }

    /**
     * @param string $path
     * @param OutputInterface $output
     */
    public function createDirectory($path, OutputInterface $output = null)
    {
        if (is_dir($path)) {
            $this->ok($path, $output);
        } else if (mkdir($path, 0777, true)) {
            $this->ok($path, $output);
        } else {
            $this->err("creating $path", $output);
        }
    }

    /**
     * @param string $msg
     * @param OutputInterface $output
     */
    protected function ok($msg, OutputInterface $output = null)
    {
        if ($output) {
            $output->writeln(sprintf(
                '   [<fg=green;options=bold>ok</fg=green;options=bold>]  <fg=yellow>%s</fg=yellow>',
                $msg
            ));
        }
    }

    /**
     * @param string $msg
     * @param OutputInterface $output
     */
    protected function err($msg, OutputInterface $output = null)
    {
        if ($output) {
            $output->writeln(sprintf(
                '  [<fg=red;options=bold>err</fg=red;options=bold>]  <fg=yellow>%s</fg=yellow>',
                $msg
            ));
        }
    }
}


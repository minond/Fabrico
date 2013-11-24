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
    const TMPL_GEN_FILE = '   <fg=green>file</fg=green>      <fg=yellow>%s</fg=yellow>';
    const TMPL_ERR_GEN_FILE = '  <fg=red>error</fg=red>      <fg=yellow>%s</fg=yellow>';
    const TMPL_ERR_WRITE_FILE = '  <fg=red>error</fg=red>      <fg=yellow>%s</fg=yellow>';
    const TMPL_GEN_DIR = '    <fg=green>dir</fg=green>      <fg=yellow>%s</fg=yellow>';
    const TMPL_ERR_GEN_DIR = '  <fg=red>error</fg=red>      <fg=yellow>%s</fg=yellow>';

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
                $output && $output->writeln(sprintf(static::TMPL_GEN_FILE, $path));
            } else {
                $output && $output->writeln(sprintf(static::TMPL_ERR_WRITE_FILE, $path));
            }
        } else {
            $output && $output->writeln(sprintf(static::TMPL_ERR_GEN_FILE, $path));
        }
    }

    /**
     * @param string $path
     * @param OutputInterface $output
     */
    public function createDirectory($path, OutputInterface $output = null)
    {
        if (is_dir($path)) {
            $output && $output->writeln(sprintf(static::TMPL_GEN_DIR, $path));
        } else if (mkdir($path, 0777, true)) {
            $output && $output->writeln(sprintf(static::TMPL_GEN_DIR, $path));
        } else {
            $output && $output->writeln(sprintf(static::TMPL_ERR_GEN_DIR, $path));
        }
    }
}


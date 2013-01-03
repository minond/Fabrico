<?php

/**
 * @package fabrico\fs
 */
namespace fabrico\fs;

use fabrico\project\Project;

class FileAccess {
	/**
	 * @var Project
	 */
	private $project;

	/**
	 * @var string
	 */
	private $last_dir;

	/**
	 * @param Project $project
	 */
	public function __construct(Project $project) {
		$this->project = $project;
	}

	/**
	 * @param string $file
	 * @return string
	 */
	public function with_path($file) {
		return $this->last_dir . $file;
	}

	/**
	 * @param string $dir
	 * @param string $like
	 * @return array
	 */
	public function files_in($dir, $like = null) {
		$this->last_dir = $dir;
		$files = scandir($dir);
		$finfo = new \finfo(FILEINFO_MIME);
		$textfiles = [];

		foreach ($files as $file) {
			if (substr($finfo->file($dir . DIRECTORY_SEPARATOR . $file), 0, 4)  === 'text'){
				$textfiles[] = $file;
			}
		}

		if (is_string($like) && strlen($like)) {
			$temp = [];
			
			foreach ($textfiles as $index => $file) {
				if (preg_match($like, $file)) {
					$temp[] = $file;
				}
			}

			$textfiles = $temp;
		}

		unset($finfo);
		unset($files);

		return $textfiles;
	}

	/**
	 * @param string $content
	 * @param string $filename
	 * @return string
	 */
	public function contains($content, $filename) {
		if (!file_exists($filename)) {
			if (file_exists($this->last_dir . DIRECTORY_SEPARATOR . $filename)) {
				$filename = $this->last_dir . DIRECTORY_SEPARATOR . $filename;
			}
			else {
				throw new \Exception("File {$filename} does not exists");
			}
		}

		return strpos(file_get_contents($filename), $content) !== false;
	}
}

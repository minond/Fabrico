<?php

use fabrico\controller\Controller;
use fabrico\controller\CliAccess;
use fabrico\cli\CliSelfDocumenting;
use fabrico\cli\CliArgLoader;
use fabrico\cli\CliIo;
use fabrico\project\Project;
use fabrico\core\util;

class Generate extends Controller implements CliAccess {
	use CliSelfDocumenting, CliArgLoader, CliIo;

	/**
	 *  @var Project
	 */
	private $project;

	/**
	 * @param Project $project
	 */
	public function __construct($wd = '', Project $project = null) {
		$this->wd = $wd ? $wd : $this->core->wd;
		$this->project = !is_null($project) ? $project : $this->core->project;
	}

	/**
	 * @param string $name
	 * @param string $type
	 * @return string
	 */
	private function get_full_name($name, $type) {
		return $this->project->dr($type) . $name . $this->project->ext($type);
	}

	/**
	 * @param string $name
	 * @param string $type
	 * @return string
	 */
	private function get_template_content($name, $type) {
		$file = $this->project->find_project_file(
			$this->get_full_name($name, $type),
			Project::FILETEMPLATE
		);

		return !is_null($file) ? file_get_contents($file) : '';
	}

	/**
	 * @param string $name
	 * @param string $template
	 * @param string $content
	 * @param boolean $force
	 */
	private function save_project_file($name, $type, $content, $force) {
		$file = $this->get_full_name($name, $type);
		$file = $this->project->get_root() . $file;

		if (!file_exists($file) || $force) {
			if (touch($file) && file_put_contents($file, $content)) {
				$this->cout(
					'Successfully created %s (%s)%s%s%s',
					$name, $type, PHP_EOL, $file, PHP_EOL
				);
			}
			else {
				$this->cout(
					'Error creating or writing to %s%s',
					$name, PHP_EOL
				);
			}
		}
		else {
			$this->cout(
				'Cannot overwrite %s, use -f to allow overwrite%s',
				$file, PHP_EOL
			);
		}
	}

	/**
	 * generate a new controller
	 * @param string $name
	 * @param boolean $force
	 * @param string $template
	 */
	public function controller($name, $force = false, $template = 'std') {
		$template = $this->get_template_content($template, Project::CONTROLLER);
		$this->save_project_file($name, Project::CONTROLLER, util::merge($template, [
			'name' => $name,
			'namespace' => $this->configuration->core->project->namespace .
				$this->configuration->core->namespace->controller
		], '@'), $force);
	}

	/**
	 * generate a new web controller
	 * @param string $name
	 * @param boolean $force
	 */
	public function web_controller($name, $force) {
		$this->controller($name, $force, 'web');
	}

	/**
	 * generate a new cli controller
	 * @param string $name
	 * @param boolean $force
	 */
	public function cli_controller($name, $force) {
		$this->controller($name, $force, 'cli');
	}
}

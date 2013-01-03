<?php

namespace @{namespace};

use fabrico\controller\Controller;
use fabrico\controller\CliAccess as Cli;
use fabrico\cli\CliSelfDocumenting;
use fabrico\cli\CliArgLoader;
use fabrico\cli\CliIo;

class @{name} extends Controller implements Cli {
	use CliSelfDocumenting, CliArgLoader, CliIo;

	public function trigger() {
		
	}
}

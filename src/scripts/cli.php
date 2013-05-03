<?php

require 'autoload.php';

use Fabrico\Core\Application;
use Fabrico\Response\Http\Response;
use Fabrico\Request\Http\Request;
use Fabrico\Project\Configuration;
use Fabrico\Event\Listeners;
use Fabrico\Cache\RuntimeCache;

use Fabrico\Core\Ext;
use Fabrico\Core\ExtensionManager;

use Symfony\Component\Console\Application as Terminal;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class TestCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('test:cmd')
            ->setDescription('Nisi nascetur non magna ultrices augue et ac, amet massa aliquam sociis. Proin nascetur. Elit ut eu pellentesque? Magna, nunc, vel tempor placerat est? Augue etiam mus lorem dapibus dis dis et lorem! Porta sed enim scelerisque sagittis turpis placerat auctor mattis, in, montes in dictumst lacus mattis, in, scelerisque, nec risus est sit arcu ridiculus nec sociis phasellus turpis! Magnis placerat, augue eros! Turpis placerat lundium! Tristique dignissim tempor aenean massa.')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Name of person'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        $name = $dialog->ask(
            $output, 'Name? ', 'Marcos'
        );
        $output->writeln("<question>{$name}</question>");
    }
}


call_user_func(function() {
    $app = new Application;
    $res = new Response;
    $req = new Request;
    $conf = new Configuration(new RuntimeCache);

    $req->setData($_REQUEST);
    $app->setRequest($req);
    $app->setResponse($res);
    $app->setConfiguration($conf);
    $app->setRoot(FABRICO_PROJECT_ROOT);
    $app->setNamespace($conf->get('project:namespace'));

    // handlers
    $req->addResponseHandlers($conf->get('project:handlers:http'));

    // project bootstraps
    if (count($conf->get('project:bootstrap'))) {
        foreach ($conf->get('project:bootstrap') as $file) {
            require_once FABRICO_PROJECT_ROOT . $file;
        }
    }







$ext = new ExtensionManager($conf);

// var_dump($ext->disable('view_backtrace')); die;
// var_dump(Ext::enable('view_backtrace'));die;
$ext->install('view_backtrace');die;
// var_dump($ext->config('view_backtrace:source:line_offset', 10));die;














});

$term = new Terminal;
$term->add(new TestCommand);
$term->run();

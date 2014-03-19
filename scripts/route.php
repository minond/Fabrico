<?php

namespace Fabrico\Runtime\Setup\Http;

use Efficio\Http\Request;
use Efficio\Http\Response;
use Efficio\Http\RuleBook;
use Efficio\Http\Status;
use Exception;
use Fabrico\Renderer;
use Fabrico\Runtime\Instance;

require 'vendor/autoload.php';

$app = Instance::create();
$conf = $app->getConfiguration();

$res = new Response;
$req = new Request;
$req->importFromGlobals();

$rules = new RuleBook;
$rules->load($conf->get('routes'), true);
$app->setRuleBook($rules);

$renderer = new Renderer;
$renderer->handlers($conf->get('app:views:renderers'));
$app->setRenderer($renderer);

try {
    if (!$app->route($req, $res)) {
        $res->setStatusCode(Status::NOT_FOUND);
        $res->setContentType(Response::HTML);
        $res->setContent(file_get_contents('public/404.html'));
    }
} catch (Exception $ex) {
    // reset headers in case something like a redirect header was set before
    // the exception was thrown. we don't want to send any of those, or
    // anything else other than a status code and the error page content

    // TODO: update Http and Utilitatis to do this:
    // $res->header->clear();
    // $res->setStatusCode(Status::INTERNAL_SERVER_ERROR);

    // rather than having to do this:
    $res->header = new \Efficio\Utilitatis\PublicObject;
    $res->setStatusCode(500);

    $res->setContentType(Response::HTML);
    $res->setContent(file_get_contents('public/500.html'));

    $res->sendHeaders();
    $res->sendContent();

    throw $ex;
}

$res->sendHeaders();
$res->sendContent();

return $res;

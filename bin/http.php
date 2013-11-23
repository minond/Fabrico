<?php

namespace Fabrico\Runtime\Setup\Http;

use Fabrico\Renderer;
use Efficio\Http\Request;
use Efficio\Http\Response;
use Efficio\Http\Status;
use Efficio\Http\RuleBook;

$app = require 'app.php';
$conf = $app->getConfiguration();

$res = new Response;
$res->setStatusCode(Status::NOT_FOUND);
$res->setContentType(Response::HTML);
$res->setContent('404, not found...');
$app->setResponse($res);
$app->setRequest(new Request(true));

$rules = new RuleBook;
$rules->load($conf->get('routes'), true);
$app->setRuleBook($rules);

$renderer = new Renderer;
$renderer->handlers($conf->get('app:renderers'));
$app->setRenderer($renderer);

$app->handle();
$res->sendHeaders();
$res->sendContent();


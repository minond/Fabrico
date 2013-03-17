<?php

require 'autoload.php';

use Fabrico\Core\Application;
use Fabrico\Response\HttpResponse;
use Fabrico\Request\HttpRequest;

call_user_func(function() {
    $app = new Application;
    $req = new HttpRequest;
    $res = null;
    $par = $_REQUEST;

    $app->setRoot('/home/server/' . $par['_project']);

    // build the request object
    if (isset($par['_controller'])) {
        $req->setController($par['_controller']);

        if (isset($par['_method'])) {
            $req->setMethod($par['_method']);
        } else if (isset($par['_action'])) {
            $req->setAction($par['_action']);
        }
    } else if (isset($par['_file'])) {
        $req->setFile($par['_file']);
    }

    // clean up the parameters object
    unset($par['_project']);
    unset($par['_file']);
    unset($par['_controller']);
    unset($par['_action']);
    unset($par['_method']);
    $req->setData($par);
    $res = $req->respondWith();
    $app->setRequest($req);
    $app->setResponse($res);

    if ($req->valid()) {
        $json = new StdClass;
        $json->inner = new StdClass;
        $json->inner->text = 'hi';

        $res->getOutput()->setContent($json);
        $res->sendHeaders();
        $res->send();

        // $res->getOutput()->output();
        // print_r($res->getOutput());
        // die("Routing request");
    } else {
        die("Invalid request");
    }
});

<?php

use Fabrico\Event\Reporter;

Reporter::after('fabrico.request.http.request:preparehandler', function($info) {
    // ...
});

<?php
require __DIR__.'/../vendor/autoload.php';

$settings = require __DIR__ . '/../bootstrap/settings.php';

$app = new Slim\App($settings);


require __DIR__ . '/../bootstrap/overwrites.php';

// set up dependencies
require __DIR__ . '/../bootstrap/dependencies.php';

// register middleware
//require __DIR__ . '/../bootstrap/middleware.php';

// register routes
require __DIR__ . '/../bootstrap/routes.php';

$app->run();

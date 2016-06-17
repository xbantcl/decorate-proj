<?php require __DIR__.'/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Decorate\Validation\Validator;

$settings = require __DIR__ . '/settings.php';

$app = new Slim\App($settings);

$capsule = new Capsule;

$dataBase = [
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'decorate',
    'username'  => 'root',
    'password'  => 'toor',
    'charset'   => 'utf8',
    'collation' => 'utf8_general_ci',
    'prefix'    => ''
];

$capsule->addConnection($dataBase);
$capsule->setEventDispatcher(new Dispatcher);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container = $app->getContainer();
$container['validation'] = new Validator();

$redisConfig = require __DIR__ . '/../config/redis.php';
foreach ($redisConfig as $key => $redis) {
    $container[$key] = $redis;
}

// register routes
require __DIR__ . '/../apps/Decorate/routes.php';
require __DIR__ . '/../apps/Passport/routes.php';

// set timezone for timestamps etc
date_default_timezone_set('UTC');


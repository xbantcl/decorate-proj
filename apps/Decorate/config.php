<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;

/**
 * Configure the database and boot Eloquent
 */
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

$capsule->addConnection($dataBase, 'primary');

$capsule->setEventDispatcher(new Dispatcher);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// set timezone for timestamps etc
date_default_timezone_set('UTC');

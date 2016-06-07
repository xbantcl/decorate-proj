<?php
require __DIR__.'/../vendor/autoload.php';

$app = new Slim\App();

$app->post('/{id}', 'Decorate\Services\UserService:test');

$app->run();

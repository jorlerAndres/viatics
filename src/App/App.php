<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/../../vendor/autoload.php';

$aux = new \DI\Container();
AppFactory::setContainer($aux);

$app = AppFactory::create();
$container = $app->getContainer();

require __DIR__ . '/Routes.php';
require __DIR__ . '/Config.php';
require __DIR__ . '/Dependencies.php';

$app->addRoutingMiddleware();

$app->run();

<?php

use Slim\Routing\RouteCollectorProxy;

/* $app->post('/login', 'App\Controllers\LoginController:autenticar');
$app->get('/login', 'App\Controllers\ViewController:vistaLogin'); */
$app->get('/logout', 'App\Controllers\LoginController:logout');
$app->get('/', 'App\Controllers\ViewController:vistaLogin');
$app->post('/', 'App\Controllers\LoginController:autenticar');

$app->group('', function (RouteCollectorProxy $group) {
    $group->get('/home', 'App\Controllers\ViewController:vistaHome')->setName('home');
    $group->get('/ayuda', 'App\Controllers\ViewController:vistaAyuda')->setName('ayuda');
    
})->add('App\Middleware\LoginMiddleware');



$app->group('/api', function (RouteCollectorProxy $group) {

    $group->post('/tareas', 'App\Controllers\TareasController:getTareas')->setName('getTareas');
    $group->post('/informe', 'App\Controllers\InformeController:operationExcel')->setName('operation');
   
    
})->add('App\Middleware\LoginMiddleware');

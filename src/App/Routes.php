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

    $group->post('/cuotas/set', 'App\Controllers\CuotasController:setCuotas')->setName('setCuotas');
    $group->post('/cuotas/get', 'App\Controllers\CuotasController:getCuotas')->setName('getCuotas'); 
    $group->post('/compra/set', 'App\Controllers\CompraController:setCompra')->setName('setCompra');
    $group->post('/compra/get', 'App\Controllers\CompraController:getCompra')->setName('getCompra');
   
    
})->add('App\Middleware\LoginMiddleware');

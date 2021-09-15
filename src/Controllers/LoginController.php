<?php

namespace App\Controllers;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\LoginModel;

class LoginController
{

    public function validar(Request $request,Response $response, $arg){
        $L = new LoginModel();
        $datos=(array)$request->getParsedBody();
        $response->getBody()->write(json_encode($L->validar($datos)));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }


    public function autenticar(Request $request,Response $response, $arg){
        $L = new LoginModel();
        $datos=(array)$request->getParsedBody();
        //var_dump($datos);
        $response->getBody()->write(json_encode($L->autenticar($datos)));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
    
    public function logout(Request $request,Response $response, $arg){
        $L = new LoginModel();
        $L->logout();
        return $response
        ->withHeader('Location', '/')
        ->withStatus(200);
    }

}

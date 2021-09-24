<?php

namespace App\Controllers;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\UsuarioModel;


class UsuarioController
{
    public function getAll(Request $request, Response $response, $arg)
    {
        $U = new UsuarioModel();
        $response->getBody()->write(json_encode($U->getResumeUser()));
        return $response
            ->withHeader('Access-Control-Allow-Origin', "*")
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function getUser(Request $request, Response $response, $arg)
    {   
        $datos=(array)$request->getParsedBody();
        $U = new UsuarioModel();
        $response->getBody()->write(json_encode($U->getUser($datos)));
        return $response
            ->withHeader('Access-Control-Allow-Origin', "*")
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
    public function setUser(Request $request, Response $response, $arg)
    {   
        $datos=(array)$request->getParsedBody();
        $U = new UsuarioModel();
        $response->getBody()->write(json_encode($U->setUser($datos)));
        return $response
            ->withHeader('Access-Control-Allow-Origin', "*")
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }



    


}

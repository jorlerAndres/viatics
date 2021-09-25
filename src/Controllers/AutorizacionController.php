<?php

namespace App\Controllers;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\AutorizacionModel;


class AutorizacionController
{
    public function setAutorizacion(Request $request, Response $response, $arg)
    {   
        $datos=(array)$request->getParsedBody();
        $U = new AutorizacionModel();
        $response->getBody()->write(json_encode($U->setAutorizacion($datos)));
        return $response
            ->withHeader('Access-Control-Allow-Origin', "*")
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }



    


}

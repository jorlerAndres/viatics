<?php

namespace App\Controllers;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\CuotasModel;


class CuotasController
{
    public function setCuotas(Request $request, Response $response, $arg)
    {   
        $datos=(array)$request->getParsedBody();
        $C = new CuotasModel();
        $response->getBody()->write(json_encode($C->setCuotas($datos)));
        return $response
            ->withHeader('Access-Control-Allow-Origin', "*")
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function getCuotas(Request $request, Response $response, $arg)
    {   
        $datos=(array)$request->getParsedBody();
        $C = new CuotasModel();
        $response->getBody()->write(json_encode($C->getCuotas($datos)));
        return $response
            ->withHeader('Access-Control-Allow-Origin', "*")
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }



    


}

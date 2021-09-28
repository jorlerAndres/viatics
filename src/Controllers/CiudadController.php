<?php

namespace App\Controllers;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\CiudadModel;


class CiudadController
{
    public function getCiudad(Request $request, Response $response, $arg)
    {   
        $datos=(array)$request->getParsedBody();
        $U = new CiudadModel();
        $response->getBody()->write(json_encode($U->getCiudad($datos)));
        return $response
            ->withHeader('Access-Control-Allow-Origin', "*")
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }



    


}

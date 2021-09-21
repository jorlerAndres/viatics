<?php

namespace App\Controllers;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\ZonasModel;


class ZonasController
{
    public function getZonas(Request $request, Response $response, $arg)
    {   
        $datos=(array)$request->getParsedBody();
        $U = new ZonasModel();
        $response->getBody()->write(json_encode($U->getZonas($datos)));
        return $response
            ->withHeader('Access-Control-Allow-Origin', "*")
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }



    


}

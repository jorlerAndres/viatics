<?php

namespace App\Controllers;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\TareasModel;


class TareasController
{
    public function getTareas(Request $request, Response $response, $arg)
    {   
        $datos=(array)$request->getParsedBody();
        $U = new TareasModel();
        $response->getBody()->write(json_encode($U->getDataTareas($datos)));
        return $response
            ->withHeader('Access-Control-Allow-Origin', "*")
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }



    


}

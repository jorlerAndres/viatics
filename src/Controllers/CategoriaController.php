<?php

namespace App\Controllers;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\CategoriaModel;


class CategoriaController
{
    public function getCategorias(Request $request, Response $response, $arg)
    {   
        $datos=(array)$request->getParsedBody();
        $U = new CategoriaModel();
        $response->getBody()->write(json_encode($U->getCategorias($datos)));
        return $response
            ->withHeader('Access-Control-Allow-Origin', "*")
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }



    


}

<?php

namespace App\Controllers;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\SubcategoriaModel;


class SubcategoriaController
{
    public function getSubcategorias(Request $request, Response $response, $arg)
    {   
        $datos=(array)$request->getParsedBody();
        $U = new SubcategoriaModel();
        $response->getBody()->write(json_encode($U->getSubcategorias($datos)));
        return $response
            ->withHeader('Access-Control-Allow-Origin', "*")
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }



    


}

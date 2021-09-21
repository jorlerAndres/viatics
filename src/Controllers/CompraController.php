<?php

namespace App\Controllers;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\CompraModel;


class CompraController
{
    public function setCompra(Request $request, Response $response, $arg)
    {   
        $datos=(array)$request->getParsedBody();
        $dataFile = $request->getUploadedFiles();
        $C = new CompraModel();
        $response->getBody()->write(json_encode($C->setCompra($datos,$dataFile)));
        return $response
            ->withHeader('Access-Control-Allow-Origin', "*")
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function getCompra(Request $request, Response $response, $arg)
    {   
        $datos=(array)$request->getParsedBody();
        $G = new CompraModel();
        $response->getBody()->write(json_encode($G->getCompra($datos)));
        return $response
            ->withHeader('Access-Control-Allow-Origin', "*")
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }



    


}

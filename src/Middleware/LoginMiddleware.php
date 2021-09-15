<?php

namespace App\Middleware;

use App\Models\LoginModel;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;


class LoginMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);
        $L = new LoginModel();
        $datos = (array)$request->getParsedBody();
        //var_dump($L);
        $autorizacion = $L->validar();
        //var_dump($autorizacion);
        if ($autorizacion) {
            $existingContent = (string)$response->getBody();
            $response = new Response();
            $response->getBody()->write($existingContent);
            return $response
                ->withHeader('Content-Type', 'text/html; charset=UTF-8');
            //->withHeader('Content-Type', 'application/json');
        } else {
            $response = new Response();
            $response->getBody()->write("Fail");
            return $response
                ->withHeader('Location', '/')
                ->withStatus(302);
        }
    }
}

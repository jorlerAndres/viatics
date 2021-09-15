<?php 

namespace App\Controllers;


use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use App\Models\UsuarioModel;
use App\Models\TareasModel;
use App\Models\InformeModel;

class ViewController
{
    
    
    
    //SE debe eliminar esta vistaRoot
    public function vistaRoot(Request $request, Response $response, $arg)
    {
        $response->getBody()->write('Hola');
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function vistaLogin(Request $request, Response $response, $arg)
    {
        $view = Twig::create('views/login', ['cache' => false]);
        return $view->render($response, 'index.html', ['vista' => 'Login'])
            ->withHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    public function vistaHome(Request $request, Response $response, $arg)
    {   
       
            $view = Twig::create('views', ['cache' => false]);
            return $view->render($response, 'home.html', ['vista' => 'Inicio', 
            'usuarios'=>$this->getUser(),
            'planes'=>$this->getplanUser(),
            'tareas'=>$this->getTareas()
            ])
                ->withHeader('Content-Type', 'text/html; charset=UTF-8');
     
    }

    public function getUser(){
        $user=new UsuarioModel();
        return $user->getResumeUser();
    }
    public function getplanUser(){
        $user=new UsuarioModel();
        return $user->getData();
    }
    public function getTareas(){
        $user=new TareasModel();
        return $user->getTareas();
    }
   

}

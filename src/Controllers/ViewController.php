<?php 

namespace App\Controllers;


use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use App\Models\UsuarioModel;
use App\Models\ZonasModel;
use App\Models\CategoriaModel;
use App\Models\SubcategoriaModel;
use App\Models\CuotasModel;

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
            'zonas'=>$this->getZonas(),
            'categorias'=>$this->getCategorias(),
            'subcategorias'=>$this->getSubcategorias(),
            'cuotasZonas'=>$this->getCuotasZonas(),
            'conteousuarios'=>$this->getCountUsers(),
            'rol'=>$_SESSION['id_rol'],
            'meses'=>$this->getMeses()
            
            ])
                ->withHeader('Content-Type', 'text/html; charset=UTF-8');
     
    }

    public function getUser(){
        $user=new UsuarioModel();
        return $user->getResumeUser();
    }
    public function getCountUsers(){
        $user=new UsuarioModel();
        return $user->getCountUsers(); 
    }

    public function getZonas(){
        $zonas=new ZonasModel();
        return $zonas->getZonas();
    }
    public function getCategorias(){
        $zonas=new CategoriaModel();
        return $zonas->getCategorias();
    }
    public function getSubcategorias(){
        $zonas=new SubcategoriaModel();
        return $zonas->getSubcategorias();
    }
    public function getCuotasZonas(){
        $cuotas=new CuotasModel();
        return $cuotas->getCuotas();
    }
    public function getMeses(){
        $user=new UsuarioModel();
        return $user->getMeses();
    }
     
   
    
}

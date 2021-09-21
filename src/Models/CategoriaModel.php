<?php

namespace App\Models;
if(!isset($_SESSION))  
{ 
    session_start();
} 
use Psr\Container\ContainerInterface;
use App\Models\BaseModel;


class CategoriaModel extends BaseModel
{
 
    public function getCategorias()
    {   
        $res='';
        $sql = "
        SELECT id_categoria,nombre,imagen
        from categorias AS z";
        
        $resData= $this->query($sql,array());
      
        return $resData; 
    }
   

}

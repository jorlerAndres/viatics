<?php

namespace App\Models;
if(!isset($_SESSION))  
{ 
    session_start();
} 
use Psr\Container\ContainerInterface;
use App\Models\BaseModel;


class SubcategoriaModel extends BaseModel
{
 
    public function getSubcategorias()
    {   
        $res='';
        $sql = "
        SELECT id_subcategoria,nombre,imagen
        from subcategoria AS s";
        
        $resData= $this->query($sql,array());
      
        return $resData; 
    }
   

}

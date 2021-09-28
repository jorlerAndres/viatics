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

    public function getPorcategoria($categoria)
    {   
        $data='';
        $sql = "
        SELECT id_subcategoria,nombre,imagen
        from subcategoria AS s where id_categoria=?";
        
        $resData= $this->query($sql,array($categoria['categoria']));

        for ($i=0; $i <sizeof($resData) ; $i++) { 

            $data.=' <div class="item-subcategoria" onclick="SeleccionSubcategoria(\'' .$resData[$i]['id_subcategoria']."".  '\')" id="'.$resData[$i]['id_subcategoria'].'">
            <img width="35px" data-imagen='.$resData[$i]['imagen'].'>
            <span style="color: rgb(122, 120, 120);">'.$resData[$i]['nombre'].'</span>
          </div>';
        }
      
        return $data; 
    }
   

}

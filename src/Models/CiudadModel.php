<?php

namespace App\Models;
if(!isset($_SESSION))  
{ 
    session_start();
} 
use Psr\Container\ContainerInterface;
use App\Models\BaseModel;
use DateTime;



class CiudadModel extends BaseModel
{

    public function getCiudad($datos)
    {   
      $res='';
        $array=array($datos['nombre'].'%',$datos['zona']);
        try {
        
            $sql = "SELECT NOMBRE FROM CIUDAD WHERE NOMBRE LIKE ? AND ID_ZONA=?";

            $resultado = $this->query($sql, $array);
            for ($i=0; $i <sizeof($resultado) ; $i++) { 
              

                $res.='<li   onclick="setCiudad(\'' .$resultado[$i]['NOMBRE']."".  '\' ,\'' .$datos['campo']."".  '\')" class="list-group-item list-group-item-action" style="cursor:pointer;">'.$resultado[$i]['NOMBRE'].'</li>';
            }
            
        } catch (\Throwable $th) {
            $res="No hay coincidencias";
        }
        return $res; 
    }
   

}

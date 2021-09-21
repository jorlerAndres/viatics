<?php

namespace App\Models;
if(!isset($_SESSION))  
{ 
    session_start();
} 
use Psr\Container\ContainerInterface;
use App\Models\BaseModel;


class ZonasModel extends BaseModel
{

    public function getZonas()
    {   
        $res='';
        $sql = "
        SELECT id_zona,nombre,imagen
        from zonas AS z";
        
        $resData= $this->query($sql,array());
      
        return $resData; 
    }
   

}

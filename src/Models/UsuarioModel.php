<?php

namespace App\Models;
if(!isset($_SESSION))  
{ 
    session_start();
} 
use Psr\Container\ContainerInterface;
use App\Models\BaseModel;


class UsuarioModel extends BaseModel
{

     public function getMeses()
    {   
        $sql = "
        SELECT id_mes,nombre,numero
        from meses  AS m";
        $resData= $this->query($sql);
        
        return $resData; 
    } 
    public function getResumeUser() 
    {    
        $userResume=array(); 
        $sql = "
        SELECT CONCAT(u.primer_nombre, ' ' , u.primer_apellido) as nombre,email,direccion,id_rol,id_zona
        from usuarios AS u 
        WHERE u.id_usuario=?";
        $resUser= $this->query($sql,array($_SESSION['id_usuario']));
       
        return $resUser[0]; 
    }
}

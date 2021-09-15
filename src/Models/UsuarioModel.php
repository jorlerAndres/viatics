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

     public function getData()
    {   
        $sql = "
        SELECT up.id_plan,p.nombre
        from usuarios AS u 
        JOIN usuario_plan as up on u.id_usuario=up.id_usuario
        JOIN plan as p on p.id_plan=up.id_plan
        WHERE u.id_usuario=?";
        $resData= $this->query($sql,array($_SESSION['id_usuario']));
        
        return $resData; 
    } 
    public function getResumeUser() 
    {    
        $userResume=array(); 
        $sql = "
        SELECT CONCAT(u.primer_nombre, ' ' , u.primer_apellido) as nombre,email,direccion,id_rol
        from usuarios AS u 
        JOIN usuario_plan as up on u.id_usuario=up.id_usuario
        JOIN plan as p on p.id_plan=up.id_plan
        WHERE u.id_usuario=?";
        $resUser= $this->query($sql,array($_SESSION['id_usuario']));
       
        return $resUser[0]; 
    }
}

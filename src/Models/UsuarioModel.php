<?php

namespace App\Models;
if(!isset($_SESSION))  
{ 
    session_start();
} 
use Psr\Container\ContainerInterface;
use App\Models\BaseModel;
use DateTime;


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
    public function getUser($datos) 
    {    
        $mail= strlen($datos['mail_busqueda'])>0 ? $datos['mail_busqueda']: null;
        $cedula= strlen($datos['cedula_busqueda'])>0 ? $datos['cedula_busqueda']: null;
        $sql = "
        SELECT ID_USUARIO,`CEDULA`, `PRIMER_NOMBRE`, `SEGUNDO_NOMBRE`, `PRIMER_APELLIDO`, `SEGUNDO_APELLIDO`, `EMAIL`, `TELEFONO`, `IMAGEN_PERFIL`, `ID_ZONA`, `ESTADO`, `VEHICULO`, `NUMERO_TARJETA_VIATICO`, `IMAGEN_TARJETA_VIATICO`, `USUARIO`, `PASSWORD`, `ID_ROL`, `HABILITADO`, `FECHA_CREACION`, `FECHA_MODIFICACION`

        FROM usuarios AS u 
        WHERE (u.email=? or u.cedula=?)";

        $resUser= $this->query($sql,array($mail,$cedula));
       
        return $resUser[0]; 
    }
    public function setUser($datos) 
    {    
        if(strlen(trim($datos['id_usuario']))> 0){
            $resUser=$this->UpdateUser($datos);
        }
        else{
            $resUser=$this->insertUser($datos);
        }
        
        return  $resUser; 
    }

    public function insertUser($datos) 
    {   
        $respuesta=array();
        $fechaActual= new DateTime('NOW');
        $nombres=explode(' ',$datos['nombres_usuario']);
        $segundo_nombre = sizeof($nombres) > 1 ? $nombres[1] : '';
        $apellidos=explode(' ',$datos['apellidos_usuario']);
        $segundo_apellido = sizeof($apellidos) > 1 ? $apellidos[1] : ''; 
         $sql = "
         INSERT INTO `usuarios` (ID_USUARIO,`CEDULA`, `PRIMER_NOMBRE`, `SEGUNDO_NOMBRE`, `PRIMER_APELLIDO`, `SEGUNDO_APELLIDO`, `EMAIL`, `TELEFONO`, `IMAGEN_PERFIL`, `ID_ZONA`, `ESTADO`, `VEHICULO`, `NUMERO_TARJETA_VIATICO`, `IMAGEN_TARJETA_VIATICO`, `USUARIO`, `PASSWORD`, `ID_ROL`, `HABILITADO`, `FECHA_CREACION`, `FECHA_MODIFICACION`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";


         $resUser= $this->query($sql,array( 1003,$datos['cedula_usuario'], $nombres[0],$segundo_nombre,$apellidos[0],$segundo_apellido,$datos['mail_usuario'],$datos['telefono_usuario'],'',$datos['zona_usuario'],$datos['estado_usuario'],$datos['vehiculo_usuario'],$datos['tarjeta_usuario'],'',$datos['cedula_usuario'],$datos['contrasena_usuario'],$datos['rol_usuario'],$datos['estado_usuario'],$fechaActual->format('y-m-d'),$fechaActual->format('y-m-d'))); 
        
         $respuesta['mensaje']='El usuario ha sido guardado';
         $respuesta['tipo_mensaje']='success';
        return  $respuesta; 
    }

    public function UpdateUser($datos) 
    {  
        $fechaActual= new DateTime('NOW');
        
        $sql = "UPDATE  `usuarios` SET `CEDULA`=?,  `EMAIL`=?, `TELEFONO`=?, `IMAGEN_PERFIL`=?, `ID_ZONA`=?, `ESTADO`=?, `VEHICULO`=?, `NUMERO_TARJETA_VIATICO`=?, `IMAGEN_TARJETA_VIATICO`=?, `PASSWORD`=?, ID_ROL=?,`FECHA_MODIFICACION`=? WHERE ID_USUARIO=?";

         $resUser= $this->query($sql,array($datos['cedula_usuario'],$datos['mail_usuario'],$datos['telefono_usuario'],'',$datos['zona_usuario'],$datos['estado_usuario'],$datos['vehiculo_usuario'],$datos['tarjeta_usuario'],'',$datos['contrasena_usuario'],$datos['rol_usuario'],$fechaActual->format('y-m-d'),$datos['id_usuario'])); 

         $respuesta['mensaje']='El usuario ha sido actualizado';
         $respuesta['tipo_mensaje']='success';
        
        return  $respuesta; 
    }
    public function getCountUsers() 
    {  

        $sql = "SELECT COUNT(ID_USUARIO) as TOTAL_USUARIOS,
                (SELECT COUNT(ID_USUARIO) FROM usuarios WHERE ESTADO=1) as ACTIVOS,
                (SELECT COUNT(ID_USUARIO) FROM usuarios WHERE ESTADO=0) as INACTIVOS
                FROM usuarios";

         $resUser= $this->query($sql); 
        
        return  $resUser[0]; 
    }
}

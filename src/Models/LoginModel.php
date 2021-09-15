<?php

namespace App\Models;

use App\Models\BaseModel;
use DateTime;

if(!isset($_SESSION))  
{ 
    session_start();
} 

class LoginModel extends BaseModel
{

    ///*************************** */
    public function autenticar($datos)
    {   
        $fechaActual= new DateTime('NOW');
        $res = array();
        $sql = "
        SELECT u.id_usuario FROM `usuarios` as u
        where u.usuario=?  and u.password=?";
        //var_dump($datos);
        $resultado = $this->query($sql, array($datos['user'], $datos['contrasena']));
        if(sizeof($resultado)==1){
           
            $_SESSION['ingreso_id'] = 1;
            $_SESSION['id_usuario'] = $resultado[0]['id_usuario'];
            $res["id"] = $_SESSION['id_usuario'];
            $res["alert"] = "success";
            $res["mensaje"] = "Te damos la bienvenida!";
            //(new Logs())->regLog(1, 'Ingreso','Login');
        }else{

            $res["alert"] = "danger";
            $res["mensaje"] = "Credenciales no validas!";
        }        
        return $res;
    }


    public function logout(){

        //(new Logs())->regLog(2, 'Salida','Login');
        session_destroy();
    }


    public function validar()
    {  
        if ($_SESSION['id_usuario']) {
            return true;
        }
        else {
            return false;
        }
    }

}
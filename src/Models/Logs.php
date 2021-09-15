<?php


namespace App\Models;


if(!isset($_SESSION)) 
{ 
    session_start(); 
} 


use App\Models\BaseModel;

class Logs extends BaseModel{
    
       /**
        |1|Ingresó a la plataforma
        |2|Salió de la plataforma cerrando sesión 
        |3|Carga de dashboard
        |4|Borrado de dashboard
        |5|Carga de informe
        |6|borrado de informe
        */
    
    public function regLog($accion,$comentario='',$archivo=''){
        $sql = "
        INSERT INTO log_historico (ID_USUARIO, ID_ACCION, COMENTARIO, ARCHIVO,FECHA_LOG,HORA_LOG) VALUES (?,?,?,?,curdate(),curtime());
        ";

        $resultado = $this->query($sql, array($_SESSION['id_usuario'],$accion,$comentario,$archivo));
    }

}
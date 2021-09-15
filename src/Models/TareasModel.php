<?php

namespace App\Models;
if(!isset($_SESSION))  
{ 
    session_start();
} 
use Psr\Container\ContainerInterface;
use App\Models\BaseModel;


class TareasModel extends BaseModel
{

    public function getDataTareas($datos)
    {   
        $res='';
        $sql = "
        SELECT id_tarea,descripcion,fecha_entrega,p.ruta_archivos
        from tarea AS t
        JOIN usuarios u on u.id_usuario=t.usuario_propietario
        JOIN plan p on p.id_plan=t.id_plan
        WHERE u.id_usuario=?";
        
        $sql.=" and t.id_plan=?";
       
        $resData= $this->query($sql,array($_SESSION['id_usuario'],$datos['plan']));
        
      for ($i=0; $i <sizeof($resData) ; $i++) { 

         
        
           $res.='<div class="perspective">
                    <div class="card mb-3 mt-3 ms-3 contenido">
                    <div class="d-flex flex-row">
                        <div class="d-flex flex-column">
                        <img src="'.$resData[$i]["ruta_archivos"].'logo.png" class="img-fluid rounded-start mt-1 ms-1" style="max-height: 70px;" width="100">
                        <div class="d-flex flex-row ms-3 mt-2">
                            <div class="boton">Abierto</div>
                        </div>
                        </div>
                        <div class="ms-3" style="text-align: left;">
                        <span class="ms-1">'.$resData[$i]["descripcion"].'</span>
                        <a href="#" id="codigo"><h4>'.$resData[$i]["id_tarea"].'</h4></a>
                            
                        <p class="mt-3"><i class="bi bi-chat-text ms-1 mt-1 text-secondary"></i><small class="ms-1 me-4"><b>Última Actualizacion:</b> 3 mins ago</small><i class="bi bi-clock text-secondary"></i><span><small><b> Fecha Vencimiento:</b> '.$resData[$i]["fecha_entrega"].'</small></span></p>
                        </div>
                    </div>
                    </div>
                </div>'; 
        }
        return $res; 
    }
    public function getTareas()
    {
        $sql = "
        SELECT id_tarea,descripcion,fecha_entrega,p.ruta_archivos
        from tarea AS t
        JOIN usuarios u on u.id_usuario=t.usuario_propietario
        JOIN plan p on p.id_plan=t.id_plan
        WHERE u.id_usuario=?";
        
        $resData= $this->query($sql,array($_SESSION['id_usuario']));
        return $resData;
    
    }

}

<?php

namespace App\Models;
if(!isset($_SESSION))  
{ 
    session_start();
} 
use Psr\Container\ContainerInterface;
use App\Models\BaseModel;
use DateTime;
use App\Models\CuotasModel;


class AutorizacionModel extends BaseModel
{

    public function setAutorizacion($datos)
    {   
        $respuesta=array();
        $periodo=explode('-', $datos['periodo_autorizacion']);

        try {
            $cuotas=new CuotasModel();
            if(!$cuotas->validaCuotaAutorizacion(array('mes'=>$periodo[1],'ano'=>$periodo[0]))){
                $respuesta['mensaje']='El periodo no tiene anticipo registrado';
                $respuesta['tipo_mensaje']='warning';
                $respuesta['alert']='Atención';
            }
            else{
                $fechaActual= new DateTime('NOW');
                $periodo=explode('-', $datos['periodo_autorizacion']);

                $sql = "
                 INSERT into autorizacion (ID_MES,ID_ANO,FECHA_LIMITE,FECHA_CREACION) values(?,?,?,?);";

                $resultado = $this->query($sql, array($periodo[1],$periodo[0],$datos['fecha_limite'],$fechaActual->format('y-m-d')));
                $resData= $this->query($sql, array());

                $respuesta['mensaje']='La autorizacion ha sido guardada';
                $respuesta['tipo_mensaje']='success';
                $respuesta['alert']='Registro Exitoso';
                return $respuesta;
            }
        } catch (\Throwable $th) {
            $respuesta['mensaje']='La autorizacion NO ha sido guardada';
            $respuesta['tipo_mensaje']='warning';
            $respuesta['alert']='Algo anda mal';
        }
        return $respuesta; 
    }
   

}

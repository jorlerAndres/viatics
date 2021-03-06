<?php

namespace App\Models;
if(!isset($_SESSION))  
{ 
    session_start();
} 
use Psr\Container\ContainerInterface;
use App\Models\BaseModel;
use DateTime;


class CuotasModel extends BaseModel
{

   
    public function getCuotas(){  

        try {
        
            $res='';
            $sql = "
                SELECT z.nombre,
                coalesce((select format(saldo,0) from cuota_anticipo where id_mes=extract(month from current_date) and id_zona=z.id_zona),0.0) as saldo,
                coalesce((select format(valor,0) from cuota_anticipo where id_mes=extract(month from current_date) and id_zona=z.id_zona),0.0) as valor
                from zonas z";
            
            $resData= $this->query($sql);

            for ($i=0; $i < 8 ; $i++) { 

                $res.='
                <div class="cuota-zona">
                  <div  class="d-flex flex-row">
                    <div class="d-flex flex-column ms-3 mt-2">
                     <span class="nombre-zona">'.$resData[$i]['nombre'].'</span>
                      <b><span class="valor-anticipo ">$'.$resData[$i]['valor'].'</span></b>
                     <span class="saldo-zona">$'.$resData[$i]['saldo'].'</span>
                    </div>
                    <div class="icono-ciudad"><img src="/assets/images/monedas-icono.png" width="28px"></div>
                  </div>
                </div>';
            }
            return $res;
        } 
        catch (\Throwable $th) {
            //throw $th;
        }
    
    }
    public function setCuotas($datos)
    {   
        $respuesta=array();
        try {
            $sql = "select id_cuota
                    from cuota_anticipo 
                    where ID_ZONA=? and ID_ANO=? and ID_MES=?";

            $resultado = $this->query($sql, array($datos['zona'],$datos['ano'],$datos['mes']));
            if($resultado){

                $this->actualizarCuota($datos,$resultado[0]['id_cuota']);
                (new Logs())->regLog(3, 'Se ingresa valor de anticipo por :'.$datos['valor'].' en zona:'.$datos['zona'].'','cargar anticipo');
            }
            else{
                $this->insertarCuota($datos);
                (new Logs())->regLog(3, 'Se ingresa valor de anticipo por :'.$datos['valor'].' en zona:'.$datos['zona'].'','cargar anticipo');
            }

            $respuesta['mensaje']='El anticipo ha sido guardado';
            $respuesta['tipo_mensaje']='success';
           
        } 
        catch (\Throwable $th) {
            $respuesta['mensaje']='El anticipo NO ha sido guardado';
            $respuesta['tipo_mensaje']='success';
        }
        return $respuesta;
    }
    public function insertarCuota($datos)
    {  
        $fechaActual= new DateTime('NOW'); 
        $sql = "
        INSERT into cuota_anticipo (ID_ZONA,PERIODO,ID_MES,ID_ANO,VALOR,FECHA_CREACION,HORA_CREACION,FECHA_MODIFICACION,HORA_MODIFICACION) values(?,?,?,?,?,?,?,?,?);";

        $resultado = $this->query($sql, array($datos['zona'],$datos['mes'],$datos['mes'],$datos['ano'],$datos['valor'],$fechaActual->format('y-m-d'),$fechaActual->format('H:m:s'),$fechaActual->format('y-m-d'),$fechaActual->format('H:m:s'))); 

        $this->insertSaldo($datos);
    }

    public function actualizarCuota($datos,$id_cuota)
    { 
        $fechaActual= new DateTime('NOW'); 
        $sql = "
        UPDATE cuota_anticipo set valor = (valor + ".$datos['valor'].") where  id_cuota=?;";

        $resultado = $this->query($sql, array($id_cuota)); 

        $this->insertSaldo($datos);
    }

    public function insertSaldo($datos)
    {
        $saldo=$this->calcularSaldo($datos);
        $sql = "
        UPDATE cuota_anticipo set SALDO = (valor - $saldo)
        where ID_ZONA=? and ID_ANO=? and ID_MES=? ";
        $resultado = $this->query($sql, array($datos['zona'],$datos['ano'],$datos['mes']));
        return $resultado; 
    }

    public function calcularSaldo($datos)
    {
        $sql = "select coalesce(sum(valor),0) as valor
                from registro_gasto 
                where ID_ZONA=? and ID_ANO=? and ID_MES=? and APROBADO=? and ESTADO=? ";

        $resultado = $this->query($sql, array($datos['zona'],$datos['ano'],$datos['mes'],1,1));
        return $resultado[0]['valor'];
    }

    public function validaCuotaAutorizacion($datos){

        $sql="SELECT ID_CUOTA 
          FROM cuota_anticipo
          WHERE ID_MES=? and ID_ANO=?";
        $resData= $this->query($sql, array($datos['mes'],$datos['ano']));
          
        if ($resData) {
            return true;
        } else {
            return false;
        }
    }
    public function validaCuota($datos){

        $sql="SELECT ID_CUOTA 
          FROM cuota_anticipo
          WHERE ID_MES=? and ID_ANO=? AND ID_ZONA=?";
        $resData= $this->query($sql, array($datos['mes'],$datos['ano'],$datos['zona']));
          
        if ($resData) {
            return true;
        } else {
            return false;
        }
    }
        



}

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

   
    public function getCuotas()
    {   
        $res='';
        $sql = "
        SELECT c.id_cuota, z.nombre, c.saldo, c.valor 
        from cuota_anticipo c 
        join zonas z on c.id_zona=z.id_zona
        
        WHERE c.id_mes=? and id_ano=?";
        
        $resData= $this->query($sql,array(9,2021));

        for ($i=0; $i <sizeof($resData) ; $i++) { 

            $res.='
            <div class="cuota-zona">
            <div  class="d-flex flex-row">
            <div class="d-flex flex-column ms-3 mt-2">
              <span class="nombre-zona">'.$resData[$i]['nombre'].'</span>
              <b><span class="valor-anticipo">'.$resData[$i]['valor'].'</span></b>
              <span class="saldo-zona">'.$resData[$i]['saldo'].'</span>
            </div>
            <div class="icono-ciudad"><img src="/assets/images/monedas-icono.png" width="28px"></div>
          </div>
          </div>
            
            
            ';
        }
        return $res;
    
    }
    public function setCuotas($datos)
    {   

        $sql = "select id_cuota
                from cuota_anticipo 
                where ID_ZONA=? and ID_ANO=? and ID_MES=?";

        $resultado = $this->query($sql, array($datos['zona'],$datos['ano'],$datos['mes']));
        if($resultado){
            $this->actualizarCuota($datos,$resultado[0]['id_cuota']);

        }
        else{
            $this->insertarCuota($datos);
        }
    
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
        var_dump($resultado);
        return $resultado; 
    }

    public function calcularSaldo($datos)
    {
        $sql = "select coalesce(sum(valor),0) as valor
                from registro_gasto 
                where ID_ZONA=? and ID_ANO=? and ID_MES=? and APROBADO=? and ESTADO=? ";

        $resultado = $this->query($sql, array($datos['zona'],$datos['ano'],$datos['mes'],"SI",1));
        return $resultado[0]['valor'];
    }



}

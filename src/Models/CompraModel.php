<?php

namespace App\Models;
if(!isset($_SESSION))  
{ 
    session_start();
} 
use Psr\Container\ContainerInterface;
use App\Models\BaseModel;
use DateTime;
use App\Models\UsuarioModel;
use App\Models\CuotasModel;
use Psr\Http\Message\UploadedFileInterface;


class CompraModel extends BaseModel
{

   
    public function getCompra($datos)
    {   
        $data='';
        $res=array();
        $res['total_anticipo']=0;
        $res['total_gasto']=0; 
        $res['saldo']=0;
        $sql = "
        SELECT RG.ID_REGISTRO, Z.NOMBRE as ZONA, CA.SALDO, RG.VALOR,CONCAT(U.PRIMER_NOMBRE, ' ' , U.PRIMER_APELLIDO) as USUARIO,
        C.NOMBRE as CATEGORIA,S.NOMBRE as SUBCATEGORIA,RG.FECHA_CREACION AS FECHA_COMPRA,format(RG.VALOR,0)as VALOR,RG.IMAGEN,
        CASE WHEN RG.OBSERVACION IS NULL THEN 'Sin Observacion'  ELSE RG.OBSERVACION END as OBSERVACION, M.NOMBRE as MES, EXTRACT(DAY FROM RG.FECHA_CREACION) AS DIA,EXTRACT(YEAR FROM RG.FECHA_CREACION) AS ANO,
        RG.APROBADO,RG.OBSERVACION_APROBACION
        FROM registro_gasto rg 
        JOIN zonas z           on rg.id_zona=z.id_zona
        JOIN usuarios u        on u.id_usuario=rg.id_usuario
        JOIN categorias c      on c.id_categoria=rg.id_categoria
        JOIN subcategoria s    on s.id_subcategoria=rg.id_subcategoria
        JOIN meses m           on m.id_mes=rg.id_mes
        JOIN cuota_anticipo ca on ca.id_zona=rg.id_zona and ca.id_mes=rg.id_mes and ca.id_ano=rg.id_ano 
        WHERE RG.ESTADO=1
        ";

        $where=$this->getWhere($datos);
       
        $sql.=$where['where'];
        $resData= $this->query($sql,$where['array']);
        for ($i=0; $i <sizeof($resData) ; $i++) { 

            $data.='<tr>
            <td>
              <div class="row contenido-registro">
                <div class="col-md-4">
                  <div class="d-flex flex-row">
                    <div class="d-flex flex-row datos-usuario ps-4"> 
                      <p class=" monserrat-text pe-2 fs-5">'.$resData[$i]['DIA'].'</p>
                      <div class="d-flex flex-column"> 
                      <span class="monserrat-text pe-4 text-secondary">'.$resData[$i]['MES'].'</span>
                      <span class="ms-5 text-primary">2021</span>
                      </div>
                     
                    </div>
                    <div class="nombre-usuario d-flex flex-row">
                      <i class="bi bi-person-circle text-secondary fs-4 pb-5 me-2"></i>
                      <div class="d-flex flex-column">
                        <span class="fs-6 fw-bold">'.$resData[$i]['USUARIO'].'</span>
                        <span class="text-secondary">'.$resData[$i]['ZONA'].'</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 " >
                  <div class=" d-flex flex-column datos-gasto mt-3">
                    <span class="ms-2 mt-2 fs-6">'.$resData[$i]['SUBCATEGORIA'].'</span>
                    <div class="d-flex flex-row datos-destino">
                     
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="d-flex flex-row justify-content-end mt-2">
                    <p class="mt-3 ms-3 me-5">'.$resData[$i]['OBSERVACION'].'</p>
                    <div class="d-flex flex-column">
                      <h6>$'.$resData[$i]['VALOR'].'</h6>
                      <p><a href="#" style="text-decoration:none; color: black;">soporte</a> </p>';

                      if ($_SESSION['id_rol']==1) {

                          $data.='<input type="range" min="0" max="1" class="range" id="'.$resData[$i]['ID_REGISTRO'].'" value="'.$resData[$i]['APROBADO'].'" onchange="guardarAprobacion(event)"/>
                          <div id="popover_'.$resData[$i]['ID_REGISTRO'].'" style="width: 220px; height:150px; background-color: white; position: absolute; left:70%; box-shadow: 0 5px 10px 5px rgb(218, 216, 216); display:none; border-radius: 10px; border: 1px rgb(214, 213, 213) solid;" class="mt-5 " >
                          <p class="m-2"> Observacion</p>
                          <textarea class="form-control  m-2 me-2" id="exampleFormControlTextarea1" rows="3"   style="font-size:11px; width:200px;">'.$resData[$i]['OBSERVACION_APROBACION'].'</textarea>
                          <button type="button" id="button_'.$resData[$i]['ID_REGISTRO'].'" class="btn btn-primary btn-sm ms-2 mt-2" style="font-size:11px;" data-registro="'.$resData[$i]['ID_REGISTRO'].'" onclick="aceptarObservacion('.'button_'.$resData[$i]['ID_REGISTRO'].')"><img src="/assets/images/chulo.png" width="19px"></button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>';
                      }
        }
        
        $res['total_anticipo']=number_format($this->getTotalAnticipo($datos));
        $res['total_gasto']=number_format($this->getTotalAprobado($datos)); 
        $res['saldo']=number_format($this->getTotalAnticipo($datos) - $this->getTotalAprobado($datos));
        $res['datos']=$data;
       
        return $res;
    }
    public function setCompra($datos,$datafile)
    {   
      $user=new UsuarioModel();
      $cuotas=new CuotasModel();
      $dataSaldo=array();
      $dataUser= $user->getResumeUser();
      $extencion=$this->obtenerExtencion($datafile['file']);
      $fechaActual= new DateTime('NOW'); 
      $ruta="assets/soportes/";
      
      $nombre=$_SESSION['id_usuario']."_".$datos['mes']."_".$datos['ano'].".".$extencion;
      
      
      $sql = "
        INSERT into registro_gasto (ID_USUARIO,ID_CATEGORIA,ID_SUBCATEGORIA,ID_ZONA,PERIODO,ID_MES,ID_ANO,VALOR,IMAGEN,APROBADO,ESTADO,OBSERVACION,FECHA_CREACION,HORA_CREACION,FECHA_MODIFICACION,HORA_MODIFICACION) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);";

        $resultado = $this->query($sql, array($_SESSION['id_usuario'],$datos['categoria'],$datos['subcategoria'],$dataUser['id_zona'],$datos['mes'],$datos['mes'], $datos['ano'], $datos['valor'],$ruta.$nombre,1,1,$datos['observacion'],$fechaActual->format('y-m-d'),$fechaActual->format('H:m:s'),$fechaActual->format('y-m-d'),$fechaActual->format('H:m:s')));  

        $dataSaldo['zona']=$dataUser['id_zona'];
        $dataSaldo['ano']=$datos['ano'];
        $dataSaldo['mes']=$datos['mes'];
        $dataSaldo['valor']=$datos['valor'];

        $cuotas->insertSaldo($dataSaldo);
        $sucess=$this->moveUploadedFile($ruta,$datafile['file'],$nombre);
    }


    function moveUploadedFile(string $directory, UploadedFileInterface $uploadedFile,$file='')
    {
        if (!file_exists($directory)) {	
            mkdir($directory, 0755, true);
        }
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $file);
        return $file;
    }

    public function obtenerExtencion(UploadedFileInterface $uploadedFile){
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        return $extension;
    }
    public function getWhere($datos){
       
        
          $array=array($datos['mes'],$datos['ano']);
          $where=" AND rg.id_mes=? and rg.id_ano=?";
          if (strlen(trim($datos['usuario']))> 0) {
              $where.=" AND U.PRIMER_NOMBRE LIKE ? ";
              array_push($array, "".$datos['usuario']."%");
          }
          if (strlen(trim($datos['zona']))> 0) {
              $where.=" AND z.ID_ZONA=?";
              array_push($array, $datos['zona']);
          }
          if ($_SESSION['id_rol']==3) {
            $where.=" AND u.ID_USUARIO=?";
              array_push($array, $_SESSION['id_usuario']);
          }
     
      $consulta=array();
      $consulta['where']= $where;
      $consulta['array']= $array;
      return $consulta;
    }
  public function getTotalAprobado($datos){

    $sql="select sum(rg.valor)as valor 
          from registro_gasto rg
          join zonas z    on z.id_zona=rg.id_zona
          join usuarios u on u.id_usuario=rg.id_usuario 
          WHERE rg.aprobado=1 and rg.estado=1";

    $where=$this->getWhere($datos);
        
    $sql.=$where['where'];
    $resData= $this->query($sql,$where['array']);
    return $resData[0]['valor'];
  }

  public function getTotalAnticipo($datos){

    $sql="select SUM(VALOR) as valor
          FROM cuota_anticipo ca
          JOIN zonas z on z.id_zona= ca.id_zona 
          WHERE ca.id_zona=? and ca.id_ano=? and ca.id_mes=?";

    $resData= $this->query($sql,array($datos['zona'],$datos['ano'],$datos['mes']));

    return $resData[0]['valor'];
  }

  public function setAprobacion($datos){

    $sql="UPDATE registro_gasto set aprobado=?
          WHERE id_registro=?";
    $res=array();
    $this->query($sql,array($datos['aprobacion'],$datos['id_registro']));
    $cuotas=new CuotasModel();
    $cuotas->insertSaldo($datos);

    $res['total_anticipo']=number_format($this->getTotalAnticipo($datos));
    $res['total_gasto']=number_format($this->getTotalAprobado($datos)); 
    $res['saldo']=number_format($this->getTotalAnticipo($datos) - $this->getTotalAprobado($datos));

    return $res;

  }

  public function setObservacion($datos){

    $sql="UPDATE registro_gasto set observacion_aprobacion=?
          WHERE id_registro=?";

    $resData= $this->query($sql,array($datos['observacion_aprobacion'],$datos['id_registro']));
    return $resData;
    
  }
  
}

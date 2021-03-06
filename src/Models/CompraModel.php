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
use Psr\Http\Message\UploadedFileInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class CompraModel extends BaseModel
{

   
    public function getCompra($datos)
    {   
      try {
        
        $data='';
        $res=array();
        $res['total_anticipo']=0;
        $res['total_gasto']=0; 
        $res['saldo']=0;
        $resultadoquery=$this->resultadoQuery($datos);
        $data=$this->getData($resultadoquery['resultado_consulta']);
        $this->download($datos);
        
        $res['total_anticipo']=number_format($this->getTotalAnticipo($datos));
        $res['total_gasto']=number_format($this->getTotalAprobado($datos)); 
        $res['total_noaprobado']=number_format($this->getTotalNoaprobado($datos)); 
        $res['saldo']=number_format($this->getTotalAnticipo($datos) - $this->getTotalAprobado($datos));
        $res['datos']=$data;
        $res['limit']=number_format($resultadoquery['limit']);
        $res['count']=sizeof($resultadoquery['resultado_consulta']);
        
        
        return $res;

      } catch (\Throwable $th) {
        //throw $th;
      }
    }
    public function setCompra($datos,$datafile)
    {   
      $respuesta=array();
      $cuotas=new CuotasModel();
      $fechaActual= new DateTime('NOW');
      $ruta='';
      $nombre='';
      try {
           
          if (!$this->validaFechaCompra($datos)) {

              $respuesta['mensaje']='Se esta ingresando una compra en un periodo vencido';
              $respuesta['tipo_mensaje']='warning';
              $respuesta['alert']='Atencion';

          }
          else if(!$cuotas->validaCuota($datos)){

            $respuesta['mensaje']='El periodo no tiene anticipo registrado';
            $respuesta['tipo_mensaje']='warning';
            $respuesta['alert']='Atencion';

          }
          else {
            $dataSaldo=array();
            if($datafile){
              $extencion=$this->obtenerExtencion($datafile['file']);
              $ruta="assets/soportes/";
              $nombre=date('d')."-".date('H')."_".date('i')."_".date('s').".".$extencion;
              $this->moveUploadedFile($ruta, $datafile['file'], $nombre);
              $this->resize($ruta,$nombre);

            }
            
            $sql = "
              INSERT into registro_gasto (ID_USUARIO,ID_CATEGORIA,ID_SUBCATEGORIA,ID_ZONA,FECHA_GASTO,PERIODO,ID_MES,ID_ANO,CIUDAD_ORIGEN,CIUDAD_DESTINO,VALOR,IMAGEN,APROBADO,ESTADO,OBSERVACION,FECHA_CREACION,HORA_CREACION,FECHA_MODIFICACION,HORA_MODIFICACION) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);";

            $resultado = $this->query($sql, array($_SESSION['id_usuario'],$datos['categoria'],$datos['subcategoria'],$datos['zona'],$datos['fecha_gasto'],$datos['mes'],$datos['mes'], $datos['ano'],$datos['origen'],$datos['destino'], $datos['valor'],$ruta.$nombre,1,1,$datos['observacion'],$fechaActual->format('y-m-d'),$fechaActual->format('H:m:s'),$fechaActual->format('y-m-d'),$fechaActual->format('H:m:s')));

            $dataSaldo['zona']=$datos['zona'];
            $dataSaldo['ano']=$datos['ano'];
            $dataSaldo['mes']=$datos['mes'];
            $dataSaldo['valor']=$datos['valor'];
           
            $cuotas->insertSaldo($dataSaldo);
            $respuesta['mensaje']='La compra ha sido guardada';
            $respuesta['tipo_mensaje']='success';
            $respuesta['alert']='Registro exitoso';
            $respuesta['width']=$datos;
          }

      } catch (\Throwable $th) {
        $respuesta['mensaje']='Error al momento de guardar';
        $respuesta['tipo_mensaje']='warning';
        $respuesta['alert']='Algo anda mal';
      }
      return $respuesta;
    }


    function moveUploadedFile(string $directory, UploadedFileInterface $uploadedFile,$file='')
    { 
      
        if (!file_exists($directory)) {	
            mkdir($directory, 0755, true);
        }
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $file);
        $file=$uploadedFile->getSize();
        
        return $file;
    }

    function resize($ruta,$nombre)
    { 
      $fileIMG = $ruta.$nombre;
      if (file_exists($fileIMG)) {

        list($width, $height) = getimagesize($fileIMG);
        $porcentaje = 0.75;

          $newWidth = $width * $porcentaje;
          $newHeight = $height * $porcentaje;

          //Crea una nueva imagen
          $tmp = imagecreatetruecolor($newWidth, $newHeight);
          $original = imagecreatefromjpeg($fileIMG);
          
          imagecopyresized($tmp, $original, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
          // Con la sgte linea puedo almacenar la imagen en una ubicacion.
          imagejpeg($tmp, $fileIMG);
        
      }
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

  public function getTotalNoaprobado($datos){

    $sql="select sum(rg.valor)as valor 
          from registro_gasto rg
          join zonas z    on z.id_zona=rg.id_zona
          join usuarios u on u.id_usuario=rg.id_usuario 
          WHERE rg.aprobado=0 and rg.estado=1";

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

    $sql="UPDATE registro_gasto set aprobado=?, usuario_aprobacion=?
          WHERE id_registro=?";
    $res=array();
    $this->query($sql,array($datos['aprobacion'],$_SESSION['id_usuario'],$datos['id_registro']));
    $cuotas=new CuotasModel();
    $cuotas->insertSaldo($datos);
    (new Logs())->regLog(7, 'Se cambia el estado de aprobacion del registro :'.$datos['id_registro'].' por :'.$datos['aprobacion'].'','Registros');
    $res['total_anticipo']=number_format($this->getTotalAnticipo($datos));
    $res['total_gasto']=number_format($this->getTotalAprobado($datos)); 
    $res['total_noaprobado']=number_format($this->getTotalNoaprobado($datos)); 
    $res['saldo']=number_format($this->getTotalAnticipo($datos) - $this->getTotalAprobado($datos));

    return $res;

  }

  public function delete($datos){

      if (!$this->validaFechaCompra($datos)) {
          $res['mensaje']='El periodo se encuentra vencido';
          $res['tipo_mensaje']='warning';
          $res['alert']='Atencion';
      } 
      else {
        
        $sql="UPDATE registro_gasto set estado=?
            WHERE id_registro=?";
        $res=array();
        $this->query($sql, array(0,$datos['id_registro']));
        $cuotas=new CuotasModel();
        $cuotas->insertSaldo($datos);
        (new Logs())->regLog(6, 'Se cambia el estado de registro:'.$datos['id_registro'].' por : Eliminado','Registros');
        $res['total_anticipo']=number_format($this->getTotalAnticipo($datos));
        $res['total_gasto']=number_format($this->getTotalAprobado($datos));
        $res['total_noaprobado']=number_format($this->getTotalNoaprobado($datos));
        $res['saldo']=number_format($this->getTotalAnticipo($datos) - $this->getTotalAprobado($datos));
      }
    return $res;

  }

  public function setObservacion($datos){

    $sql="UPDATE registro_gasto set observacion_aprobacion=?
          WHERE id_registro=?";

    $resData= $this->query($sql,array($datos['observacion_aprobacion'],$datos['id_registro']));
    return $resData;
    
  }
  public function validaFechaCompra($datos){

    if($datos['mes'] >= date('m')){
       return true;
    }
    else{
      $sql="SELECT ID_AUTORIZACION 
      FROM autorizacion
      WHERE ID_MES=? and ID_ANO=? and FECHA_LIMITE >= current_date";
      $resData= $this->query($sql,array($datos['mes'],$datos['ano']));
      
      if($resData){
        return true;
      }
      else{
        return false;
      }

    }

  }

  public function resultadoQuery($datos){

    $offset=6;
    $resultado=array();
    $limit=($datos['limit'] -1) * $offset;
    $sql = "
    SELECT RG.ID_REGISTRO, Z.NOMBRE as ZONA, CA.SALDO, RG.VALOR,CONCAT(U.PRIMER_NOMBRE, ' ' , U.PRIMER_APELLIDO) as USUARIO,
    C.NOMBRE as CATEGORIA,S.NOMBRE as SUBCATEGORIA,RG.FECHA_GASTO AS FECHA_COMPRA,format(RG.VALOR,0)as VALOR,RG.IMAGEN,
    CASE WHEN RG.OBSERVACION IS NULL THEN 'Sin Observacion'  ELSE RG.OBSERVACION END as OBSERVACION, M.NOMBRE as MES, EXTRACT(DAY FROM RG.FECHA_GASTO) AS DIA,EXTRACT(YEAR FROM RG.FECHA_GASTO) AS ANO,
    RG.APROBADO,RG.OBSERVACION_APROBACION,RG.IMAGEN,RG.CIUDAD_ORIGEN,RG.CIUDAD_DESTINO
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
    $sql.=$where['where']; $sql.=" limit $limit, 6";
    $resData= $this->query($sql,$where['array']);
    $resultado['resultado_consulta']=$resData;
    $resultado['limit']=$datos['limit'];
    

    return $resultado;
  }

  public function download($datos){
    $ruta="assets/";
    $file="archivo.xlsx";
    $spreadsheet=new Spreadsheet();
   //$spreadsheet=\PhpOffice\PhpSpreadsheet\IOFactory::load('assets/archivo.xlsx');
     $sheet=$spreadsheet->getActiveSheet();
     $sheet->setCellValue('A1','FECHA');
     $sheet->setCellValue('B1','USUARIO');
     $sheet->setCellValue('C1','ZONA');
     $sheet->setCellValue('D1','CATEGORIA');
     $sheet->setCellValue('E1','SUBCATEGORIA');
     $sheet->setCellValue('F1','ORIGEN');
     $sheet->setCellValue('G1','DESTINO');
     $sheet->setCellValue('H1','VALOR');
     $sheet->setCellValue('I1','OBSERVACION');
     $sheet->setCellValue('J1','APROBADO');
     $sheet->setCellValue('K1','OBSERVACION_APROBACION');

     $resdata=$this->resultadoQuery($datos);
     $resdata=$resdata['resultado_consulta'];
    $fila=2;
     for ($i=0; $i <sizeof($resdata) ; $i++) {

      $sheet->setCellValue('A'.$fila,$resdata[$i]['FECHA_COMPRA']);
      $sheet->setCellValue('B'.$fila,$resdata[$i]['USUARIO']);
      $sheet->setCellValue('C'.$fila,$resdata[$i]['ZONA']);
      $sheet->setCellValue('D'.$fila,$resdata[$i]['CATEGORIA']);
      $sheet->setCellValue('E'.$fila,$resdata[$i]['SUBCATEGORIA']);
      $sheet->setCellValue('F'.$fila,$resdata[$i]['CIUDAD_ORIGEN']);
      $sheet->setCellValue('G'.$fila,$resdata[$i]['CIUDAD_DESTINO']);
      $sheet->setCellValue('H'.$fila,$resdata[$i]['VALOR']);
      $sheet->setCellValue('I'.$fila,$resdata[$i]['OBSERVACION']);
      $sheet->setCellValue('J'.$fila,$resdata[$i]['APROBADO']);
      $sheet->setCellValue('K'.$fila,$resdata[$i]['OBSERVACION_APROBACION']);
      $fila++;

     }  
      $writer=new Xlsx($spreadsheet);
      $file="archivo.xlsx";
      $writer->save($ruta.$file);
      return sizeof($resdata);
     
  }

  
  
  public function getData($resData){

    $data='';
    $disabled='';
    
    $disabled= $_SESSION['id_rol']==3 ? 'disabled' : '';
    $display= $_SESSION['id_rol']==1 ? "none" : "block";

    for ($i=0; $i <sizeof($resData) ; $i++) { 
      $data.='<tr>
                <td>
                  <div class="row contenido-registro" id="contenido_'.$resData[$i]['ID_REGISTRO'].'">
                    <div class="col-md-4">
                      <div class="d-flex flex-row">
                      <button type="button" class="button-eliminar  mt-2" data-registro="'.$resData[$i]['ID_REGISTRO'].'"  onclick="eliminar(event)" style="display: '.$display.'">
                      Eliminar</button>
                        <div class="d-flex flex-row datos-usuario ps-4"> 
                        <div class="d-flex flex-column"> 
                          <span class=" monserrat-text pe-2 fs-5">'.$resData[$i]['DIA'].'</span>
                          </div>
                          <div class="d-flex flex-column"> 
                          <span class="monserrat-text pe-4 text-secondary">'.$resData[$i]['MES'].'</span>
                          <span class="ms-5 text-dark fw-bolder">2021</span>
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
                      <span class="ms-2 mt-2 fs-6 fw-bold">'.$resData[$i]['CATEGORIA'].'</span>
                        <span class="ms-2  fs-6">'.$resData[$i]['SUBCATEGORIA'].'</span>
                        <div class="d-flex flex-row datos-destino">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="d-flex flex-row justify-content-end mt-2">
                        <p class="mt-2 ms-4 me-5">'.$resData[$i]['OBSERVACION'].'</p>
                        <div class="d-flex flex-column">
                          <h6>$'.$resData[$i]['VALOR'].'</h6>
                          <button type="button" class="boton-soporte" data-imagen="'.$resData[$i]['IMAGEN'].'" onclick="mostrarImagen(event)">soporte</button>
                          <div height:20px; width:20px; background-color:red;><input type="range" min="0" max="1" class="range" id="'.$resData[$i]['ID_REGISTRO'].'" value="'.$resData[$i]['APROBADO'].'" onchange="guardarAprobacion(event)" '.$disabled.'/>
                        </div>
                        <div id="popover_'.$resData[$i]['ID_REGISTRO'].'" style="width: 220px; height:150px;    background-color: white; position: absolute; left:70%; box-shadow: 0 5px 10px 5px rgb(218, 216, 216); display:none; border-radius: 10px; border: 1px rgb(214, 213, 213) solid;" class="mt-5 " >
                              <p class="m-2"> Observacion</p>
                              <textarea class="form-control  m-2 me-2" id="exampleFormControlTextarea1" rows="3"        style="font-size:11px; width:200px;" '.$disabled.'>'.$resData[$i]['OBSERVACION_APROBACION'].'
                            </textarea>
                            <button type="button" id="button_'.$resData[$i]['ID_REGISTRO'].'" class="btn btn-primary btn-sm   ms-2 mt-2" style="font-size:11px;" data-registro="'.$resData[$i]['ID_REGISTRO'].'" onclick="aceptarObservacion('.'button_'.$resData[$i]['ID_REGISTRO'].')">
                            <img src="/assets/images/chulo.png" width="19px">
                            </button>
                        </div>
                      </div>
                    </div>
                  </div>
                  </div>
                </td>
              </tr>';
      
    }
      return $data;
  }
  
}

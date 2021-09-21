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

   
    public function getCompra()
    {   
        $res='';
        $sql = "
        SELECT RG.ID_REGISTRO, Z.NOMBRE as ZONA, CA.SALDO, RG.VALOR,CONCAT(U.PRIMER_NOMBRE, ' ' , U.PRIMER_APELLIDO) as USUARIO,
        C.NOMBRE as CATEGORIA,S.NOMBRE as SUBCATEGORIA,RG.FECHA_CREACION AS FECHA_COMPRA,RG.VALOR,RG.IMAGEN,
        CASE WHEN RG.OBSERVACION IS NULL THEN 'Sin Observacion' end as OBSERVACION, M.NOMBRE as MES, EXTRACT(DAY FROM RG.FECHA_CREACION) AS DIA,EXTRACT(YEAR FROM RG.FECHA_CREACION) AS ANO
        FROM registro_gasto rg 
        JOIN zonas z           on rg.id_zona=z.id_zona
        JOIN usuarios u        on u.id_usuario=rg.id_usuario
        JOIN categorias c      on c.id_categoria=rg.id_categoria
        JOIN subcategoria s    on s.id_subcategoria=rg.id_subcategoria
        JOIN meses m           on m.id_mes=rg.id_mes
        JOIN cuota_anticipo ca on ca.id_zona=rg.id_zona and ca.id_mes=rg.id_mes and ca.id_ano=rg.id_ano
        
        WHERE rg.id_mes=? and rg.id_ano=?";
        
        $resData= $this->query($sql,array(9,2021));

        for ($i=0; $i <sizeof($resData) ; $i++) { 

           /*  $res.=' <tr>
                    <th scope="row"><div class="d-flex flex-row"><i class="bi bi-person-circle text-secondary me-2"></i>'.$resData[$i]['USUARIO'].'</div></th>
                    <td>'.$resData[$i]['ZONA'].'</td>
                    <td>'.$resData[$i]['CATEGORIA'].'</td>
                    <td>'.$resData[$i]['SUBCATEGORIA'].'</td>
                    <td>'.$resData[$i]['FECHA_COMPRA'].'</td>
                    <td>origen</td>
                    <td>destino</td>
                    <td>'.$resData[$i]['VALOR'].'</td>
                    <td><a href="'.$resData[$i]['IMAGEN'].'">Link</a></td>
                    <td>'.$resData[$i]['OBSERVACION'].'</td>
                    <td>aprobacion</td>
                    <td class="d-flex flex-row flex-wrap"><input type="text" class="form-control ms-5" id="observacion_aprobacion"></td>
                </tr>'; */


            $res.='<tr>
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
                      <p class=""><b> origen:</b> Cali</p>
                      <p class="ms-2"><b> Destino:</b> Cali</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="d-flex flex-row justify-content-end mt-2">
                    <p class="mt-3 ms-3 me-5">'.$resData[$i]['OBSERVACION'].'</p>
                    <div class="d-flex flex-column">
                      <h6>$'.$resData[$i]['VALOR'].'</h6>
                      <p><a href="#">Link soporte</a> </p>
                    </div>
                  </div>
                </div>
              </div>
            </td>
          </tr>';


        }

       
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
          INSERT into registro_gasto (ID_USUARIO,ID_CATEGORIA,ID_SUBCATEGORIA,ID_ZONA,PERIODO,ID_MES,ID_ANO,VALOR,IMAGEN,APROBADO,ESTADO,FECHA_CREACION,HORA_CREACION,FECHA_MODIFICACION,HORA_MODIFICACION) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);";

          $resultado = $this->query($sql, array($_SESSION['id_usuario'],$datos['categoria'],$datos['subcategoria'],$dataUser['id_zona'],$datos['mes'],$datos['mes'],$datos['ano'],$datos['valor'],$ruta.$nombre,'SI',1,$fechaActual->format('y-m-d'),$fechaActual->format('H:m:s'),$fechaActual->format('y-m-d'),$fechaActual->format('H:m:s')));  

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
   
}

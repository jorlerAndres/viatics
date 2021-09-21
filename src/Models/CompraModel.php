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
        $sql = "
        SELECT RG.ID_REGISTRO, Z.NOMBRE, C.SALDO, RG.VALOR,CONCAT(U.PRIMER_NOMBRE, ' ' , U.PRIMER_APELLIDO) as USUARIO,
        C.NOMBRE as CATEGORIA,S.NOMBRE as SUBCATEGORIA,RG.FECHA_CREACION AS FECHA_COMPRA,RG.VALOR,RG.IMAGEN,RG.OBSERVACION
        FROM registro_gasto rg 
        JOIN zonas z           on rg.id_zona=z.id_zona
        JOIN usuarios u        on u.id_usuario=rg.id_usuario
        JOIN categorias c      on c.id_categoria=rg.id_categoria
        JOIN subcategoria s    on s.id_subcategoria=rg.id_subcategoria
        JOIN cuota_anticipo ca on ca.id_zona=rg.id_zona and ca.id_mes=rg.id_mes and ca.id_ano=rg.id_ano
        
        WHERE rg.id_mes=? and rg.id_ano=?";
        
        $resData= $this->query($sql,array(9,2021));
        return $resData;
    
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

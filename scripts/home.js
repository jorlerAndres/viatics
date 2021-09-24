var host = window.location.origin;
const div_zonas=document.getElementById("div_zonas");
const div_categorias=document.getElementById("categorias");
const div_subcategorias=document.getElementById("subcategorias");
var zonabusqueda=document.getElementById('zona_busqueda');
var usuariobusqueda=document.getElementById('usuario_busqueda');
var formularioUsuario=document.getElementById('formulario_usuario');
var fecharegistro=document.getElementById('fecha_registro');

var zona='';
var mes='';
var categoria='';
var subcategoria='';

document.addEventListener("DOMContentLoaded", function () {
  
  
  
  for (let i = 0; i < div_zonas.children.length; i++) {
       var div_zonaprueba=div_zonas.children[i].lastElementChild.firstElementChild;
       console.log(div_zonaprueba);
       div_zonaprueba.style.backgroundImage=`url('${div_zonaprueba.getAttribute("data-imagen")}')`; 
  }

 
  for (let i = 0; i < div_categorias.children.length; i++) {
      var div_categoria=div_categorias.children[i].firstElementChild.firstElementChild;
      div_categoria.setAttribute("src", div_categoria.getAttribute("data-imagen"));
  }

  for (let i = 0; i < div_subcategorias.children.length; i++) {
      var div_subcategoria=div_subcategorias.children[i].firstElementChild;
      div_subcategoria.setAttribute("src", div_subcategoria.getAttribute("data-imagen"));
  }
  ponerMesActual();
  cargarzonas();
})

function ponerMesActual(){
  var mesActual=new Date().getMonth()+1;
  if(mesActual< 10){
    mesActual='0'+ mesActual
  }
  fecharegistro.value=new Date().getFullYear() + "-"+mesActual;
}

function seleccionMes(e)
{
  var div_mes=document.querySelectorAll(".div_mes");

  div_mes.forEach(element => {

      if(element.style.boxShadow){
        element.style.removeProperty('box-shadow');
      }
      if(element.firstElementChild.style.color=="blue"){
        element.firstElementChild.style.color="#88888a"
      }
  
  }); 
  mes=document.getElementById(e);
  mes.style.boxShadow="0 0 12px rgb(214, 216, 216)";
  mes.firstElementChild.style.color="blue";
  console.log(mes);
}

function seleccionZona(e)
{
  var div_zonaprueba=document.querySelectorAll(".div_zonaprueba");
  div_zonaprueba.forEach(element => {

      if(element.style.boxShadow){
        element.style.removeProperty('box-shadow');
      }
   
  }); 
  zona=document.getElementById(e);
  zona.style.boxShadow="1px 1px 50px 20px rgb(49, 49, 49) inset";

}

function seleccionCategoria(e)
{
  var cuadrocategoria=document.querySelectorAll(".cuadro-categoria");
  cuadrocategoria.forEach(element => {

      if(element.style.borderColor){
        element.style.removeProperty('border-color');
        element.style.boxShadow="1px 1px 5px 6px rgb(247, 246, 246) inset";
      }
   
  }); 
  categoria=document.getElementById(e);
  categoria.style.borderColor="rgba(35, 35, 248, 0.308)";
  categoria.style.boxShadow="0px 0px 0px 0px rgb(250, 249, 249)";

}

function SeleccionSubcategoria(e)
{
  var itemsubcategoria=document.querySelectorAll(".item-subcategoria");
  itemsubcategoria.forEach(element => {
    
      if(element.style.boxShadow){
        element.style.removeProperty('box-shadow');
      }
   
  }); 
  subcategoria=document.getElementById(e);
  subcategoria.style.boxShadow="-1px 1px 7px 0px rgb(17, 17, 17)";
  

}


function guardarAnticipo(e){
  console.log("zona:"+zona.id);
  var formData = new FormData();
  formData.append("zona", zona.id); 
  formData.append("mes", mes.id); 
  formData.append("valor", document.getElementById('valor_cargarcuotas').value); 
  formData.append("ano", new Date().getFullYear()); 
   fetch(host+'/api/cuotas/set',{
      
    method: "POST",
    body: formData,
  })
  .then(response => response.json())
  .then(data => {

  })
  .catch(function(error) {
    return error;
  }) 
  swal('Registro Exitoso','el anticipo ha sido adicionado','success');
}

function guardarCompra(){
  var periodo = document.getElementById('periodo_gasto').value.split('-',);

  var fileUP=document.getElementById('file_soporte')
  var formData = new FormData();
  formData.append("categoria", categoria.id); 
  formData.append("subcategoria", subcategoria.id); 
  formData.append("valor", document.getElementById('valor_gasto').value); 
  formData.append("observacion", document.getElementById('observacion_registros').value);
  formData.append("periodo", document.getElementById('periodo_gasto').value); 
  formData.append("mes", periodo[1])
  formData.append("ano", periodo[0]);
  formData.append("file", fileUP.files[0]);
  formData.append("nombre_archivo", fileUP.files[0].name); 
   fetch(host+'/api/compra/set',{
      
    method: "POST",
    body: formData,
  })
  .then(response => response.json())
  .then(data => {
   

  })
  .catch(function(error) {
    return error;
  }) 
  swal('Registro Exitoso','La Compra ha sido adicionada','success'); 
}
function cargarzonas(){

  fetch(host+'/api/cuotas/get',{
      
    method: "POST",
   
  })
  .then(response => response.json())
  .then(data => {
    contenidozonas.innerHTML=data;

  })
  .catch(function(error) {
    return error;
  }) 
}

zonabusqueda.addEventListener("change", function () {
  cargartabla();

})
usuariobusqueda.addEventListener("keyup", function () {
  cargartabla();

})

 async function cargartabla(){
  var tbody=document.getElementById('tbody');
  var periodo = document.getElementById('fecha_registro').value.split('-',);
  var formData = new FormData();
  formData.append("zona",zonabusqueda.value); 
  formData.append("usuario",usuariobusqueda.value); 
  formData.append("ano",periodo[0]); 
  formData.append("mes",periodo[1]); 
  await fetch(host+'/api/compra/get',{
      
    method: "POST",
    body: formData,
  })
  .then(response => response.json())
  .then(data => {
    tbody.innerHTML=data['datos'];
    console.log(data['total_anticipo']);
    var anticipoglobal=document.getElementById('anticipo_global');
    anticipoglobal.innerText=data['total_anticipo'];

    var gastoaprobado=document.getElementById('gasto_aprobado');
    gastoaprobado.innerText=data['total_gasto'];

    var saldo=document.getElementById('saldo');
    saldo.innerText=data['saldo'];
  })
  .catch(function(error) {
    return error;
  }) 
 
}

formulario_busquedausuario.onsubmit = async (e) => {
  e.preventDefault();

  let response = await fetch(host+'/api/usuario/get', {
    method: 'POST',
    body: new FormData(formulario_busquedausuario)
  });

  let result = await response.json();
  
  console.log(result['id_usuario']);
  document.getElementById('id_usuario').value=result['ID_USUARIO'];
  document.getElementById('nombres_usuario').value=result['PRIMER_NOMBRE']+" "+result['SEGUNDO_NOMBRE'];
  document.getElementById('apellidos_usuario').value=result['PRIMER_APELLIDO']+" "+result['SEGUNDO_APELLIDO'];
  document.getElementById('cedula_usuario').value=result['CEDULA'];
  document.getElementById('mail_usuario').value=result['EMAIL'];
  document.getElementById('telefono_usuario').value=result['TELEFONO'];
  document.getElementById('zona_usuario').value=result['ID_ZONA'];
  document.getElementById('vehiculo_usuario').value=result['VEHICULO'];
  document.getElementById('tarjeta_usuario').value=result['NUMERO_TARJETA_VIATICO'];
  document.getElementById('contrasena_usuario').value=result['PASSWORD'];
  document.getElementById('rol_usuario').value=result['ID_ROL'];
};

formularioUsuario.onsubmit = async (e) => {
  e.preventDefault();

  let response = await fetch(host+'/api/usuario/set', {
    method: 'POST',
    body: new FormData(formularioUsuario)
  });

  let result = await response.json();

  //alert(result.message);
};
function guardarAprobacion(e){

  var formData = new FormData();
  var periodo = document.getElementById('fecha_registro').value.split('-',);
  var formData = new FormData();
  formData.append("zona",zonabusqueda.value); 
  formData.append("usuario",usuariobusqueda.value); 
  formData.append("ano",periodo[0]); 
  formData.append("mes",periodo[1]);
  formData.append("id_registro",e.getAttribute("data-registro"));

   fetch(host+'/api/compra/aprobacion',{
      
    method: "POST",
    body: formData,
  })
  .then(response => response.json())
  .then(data => {

  })
  .catch(function(error) {
    return error;
  }) 
  
}
var host = window.location.origin;



const btnEnviarFormulario=document.getElementById('btnEnviarFormulario');
btnEnviarFormulario.addEventListener('click',e=>{
    const userForm=document.getElementById('userForm');
    const passwordForm=document.getElementById('passwordForm');
    validar(userForm,passwordForm);
});

async function validar(userForm,passwordForm){  
    let user=userForm.value
    let contrasena=passwordForm.value
    try {
         const formData = new FormData();
         formData.append('user',user);
         formData.append('contrasena',contrasena);
         const res = await fetch(host+'/',
          {
            method: 'POST',
            body: formData,
          });
        const data = await res.json();
        console.log(data);
        const {id}=data;
        if(id){
            location.href =host+"/home"
            
        }else{
          mostrarAlert();
        } 
    } catch (error) {
        console.log(error)
    }
}

function mostrarAlert(data){ 
  swal('Ingreso','Por favor Revise las credenciales','warning');

}
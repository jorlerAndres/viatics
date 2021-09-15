const misTareasTab=document.getElementById("misTareas-tab");
const overCanvas=document.getElementById("overCanvas");
const listMenusidenav=document.getElementById("list-menusidenav")
const itemplanes=document.getElementById("itemplanes")

const host = window.location.origin;

  var listmenu= document.querySelectorAll('.nav-item');
  listmenu.forEach(element => {
    
    element.addEventListener("click", function (e) {
        object_i = element.firstElementChild.firstElementChild;
        object_text = element.firstElementChild.lastElementChild;
      if( object_i.classList.contains('text-primary')){
        object_i.classList.remove("text-primary");
        
      }
      else{
        object_i.classList.add("text-primary");
        object_text.style.color="blue";
      } 
     
      
    })
  
}); 

  async function fetchCargarTarea(e){

      var formData = new FormData();
      
      console.log(host+'/api/tareas');
       formData.append("plan", e.getAttribute("data-plan")); 
       await fetch(host+'/api/tareas',{
    
        method: "POST",
        body: formData,
      })
      .then(response => response.json())
      .then(data => {
        div_contenido.innerHTML=data;
        rotateContent();
        return data;
      })
    
      .catch(function(error) {
        return error;
      }) 
    }




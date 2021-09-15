var host = window.location.origin;
const consultarDecoracion = async (codigo) => {
    try {
      console.log(codigo);
      const res = await fetch(host+'/api/decoracion/all');
      const data = await res.json();
      const mp = await data.find(Decoracion => Decoracion.CODIGO == codigo);
      console.log(data);
      return mp;
    } catch (error) {
      console.log(error)
    }
}

const consultarDecoraciones = async () => {
  try {
    console.log(referencia);
    const res = await fetch(host+'/api/decoracion/all');
    const data = await res.json();
    console.log(data);
    return data;
  } catch (error) {
    console.log(error)
  }
}

const consultarListaDecoracion = async () => {
  try {
    //console.log(referencia);
    const res = await fetch(host+'/api/decoracion/all');
    const data = await res.json();
    const lista={};
    data.forEach(Decoracion => {
      lista[new String(Decoracion.CODIGO)]=null; 
    });
    //const mp = await data.find(Decoracion => Decoracion.ReferenciaProimpo == referencia);
    console.log(lista);
    return lista;
  } catch (error) {
    console.log(error)
  }
}
  
function dibujandoEnModal(deco){
    let html=`
    <b><p>Codigo:</b> ${deco.CODIGO}<br> </p>
    <b>Descripción:</b> ${deco.DESCRIPCION}<br>
    <b>Valor:</b> ${deco.VALOR}<br>`
  return html;
}

export {consultarDecoracion,consultarListaDecoracion,consultarDecoraciones, dibujandoEnModal}


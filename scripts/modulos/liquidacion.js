var host = window.location.origin;

let totalCostosLiquidacion = new Array();
totalCostosLiquidacion['flete']=[0,0,0,0,0]
let totalPrecioLiquidacion = new Array();
totalPrecioLiquidacion['costoEscala']=[0,0,0,0,0]
totalPrecioLiquidacion['precioVentaEscala']=[0,0,0,0,0]
totalPrecioLiquidacion['precioVentaTotal']=[0,0,0,0,0]

async function liquidador(origen, itemsLiquidacion) {
  console.log('itemsLiquidacion: ', itemsLiquidacion);
  //Guardamos Los items de la liquidacion
  localStorage.setItem('itemsliquidacion', JSON.stringify(itemsLiquidacion));
  //Ubicamos segun el item a quien vamos a liquidar
  switch (origen) {
    case "materiaPrima":
      return materiasPrimas(itemsLiquidacion)
      break;
    case "manoObra":
      return manodeObra(itemsLiquidacion)
      break;
    case "empaqueMaquila":
      return empaqueMaquila(itemsLiquidacion)
      break;
    case "decoracion":
      return decoracion(itemsLiquidacion)
      break;
    case "flete":
      return flete(itemsLiquidacion)
      break;
      case "margen":
        return margen(itemsLiquidacion)
        break;
    case "moldeMaquinaria":
      return moldesMaquinaria(itemsLiquidacion)
      break;
    default:
      break;
  }
}

async function materiasPrimas(itemsLiquidacion) {
  console.log(itemsLiquidacion);
  let cantidadProducir = itemsLiquidacion.cantidadProducir;
  let mp = itemsLiquidacion.materiaPrima;
  let asociados = [];
  let id = 0;
  let detalle = "Ninguno";
  const itemLiquidacion = await mp.map(async function (item) {
    let indexAnterior = 0;
    let referenciaMP = item.mp.REFERENCIAPROIMPO;
    let referenciaST = "";
    let consumoAsociado = 0;
    try {
      if (item.st == "NA") {
        if (item.mp.TIPO != "Insumo") {
          indexAnterior = mp.lastIndexOf(item) - 1;
          console.log("ST Anterior", mp[indexAnterior]["st"]);
          if (mp[indexAnterior]["st"] == "NA") {
            detalle = "Rechazado";
          }
          consumoAsociado =
            (parseFloat(mp[indexAnterior]["st"].PESOPIEZA) +
              parseFloat(mp[indexAnterior]["st"].PESOPIEZA) *
              parseFloat(mp[indexAnterior]["mp"].DESPERDICIO)) /
            1000;
          console.log("MP Anterior", mp[indexAnterior]);
          asociados.push(indexAnterior);
        }
      } else {
        referenciaST = item.st.REFERENCIASEMITERMINADOS;
      }
      id++;
      const formData = new FormData();
      formData.append("Cantidad", cantidadProducir);
      formData.append("ReferenciaMP", referenciaMP);
      formData.append("ReferenciaST", referenciaST);
      formData.append("ultimoConsumoU", consumoAsociado);
      formData.append("origen", "materiaPrima");
      const res = await fetch(host+"/api/liquidacion", {
        method: "POST",
        body: formData,
      });
      return await res.json();
    } catch (error) {
      detalle = "Rechazado";
      console.log(error, id);
      id -= 1;
    }
  });
  Promise.all(itemLiquidacion).then((data) =>
    dibujarItemsLiquidados(data, "MateriasPrimas")
  );
  return {
    asociados: asociados,
    subTotal: 0,
    costoTotalItems: 0,
    mensaje: "ok",
    detalle: detalle,
    id: id,
  };
}
async function manodeObra(itemsLiquidacion) {
  let cantidadProducir = itemsLiquidacion.cantidadProducir;
  let mo = itemsLiquidacion.manodeObra;
  const itemLiquidacion = await mo.map(async function (item) {
    console.log(item);
    let referenciaST = item.st.REFERENCIASEMITERMINADOS;
    let cantidadST = item.cantMO;
    let id = 0;
    try {
      const formData = new FormData();
      formData.append("Cantidad", cantidadProducir);
      formData.append("ReferenciaST", referenciaST);
      formData.append("CantidadST", cantidadST);
      formData.append("origen", "manoObra");
      const res = await fetch(host+"/api/liquidacion", {
        method: "POST",
        body: formData,
      });
      return await res.json();
    } catch (error) {
      console.log(error);
      id -= 1;
    }
  });
  Promise.all(itemLiquidacion).then((data) =>
    dibujarItemsLiquidados(data, "ManodeObra")
  );
  return {
    subTotal: 0,
    costoTotalItems: 0,
    mensaje: "ok",
    id: 0,
  };
}
async function empaqueMaquila(itemsLiquidacion) {
  console.log(itemsLiquidacion);
  let cantidadProducir = itemsLiquidacion.cantidadProducir;
  let unidadEmpaque = itemsLiquidacion.unidadEmpaque;
  let em = itemsLiquidacion.empaqueMaquila;
  const itemLiquidacion = await em.map(async function (item) {
    console.log(item);
    let referenciaMP = item.em.REFERENCIAPROIMPO;
    let cantidadMP = item.cantEM;
    console.log(cantidadMP);
    let id = 0;
    try {
      const formData = new FormData();
      formData.append("Cantidad", cantidadProducir);
      formData.append("CantidadMP", cantidadMP);
      if (referenciaMP == "Otro") {
        formData.append("ReferenciaMP", "Otro");
        formData.append("Descripcion", item.em.Nombre);
        formData.append("ValorUnitario", item.em.PrecioUnitario);
        formData.append("origen", "Otro");
      } else {
        formData.append("UnidadEmpaque", unidadEmpaque);
        formData.append("ReferenciaMP", referenciaMP);
        formData.append("origen", "empaqueMaquila");
      }
      const res = await fetch(host+"/api/liquidacion", {
        method: "POST",
        body: formData,
      });
      return await res.json();
    } catch (error) {
      console.log(error);
      id -= 1;
    }
  });
  Promise.all(itemLiquidacion).then((data) =>
    dibujarItemsLiquidados(data, "EmpaqueMaquila")
  );
  return {
    subTotal: 0,
    costoTotalItems: 0,
    mensaje: "ok",
    id: 0,
  };
}

async function decoracion(itemsLiquidacion) {
  console.log(itemsLiquidacion);
  let cantidadProducir = itemsLiquidacion.cantidadProducir;
  let deco = itemsLiquidacion.decoracion;
  let id = 0;
  let detalle = "Ninguno";
  const itemLiquidacion = await deco.map(async function (item) {
    console.log(item);
    let referenciaDeco = item.deco.CODIGO;
    let cantidadDeco = item.cantDeco;
    try {
      id++;
      const formData = new FormData();
      formData.append("Cantidad", cantidadProducir);
      formData.append("ReferenciaDeco", referenciaDeco);
      formData.append("CantidadDeco", cantidadDeco);
      formData.append("origen", "decoracion");
      const res = await fetch(host+"/api/liquidacion", {
        method: "POST",
        body: formData,
      });
      return await res.json();
    } catch (error) {
      detalle = "Rechazado";
      console.log(error, id);
      id -= 1;
    }
  });
  Promise.all(itemLiquidacion).then((data) =>
    dibujarItemsLiquidados(data, "Decoracion")
  );
  return {
    subTotal: 0,
    costoTotalItems: 0,
    mensaje: "ok",
    detalle: detalle,
    id: id,
  };
}

async function moldesMaquinaria(itemsLiquidacion) {
  let cantidadProducir = itemsLiquidacion.cantidadProducir;
  let mm = itemsLiquidacion.moldesMaquinaria;
  let id = 0;
  const itemLiquidacion = await mm.map(async function (item) {
    console.log(item);
    let ReferenciaMP = item.mm.REFERENCIAPROIMPO;
    let cantidadMP = item.cantMP;
    try {
      id++
      const formData = new FormData();
      formData.append("Cantidad", cantidadProducir);
      formData.append("ReferenciaMP", ReferenciaMP);
      formData.append("CantidadMP", cantidadMP);
      formData.append("origen", "moldes");
      const res = await fetch(host+"/api/liquidacion", {
        method: "POST",
        body: formData,
      });
      return await res.json();
    } catch (error) {
      console.log(error);
      id -= 1;
    }
  });
  Promise.all(itemLiquidacion).then((data) =>
    dibujarItemsLiquidados(data, "MoldesMaquinaria")
  );
  return {
    subTotal: 0,
    costoTotalItems: 0,
    mensaje: "ok",
    id: 0,
  };
}

async function margen(itemsLiquidacion) {
  let margenes = itemsLiquidacion.margen;
  totalPrecioLiquidacion['margenes']=Object.values(margenes);
  calcularPrecioVenta();
}

async function calcularPrecioVenta() {
  const precioComercial=document.getElementById('precioComercial')
  const itemPrecioComercial = precioComercial.getElementsByTagName('td')
  const valorTotalVenta=document.getElementById('valorTotalVenta')
  const itemValorTotalVenta = valorTotalVenta.getElementsByTagName('td')
  console.log(totalPrecioLiquidacion);
  for (let index = 0; index < 5; index++) {
    console.log(totalPrecioLiquidacion['costoEscala'][index]);
    console.log(totalPrecioLiquidacion['margenes']);
    console.log(totalPrecioLiquidacion['margenes'][0]);
    let costoEsc=totalPrecioLiquidacion['costoEscala'][index];
    let margen=Number(totalPrecioLiquidacion['margenes'][0][index])/100
    console.log(margen);
    //Calculamos el precio de venta por escala.
    totalPrecioLiquidacion['precioVentaEscala'][index]=costoEsc/(1-margen)
    itemPrecioComercial[index+1].innerText=formatoMoneda(totalPrecioLiquidacion['precioVentaEscala'][index])
    //Calculamos el precio de venta total
    console.log(totalPrecioLiquidacion['escalas'][index])
    let esc=Number(totalPrecioLiquidacion['escalas'][index])
    console.log(esc)
    totalPrecioLiquidacion['precioVentaTotal'][index]=esc*(costoEsc/(1-margen))
    itemValorTotalVenta[index+1].innerText=formatoMoneda(totalPrecioLiquidacion['precioVentaTotal'][index])
  }

} 

async function flete(itemsLiquidacion) {
  console.log("Flete===>");
  let flete = itemsLiquidacion.flete;
  let escalas = flete.escalas;
  totalPrecioLiquidacion['escalas']=escalas;
  try {
    const formData = new FormData();
    formData.append("Escalas", escalas);
    formData.append("ReferenciaP", "CA-PR-139TP.BL");
    formData.append("ReferenciaMP", 'Grande');
    formData.append("Destino", 10);
    formData.append("ValorDeclarado", 1750);
    formData.append("origen", "flete");
    const res = await fetch(host+"/api/liquidacion", {
      method: "POST",
      body: formData,
    });
    let data = await res.json();
    console.log("Flete===>");
    console.log(data);
    dibujarFletes(data);
  } catch (error) {
    console.log(error);
  }

  return {
    subTotal: 0,
    costoTotalItems: 0,
    mensaje: "ok",
    id: 0,
  };
}

/**
 *Esta funcion muestra la liquidacion en la vista.
 *Primero, elimina las liquidaciones que presentaron error y no se liquidaron. Ocurre solo en materia prima.
 *Sengundo, limpia la tabla de liquidacion. items${origen}
 *Tercero, recorre las liquidaciones o filas añadiendo el boton remover y el contenido que le corresponde.
 * @param {listado de liquidaciones} listFilas
 * @param {determina para que area va la liquidacion, ej: Materia Prima o decoracion} origen
 *
 */

function dibujarItemsLiquidados(listFilas, origen) {
  let filas = listFilas.filter((f) => f != undefined);
  let id = 0;
  let subTotalUnidad = 0;
  let subTotalConsumoEstimado = 0;
  let subTotalCosto = 0;
  //Limpiamos tabla.
  const itemsMateriasPrimas = document.getElementById(`items${origen}`);
  itemsMateriasPrimas.innerHTML = "";
  //Creamos los items para mostrar.
  filas.forEach((data) => {
    var fila = document.createElement("tr");
    var celdaAccion = document.createElement("td");
    var a = document.createElement("a");
    a.setAttribute(
      "class",
      "btn-floating btn-small waves-effect waves-light red"
    );
    a.setAttribute("data", origen);
    var i = document.createElement("i");
    i.setAttribute("class", "material-icons");
    i.setAttribute("data", origen);
    i.setAttribute("id", id);
    var textI = document.createTextNode("remove");
    i.appendChild(textI);
    a.appendChild(i);
    celdaAccion.appendChild(a);
    fila.appendChild(celdaAccion);
    console.log(data);
    subTotalUnidad += data.TotalUnidad;
    subTotalConsumoEstimado += data.ConsumoEstimado;
    subTotalCosto += data.CostoTotal;
    for (const property in data) {
      console.log(`${property}: ${data[property]}`);
      var celda = document.createElement("td");
      if (
        property == "ReferenciaMP" ||
        property == "ReferenciaST" ||
        property == "Descripcion" ||
        property == "DescripcionST" ||
        property == "DescripcionMP"
      ) {
        var contenidoCelda = document.createTextNode(data[property]);
      } else {
        var numero = 0;
        if (property == "ConsumoUnitario") {
          numero = Intl.NumberFormat("es-CO", {
            maximumSignificantDigits: 3,
          }).format(data[property]);
        } else {
          numero = formatoMoneda(data[property]);
        }
        var contenidoCelda = document.createTextNode(numero);
      }
      celda.appendChild(contenidoCelda);
      fila.appendChild(celda);
    }
    itemsMateriasPrimas.appendChild(fila);
    id += 1;
  });
  //Modificamos las celdas que contendras los subTotales
  const totalesMateriasPrimas = document.getElementById(`totales${origen}`);
  const subTotales = totalesMateriasPrimas.getElementsByTagName("td");

  let htmlTotalUnidad = subTotales[1];
  let htmlTotalConsumoEstimado = subTotales[2];
  let htmlTotalCosto = subTotales[3];

  try {
    htmlTotalUnidad.innerText = formatoMoneda(subTotalUnidad);
    htmlTotalConsumoEstimado.innerText = formatoMoneda(subTotalConsumoEstimado);
    htmlTotalCosto.innerText = formatoMoneda(subTotalCosto);
  } catch (error) {
    console.log(error)
  }
  actualizarCostosLiquidacion(origen, subTotalUnidad, subTotalCosto)
  liquidarCostos();
}

function dibujarFletes(liquidacionFlete) {
  const costosTotal = document.getElementById('costosFlete')
  const itemCostosTotal = costosTotal.getElementsByTagName('td')
  let datosEscalas=Object.values(liquidacionFlete);
  let valorFleteMinimo=0;
  let costosFlete=new Array();
  for (let index = 0; index < 5; index++) {
    valorFleteMinimo=datosEscalas[index].FleteMinimo
    if(!isNaN(valorFleteMinimo)){
      itemCostosTotal[index+1].innerText = formatoMoneda(valorFleteMinimo);
    }else{
      itemCostosTotal[index+1].innerText="N/A"
      valorFleteMinimo=0;
    }
    costosFlete[index]=valorFleteMinimo;
  }
  totalCostosLiquidacion['flete']=costosFlete
  liquidarCostos();
  console.log(totalCostosLiquidacion);
}

function actualizarCostosLiquidacion(origen, subTotalUnidad, subTotalCosto) {
  //Se debe ajustar para que realice el calculo en todas las escalas
  totalCostosLiquidacion[origen.toString()] = { "subTotal": subTotalUnidad, "totalCosto": subTotalCosto }
  let idFila = 'costos' + origen;
  let filaCosto = document.getElementById(idFila)
  let itemsFilaCosto = filaCosto.getElementsByTagName('td')
  for (let index = 1; index < itemsFilaCosto.length; index++) {
    itemsFilaCosto[index].innerText = formatoMoneda(subTotalUnidad);
  }
}


//Para esta liquidacion se van a tener en cuenta las escalas
function liquidarCostos() {
  let liquidacionSubTotales = 0;
  let costosTotales=[];
  Object.keys(totalCostosLiquidacion).forEach(item => {
    if(item!="flete"){
      liquidacionSubTotales += totalCostosLiquidacion[item].subTotal
    }
  })
  const itemsCostoTotal = document.getElementById('costosTotal').getElementsByTagName('td')
  
  for (let index = 0; index < 5; index++) {
    totalPrecioLiquidacion['costoEscala'][index]=liquidacionSubTotales+totalCostosLiquidacion['flete'][index]
    itemsCostoTotal[index+1].innerText = formatoMoneda(liquidacionSubTotales+totalCostosLiquidacion['flete'][index]);
  }
  console.log(totalPrecioLiquidacion);
  
}

function formatoMoneda(valor){
  return Intl.NumberFormat("es-CO", {
    style: "currency",
    currency: "COP",
  }).format(valor)
}

export {
  liquidador,
};

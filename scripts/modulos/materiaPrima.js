var host = window.location.origin;
const consultarMateriasPrimas = async () => {
  try {
    var myHeaders = new Headers({
      'Content-Type': 'text/xml'
    });
    const res = await fetch(host+'/api/materias/all',{
      headers: {
        'Access-Control-Allow-Origin':'http://localhost/liquidacion',  
      },
      credentials: "include",
      mode:'cors',
      method: 'GET'
    });
    console.log(res);
    if(res.status==200){
      const data = await res.json();
      console.log(data);
      return data;
    }
  } catch (error) {
    console.log(error)
  }
}

const consultarMateriaPrima = async (referencia) => {
    try {
      console.log(referencia);
      const res = await fetch(host+'/api/materias/all');
      const data = await res.json();
      const mp = await data.find(materiaPrima => materiaPrima.REFERENCIAPROIMPO == referencia);
      console.log(data);
      return mp;
    } catch (error) {
      console.log(error)
    }
}

const consultarListaMateriaPrima = async () => {
  try {
    //console.log(referencia);
    const res = await fetch(host+'/api/materias/all');
    const data = await res.json();
    const lista={};
    console.log(data);
    data.forEach(materiaPrima => {
      lista[new String(materiaPrima.REFERENCIAPROIMPO)]=null; 
    });
    //const mp = await data.find(materiaPrima => materiaPrima.ReferenciaProimpo == referencia);
    console.log(lista);
    return lista;
  } catch (error) {
    console.log(error)
  }
}
  
function dibujandoEnModal(mp){
    let html=`<tr>
    <td>${mp.NOMBRE}</td>
    <td>${mp.CANTIDADAMORTIZACION}</td>
    <td>${mp.VALORMOLDE}</td>
  </tr>`
  return html;
}


function dibujarTabla(data) {
  // specify the columns
  const columnDefs = [
    { headerName: 'Referencia', field: "REFERENCIAPROIMPO" },
    { headerName: 'Tipo', field: "TIPO" },
    { headerName: 'Proveedor', field: "PROVEEDOR" },
    { headerName: 'Nombre', field: "NOMBRE" },
    { headerName: 'Unidad de Medida', field: "UNIDADDEMEDIDA" },
    { headerName: 'Clave', field: "CLAVE" },
    { headerName: 'Largo', field: "LARGO" },
    { headerName: 'Ancho', field: "ANCHO" },
    { headerName: 'Alto', field: "ALTO" },
    { headerName: 'Minima Producción', field: "MINIMAPRODUCCION" },
    { headerName: 'Precio Unidad', field: "PRECIOUNITARIO" },
    { headerName: 'Desperdicio', field: "DESPERDICIO" },
    { headerName: 'Dosificación', field: "DOSIFICACION" },
    { headerName: 'Cantidad Amortización', field: "CANTIDADAMORTIZACION" },
    { headerName: 'Valor Molde', field: "VALORMOLDE" }
  ];

  const rowData = data;

  // let the grid know which columns and what data to use
  const gridOptions = {
    defaultColDef: {
      editable: true,
      filter: true,
      resizable: true,
      sortable: true,
    },
    columnDefs: columnDefs,
    rowData: rowData,
    pagination: true,
    onCellClicked: function (e) {
      console.log('clic', e);
    },
    onCellEditingStarted: function (event) {
      console.log('cellEditingStarted');
    },
    onCellEditingStopped: function (event) {
      console.log('cellEditingStopped', event.data.ID_MP);
      console.log('cellEditingStopped', event.value);
      console.log('cellEditingStopped', event.colDef.field);

      let id=event.data.ID_MP;
      let campo=event.colDef.field;
      let valor=event.value;

      actualizar(id,campo,valor);
    },
    localeText: {
      // for filter panel
      page: 'Pagina',
      more: 'Mas',
      to: 'a',
      of: 'de',
      next: 'Siguente',
      last: 'Último',
      first: 'Primero',
      previous: 'Anteror',
      loadingOoo: 'Cargando...',
      contains: 'Contiene',
      notContains: 'No contiene',
      startsWith: 'Empieza con',
      endsWith: 'Termina con',
      filterOoo: 'Filtrar',
      applyFilter: 'Aplicar Filtro...',
      equals: 'Igual',
      notEqual: 'No Igual',
      andCondition: 'Y',
      orCondition: 'O',
    }
  };


  // lookup the container we want the Grid to use
  const eGridDiv = document.querySelector('#myGrid');

  // create the grid passing in the div to use together with the columns & data we want to use
  new agGrid.Grid(eGridDiv, gridOptions);

  var allColumnIds = [];
  gridOptions.columnApi.getAllColumns().forEach(function (column) {
    allColumnIds.push(column.colId);
  });
  gridOptions.columnApi.autoSizeColumns(allColumnIds, false);

}


//Metodos para generar acciones en la tabla a nivel de API
const actualizar = async (id,campo,valor) => {
  try {
    
    const formData = new FormData();
    formData.append('id',id);
    formData.append('campo',campo);
    formData.append('valor',valor);
    const res = await fetch(host+'/api/materias/update',
      {
        method: 'POST',
        body: formData,
      });
      console.log(res);
    const data = await res.json();
    const { Mensaje } = data;
  console.log(Mensaje);
    
  } catch (error) {
    console.log(error)
  }
}


export {consultarMateriaPrima,consultarListaMateriaPrima, consultarMateriasPrimas, dibujandoEnModal,dibujarTabla}


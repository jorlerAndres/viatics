var imagenURL='https://proimpocatalogo.com/catalogo/master/'
var host = window.location.origin;

const consultarListaProductos = async () => {
  try {
    
    const res = await fetch(host+'/api/productos/all');
    const data = await res.json();
    const lista={};
    console.log(data);
    data.forEach(producto => {
      var referencia=producto.REFERENCIA;
      lista[new String(referencia)]=imagenURL+referencia+".1.jpg"
    });
    //const mp = await data.find(materiaPrima => materiaPrima.ReferenciaProimpo == referencia);
    console.log(lista);
    return lista;
  } catch (error) {
    console.log(error)
  }
}


const consultarProducto = async (Referencia) => {
  try {
    const res = await fetch(host+"/api/productos/all");
    const data = await res.json();
    const producto = await data.find((P) => P.REFERENCIA == Referencia);
    return producto;
  } catch (error) {
    console.log(error);
  }
};

const consultarProductos = async () => {
  try {
    const res = await fetch(host+'/api/productos/all');
    const data = await res.json();
    console.log(data);
    return data;
  } catch (error) {
    console.log(error);
  }
};

function dibujarProducto(producto) {
  let referencia=producto.REFERENCIA;
  const descripcionP=document.getElementById('descripcionP');
  descripcionP.setAttribute("placeholder","")
  descripcionP.value=producto.DESCRIPCION;
  const referenciaP=document.getElementById('referenciaP');
  referenciaP.innerHTML=`<strong>${referencia}</strong>`
  const unidadEmpaque=document.getElementById('unidadEmpaque');
  unidadEmpaque.setAttribute("placeholder","")
  unidadEmpaque.value=producto.UNIDAD_EMPAQUE
  const img_referenciaP=document.getElementById('img_referenciaP');
  img_referenciaP.setAttribute('src',imagenURL+referencia+".1.jpg")
}

//Tabla agGrid

let gridOptions = "";
let columnDefs = [];
let eGridDiv = "";
let rowData = {};

function dibujarTabla(data) {
  // specify the columns
  columnDefs = [
    { headerName: "Referencia", field: "REFERENCIA" },
    { headerName: "Decscripción", field: "DESCRIPCION" },
    { headerName: "Numero Partes", field: "NPARTES" },
    { headerName: "Numero Procesos", field: "NPROCESO" },
    { headerName: "Unidad de Empaque", field: "UNIDAD_EMPAQUE" },
    { headerName: "Unidad de Medida", field: "UNDMEDIDA" },
  ];

  rowData = data;

  // let the grid know which columns and what data to use
  gridOptions = {
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
      console.log("clic", e);
    },
    onCellEditingStarted: function (event) {
      console.log("cellEditingStarted");
    },
    onCellEditingStopped: function (event) {
      console.log("cellEditingStopped", event.data.ID_PRODUCTO);
      console.log("cellEditingStopped", event.value);
      console.log("cellEditingStopped", event.colDef.field);

      let id = event.data.ID_PRODUCTO;
      let campo = event.colDef.field;
      let valor = event.value;

      actualizar(id, campo, valor);
    },
    localeText: {
      // for filter panel
      page: "Pagina",
      more: "Mas",
      to: "a",
      of: "de",
      next: "Siguente",
      last: "Último",
      first: "Primero",
      previous: "Anteror",
      loadingOoo: "Cargando...",
      contains: "Contiene",
      notContains: "No contiene",
      startsWith: "Empieza con",
      endsWith: "Termina con",
      filterOoo: "Filtrar",
      applyFilter: "Aplicar Filtro...",
      equals: "Igual",
      notEqual: "No Igual",
      andCondition: "Y",
      orCondition: "O",
    },
  };

  // lookup the container we want the Grid to use
  eGridDiv = document.querySelector("#myGrid");

  // create the grid passing in the div to use together with the columns & data we want to use
  new agGrid.Grid(eGridDiv, gridOptions);

  var allColumnIds = [];
  gridOptions.columnApi.getAllColumns().forEach(function (column) {
    allColumnIds.push(column.colId);
  });
  gridOptions.columnApi.autoSizeColumns(allColumnIds, false);
}

//Metodos para generar acciones en la tabla a nivel de API
const actualizar = async (id, campo, valor) => {
  try {
    const formData = new FormData();
    formData.append("id", id);
    formData.append("campo", campo);
    formData.append("valor", valor);
    const res = await fetch(host+'/api/productos/update', {
      method: "POST",
      body: formData,
    });
    console.log(res);
    const data = await res.json();
    const { Mensaje } = data;
    console.log(Mensaje);
  } catch (error) {
    console.log(error);
  }
};

export { consultarProducto, dibujarProducto, consultarProductos,consultarListaProductos, dibujarTabla };

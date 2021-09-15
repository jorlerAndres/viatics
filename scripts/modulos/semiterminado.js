var host = window.location.origin;
const consultarST = async (referencia) => {
    try {
      const res = await fetch(host+'/api/semiterminados/all');
      const data = await res.json();
      console.log(data);
      const st = await data.find(semiTerminado => semiTerminado.REFERENCIASEMITERMINADOS == referencia);
      return st;
    } catch (error) {
      console.log(error)
    }
  }

  const consultarListaST = async () => {
    try {
      //console.log(referencia);
      const res = await fetch(host+'/api/semiterminados/all');
      const data = await res.json();
      const lista={};
      data.forEach(semiTerminado => {
        lista[new String(semiTerminado.REFERENCIASEMITERMINADOS)]=null; 
      });
      //const mp = await data.find(materiaPrima => materiaPrima.ReferenciaProimpo == referencia);
      console.log(lista);
      return lista;
    } catch (error) {
      console.log(error)
    }
  }

function dibujandoEnModal(st){
    let html=`
    <b><p>Descripcion:</b> ${st.Descripcion}<br> </p>
    <b>ValorPieza:</b> ${st.ValorPieza}<br>`
    return html;
}

const consultarSemiterminados = async () => {
  try {
    const res = await fetch(host+'/api/semiterminados/all');
    const data = await res.json();
    console.log(data);
    return data;
  } catch (error) {
    console.log(error);
  }
};

//Tabla agGrid

let gridOptions = "";
let columnDefs = [];
let eGridDiv = "";
let rowData = {};

function dibujarTabla(data) {
  // specify the columns
  columnDefs = [
    { headerName: "Referencia", field: "REFERENCIASEMITERMINADOS" },
    { headerName: "Decscripción", field: "DESCRIPCION" },
    { headerName: "Ciclo", field: "CICLO" },
    { headerName: "Cavidades", field: "CAVIDADES" },
    { headerName: "Unidad Hora", field: "UND_HORA" },
    { headerName: "Unidad Dia", field: "UND_DIA" },
    { headerName: "Unidad Mes", field: "UND_MES" },
    { headerName: "Valor Hora", field: "VALORHORA" },
    { headerName: "Valor Pieza", field: "VALORPIEZA" },
    { headerName: "Peso Pieza", field: "PESOPIEZA" },
    { headerName: "Peso Pieza", field: "PROCESO" }
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
      console.log("cellEditingStopped", event.data.ID_ST);
      console.log("cellEditingStopped", event.value);
      console.log("cellEditingStopped", event.colDef.field);

      let id = event.data.ID_ST;
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

const actualizar = async (id,campo,valor) => {
  try {
    const formData = new FormData();
    formData.append('id',id);
    formData.append('campo',campo);
    formData.append('valor',valor);
    const res = await fetch(host+'/api/semiterminados/update',
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

  export {consultarST,consultarListaST,dibujandoEnModal,consultarSemiterminados,dibujarTabla}
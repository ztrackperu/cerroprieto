/*
$(document).ready(function() {
    if(modalComando == 0){
        $('#modalComando').modal({
            backdrop: 'static', // No permite cerrar haciendo clic fuera del modal
            keyboard: false     // No permite cerrar presionando la tecla Esc
        });
    }else{
        $('#modalComando').modal({
            //permite cerrar
            backdrop: true, 
            keyboard: true     
        });
    }
    $('#modalComando').modal('show');
   
    dataComando();
});*/


document.addEventListener("DOMContentLoaded", async function() {
    try {
       cargarBarra();
       dataComando();

    } catch (err) {
        alert(err);
    }
    setInterval( async function(){ okey =  await cargarBarra();}, 30000);
    setInterval( async function(){ okey =  await dataComando();}, 30000);
});

async function cargarBarra(){
    const response = await fetch(base_url + "AdminPage/ListaComandos/", { method: "GET" });
    const result = await response.json();
    console.log('RESULTADO');
    let data = result.data;
    let lista = data.lista;
    //console.log(lista);
    let totalProgress = 0;
    const totalItems = data.contador * 2; 

    lista.forEach(item => {
        if (item.fecha_ejecucion) {
            totalProgress += 1;
        }

        if (item.validacion) {
            totalProgress += 1;
        }
    });

    // Calcular el porcentaje
    const percentage = (totalProgress / totalItems) * 100;

    console.log('PORCENTAJE');
    console.log(percentage);
    let vpercentage = document.getElementById('valuePercentage');
    vpercentage.innerHTML = percentage.toFixed(2) + '%';

    // Actualizar la barra de progreso
    $('#progressbar').progressbar({
        value: percentage
    });
    $("#progressbar").css({ 'background': '#e4eefa' });
    $("#progressbar > div").css({ 'background': '#0e2238' });
}

function dataComando(){
    tblComandos = $('#tblComandos').DataTable({
    ajax: {
        url: base_url + "AdminPage/ListaComandos",
        dataSrc: function(result) {
            // Acceder a los campos dentro de 'result.data.lista'
            return result.data.lista;
        }
    },
    buttons: [
        {extend: "excel", className: "buttonsToHide visually-hidden"},
        {extend: "pdf", className: "buttonsToHide visually-hidden"},
        {extend: "print", className: "buttonsToHide visually-hidden"}
        ],
    columns: [
        {'data': null },
        {'data': 'evento'},
        {'data': 'status'},
        {'data': null},
        {'data': 'validacion'},
        {'data': 'fecha_creacion'},
        {'data': 'fecha_ejecucion'},
        {'data': 'validacion'}
    ],
    columnDefs: [
        {
            targets:0,
            render: function(data, type, row, meta){
                return meta.row + 1;
            }
        },
        {
            targets: 2, 
            render: function(data, type, row, meta) {
                if (data === 1) {
                    return 'Solicitado';
                }else if (data === 2) {
                    return 'Ejecutado';
                }else if(data === 3){
                    return 'Validado';
                }else if(data === 4){
                    return 'Eliminado';
                }
            }
        },
        {
            targets: 3, // Índice de la columna 'acciones'
            render: function(data, type, row, meta) {
                if (row.validacion === null) {
                    return '<button class="btn btn-danger" onclick="myFunction(' + (meta.row + 1) + ')">Eliminar</button>';
                }
                return ''; 
            }
        },
        {
            targets: 4, 
            visible: false 
        },
        {
            targets: 5, 
            render: function(data, type, row, meta) {
                //conversion de fecha
                let fecha = new Date(data);
                fecha = fecha.toLocaleString();
                return data ? fecha : 'NA'; 
            }
        },
        {
            targets: 6, 
            render: function(data, type, row, meta) {
                let fecha = new Date(data);
                fecha = fecha.toLocaleString();
                return data ? data : 'NA';
            }
        },
        {
            targets:7,
            render: function(data,type,row,meta){
                let fecha = new Date(data);
                fecha = fecha.toLocaleString();
                return data ? fecha : 'NA';
            }
        }
    ],
    responsive: true,
    bDestroy: true,
    iDisplayLength: 10,
    dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    });
}


function myFunction(id) {
    alert('ID: ' + id);
}

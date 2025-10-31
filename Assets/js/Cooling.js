let modalComando = 0;
let datosArray = [];
function frmProcess(e){
    e.preventDefault();
    //let valorH = $('input[name=\'homogenization\']').val();
    //let valorR = $('input[name=\'ripening\']').val();
    //let valorV = $('input[name=\'ventilation\']').val();
    let valorC = $('input[name=\'cooling\']').val();
    let valorT = $('input[name=\'tmpProduct\']').val();
    //let created_at = new Date().toLocaleString();
   
    //if(valorH != 0 && valorR != 0 && valorV != 0 && valorC != 0 && valorT != 0){
    if(valorC != 0 && valorT != 0){

        $('#strtProcess').modal('show');
        //$('#validateH').val(valorH);
        //$('#validateR').val(valorR);
        //$('#validateV').val(valorV);
        $('#validateC_c').val(valorC);
        $('#validateTP_c').val(valorT);


        //Vaciar Array
        datosArray=[];
        //Creando Objeto
        datosArray.push ({
            //homogenization: valorH,
            //ripening: valorR,
            //ventilation: valorV,
            cooling: valorC,
            tmpProduct: valorT,
            //created_at: created_at
        })
    }else{
        Swal.fire({
            icon: 'error',
            title: 'Oops...!',
            text: 'It looks like an input has 0 value',
        });
    }
    //$('#strtProcess').modal('show');
    /*Swal.fire({
        icon: 'error',
        title: 'Oops...!',
        text: 'It looks like an input has 0 value',
    });*/
}

function alertas(msg, icono) {
    Swal.fire({
        position: 'top-end',
        icon: icono,
        title: msg,
        showConfirmButton: false,
        timer: 3000
    })
}


async function btnProcesar(){

    console.log(datosArray);
    $('#formProcess').trigger('reset');
    $('#strtProcess').modal('hide');
    //trama = datosArray[0].homogenization+"|"+datosArray[0].ripening+"|"+datosArray[0].ventilation+"|"+datosArray[0].cooling+"|"+datosArray[0].tmpProduct ;
    trama = datosArray[0].cooling+"|"+datosArray[0].tmpProduct;
    console.log(trama);
    $("#loading").show();
    
    const response = await fetch(base_url + "Cooling/Comando/"+trama, {method: "GET", });

    const result = await response.json();
    analizar =JSON.parse(result) ;
    if(analizar.estado==0){
        acc = "success";
        men = "Cooling Mode active...";

    }else{
        acc = "error";
        men = "wait...";
    }
    alertas(men, acc); 
    //document.getElementById('loading').style.display = 'none';

    $('#strtProcess').modal('hide');
    //setTimeout(1000);
    //console.log("aqui");
    setTimeout(function(){
        //console.log("Hola Mundo");
        //dirigirme al menu
        window.location.href = base_url+"AdminPage";

    }, 2000);
    
}

$(document).ready(function(){
    $('#homogenizationInput').TouchSpin({
        min: 0,
        max: 100,
        step: 1,
        decimals: 0,
        boostat: 5,
        maxboostedstep: 10,
        postfix: 'Hours'
    })
    $('#ripeningInput').TouchSpin({
        min: 0,
        max: 100,
        step: 1,
        decimals: 0,
        boostat: 5,
        maxboostedstep: 10,
        postfix: 'Hours'
    })
    $('#ventilationInput').TouchSpin({
        min: 0,
        max: 100,
        step: 1,
        decimals: 0,
        boostat: 5,
        maxboostedstep: 10,
        postfix: 'Hours'
    })
    $('#coolingInput').TouchSpin({
        min: 0,
        max: 100,
        step: 1,
        decimals: 0,
        boostat: 5,
        maxboostedstep: 10,
        postfix: 'Hours'
    })
    $('#tmpProductInput').TouchSpin({
        min: -40,
        max: 108,
        step: 1,
        decimals: 0,
        boostat: 5,
        maxboostedstep: 10,
        postfix: '°F'
    })
});





let modalComando = 0;

datosArray = [];
dataON_OFF=[];
function frmProcess(e){
    e.preventDefault();
    let valorT = $('input[name=\'spTemperature\']').val();
    let valorE = $('input[name=\'spEthylene\']').val();
    let valorC = $('input[name=\'spCo2\']').val();
    let valorH = $('input[name=\'spHumidity\']').val();
    let valorI = $('input[name=\'iHours\']').val();

    if(valorT != 0 && valorE != 0 && valorC != 0 && valorH != 0 && valorI != 0){
        $('#strtProcess').modal('show');
        $('#validateTMP').val(valorT);
        $('#validateEthy').val(valorE);
        $('#validateCo2').val(valorC);
        $('#validateHm').val(valorH);
        $('#validateIH').val(valorI);

        datosArray=[];
        //Creando Objeto
        datosArray.push ({
            spTmp: valorT,
            spEthy: valorE,
            spCo2: valorC,
            spHm: valorH,
            iHours: valorI
        })
    }else{
        Swal.fire({
            icon: 'error',
            title: 'Oops...!',
            text: 'It looks like an input has 0 value',
        });
    }
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
    trama = datosArray[0].spTmp+"|"+datosArray[0].spEthy+"|"+datosArray[0].iHours+"|"+datosArray[0].spHm+"|"+datosArray[0].spCo2 ;
    console.log(trama);
    $("#loading").show();
    const response = await fetch(base_url + "Ripener/Comando/"+trama, {method: "GET", });

    //const response = await fetch(base_url + "Ripener/Comando", {method: "POST",body: JSON.stringify(datosArray),headers: {"Content-Type": "application/json",},});
    const result = await response.json();
    analizar =JSON.parse(result) ;
    if(analizar.estado==1){
        acc = "success";
        men = "loading...";

    }else{
        acc = "error";
        men = "wait...";
    }
    alertas(men, acc); 
    document.getElementById('loading').style.display = 'none';

    $('#strtProcess').modal('hide');
    //location.href =base_url + "Ripener/AdminPage";
    window.location.href = base_url+"AdminPage";


    

}

$(document).ready(function(){
    $('#tmpInput').TouchSpin({
        min: 58,
        max: 104,
        step: 0.1,
        decimals: 1,
        boostat: 5,
        maxboostedstep: 10,
        postfix: '°F'
    })
    $('#ethyInput').TouchSpin({
        min: 1,
        max: 300,
        step: 1,
        decimals: 0,
        boostat: 5,
        maxboostedstep: 10,
        postfix: 'PPM'
    })
    $('#co2Input').TouchSpin({
        min: 0.1,
        max: 30,
        step: 0.1,
        decimals: 1,
        boostat: 5,
        maxboostedstep: 10,
        postfix: '%'
    })
    $('#hmInput').TouchSpin({
        min: 80,
        max: 99,
        step: 1,
        decimals: 0,
        boostat: 5,
        maxboostedstep: 10,
        postfix: '%'
    })
    $('#ihoursInput').TouchSpin({
        min: 0,
        max: 100,
        step: 1,
        decimals: 0,
        boostat: 5,
        maxboostedstep: 10,
        postfix: 'Hrs'
    })
});
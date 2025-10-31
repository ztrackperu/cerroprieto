let modalComando = 1;
/*
tituloGrafica.textContent =id;
const response = await fetch(base_url + "Graph/GraficaInicial/"+id, {method: "GET", });
const result = await response.json();
//reemplazar el nombre de la grafica 

console.log(result);
todo = result ;
fechaFin.value =result.date[1] ;
fechaInicial.value =result.date[0] ;
//setInterval( function(){ $(".loader").fadeOut("fast"); }, 1000);

graph = await graficaMadurador1(result.graph,result.cadena,result.temperature,result.temperature);

return result;

*/
let temp_c_f = document.getElementById('temp_c_f');
let tituloGrafica = document.getElementById('tituloGrafica');
let fechaInicial = document.getElementById('fechaInicial');
let fechaFin = document.getElementById('fechaFin');
const grafica1 = document.getElementById("graficaFinal");
const bajarGrafica = document.getElementById('bajarGraph');
timeUser =new Date();
console.log(timeUser.getUTCHours());
extraerdata =[];
todo={};
function c_f(temp,data=0){
    if(data==0){
        res = temp==0 ? 'C' :'F';
        temp_c_f.value=temp;
    }else{res= temp==0 ? data: parseInt((data*9)/5 +32);}
    return res;
}
function dato_procesado(data,temp1){
    data2=[];
    data.forEach(function(num){
        if(num){
            num = temp1==1 ? (parseInt(((num*9)/5+32)*100))/100: (parseInt((((num-32)*5)/9)*100))/100;
        }
        data2.push(num);
    })
    return data2;
}
temp_c_f.addEventListener('change', async function(){
    der = temp_c_f.value;
    console.log(der);
    console.log(todo);
    okey = await graficaMadurador1(todo.graph,todo.cadena,todo.temperature,der);
})
async function procesarFecha(){
    
    contenedor =tituloGrafica.textContent ;
    fechaInicialx=fechaInicial.value;
    console.log(fechaInicialx)
    fechaFinx=fechaFin.value;
    console.log(fechaFinx)
    if(fechaInicialx==''|| fechaFinx==''){alert("No se seleccionado las fechas");
    }else{
        console.log("vamos a analizar");
        conj= contenedor+"/"+"14872"+"/"+fechaInicialx+"/"+fechaFinx;
        console.log(conj);
        if(fechaInicialx!=todo.date[0] || fechaFinx!=todo.date[1]){
            $(".loader").show();
            const response = await fetch(base_url + "Graph/GraficaInicial/"+conj,{method: 'GET'});
            const data = await response.json();

            if(data=="mal"){alert("Fecha Inicial mayor a Fecha Mayor!");}
            else if(data=="rango"){alert("Búsqueda fuera de Rango , contacta al Administrador");}
            else{
                //aqui recibimos la infro procesada y lista pa mostrar en la grafica 
                console.log(data);
                todo = data ;
                graph = await graficaMadurador1(data.graph,data.cadena,data.temperature,data.temperature);
                //setInterval( async function(){ $(".loader").fadeOut("fast"); }, 1000);

            }
        }else{
            alert("Fechas Procesadas!");
        }
    }

}

function replaceOutliers(array) {
    // Función para calcular la media
    function mean(arr) {
        return arr.reduce((acc, val) => acc + val, 0) / arr.length;
    }

    // Función para calcular la desviación estándar
    function standardDeviation(arr, mean) {
        const variance = arr.reduce((acc, val) => acc + Math.pow(val - mean, 2), 0) / arr.length;
        return Math.sqrt(variance);
    }

    // Calcular media y desviación estándar
    const arrayFiltered = array.filter(val => val !== null && !isNaN(val));
    const avg = mean(arrayFiltered);
    const stdDev = standardDeviation(arrayFiltered, avg);

    // Reemplazar valores atípicos por null
    return array.map(val => {
        if (val === null || isNaN(val)) {
            return null;
        }
        // Verificar si el valor está fuera del rango de ±2 desviaciones estándar
        return Math.abs(val - avg) > 2 * stdDev ? null : val;
    });
}

document.addEventListener("DOMContentLoaded", async function(){

    try{

        const response = await fetch(base_url + "Graph/ListaDispositivoEmpresa",{method: 'GET'}); 
        const data = await response.json();
        console.log(data);
        console.log(data.telemetria_id);
        console.log(data.nombre_contenedor);
        //id=data.nombre_contenedor;
        //en este caso enviar id de telemetria


        nombre_device=data.nombre_contenedor;
        telemetria=data.telemetria_id;
        tituloGrafica.textContent =nombre_device;



        const response1 = await fetch(base_url + "Graph/GraficaInicial/"+nombre_device+"/"+telemetria, {method: "GET", });
        const result = await response1.json();
        console.log(result);
        todo = result ;
        fechaFin.value =result.date[1] ;
        fechaInicial.value =result.date[0] ;
        //console.log(todo)
        
        graph = await graficaMadurador1(result.graph,result.cadena,result.temperature,result.temperature);



        /*
        data.data.forEach((contenedor, indice) => pintarCirculo(contenedor,indice));
        //insertar en texto la data 
        //console.log(data.text);
        carruselExtra.innerHTML  =data.text;
        cardOnline.innerHTML = data.estadofecha[0];
        cardWait.innerHTML = data.estadofecha[1];
        cardOffline.innerHTML = data.estadofecha[2];

        d1 = data.estadofecha[0];
        d2 = data.estadofecha[1];
        d3 = data.estadofecha[2];

        //console.log(data.extraer);
        extraerdata = data.extraer ;
        //console.log(extraerdata);
       
        //myDoughnutChart.update();
        // CREANDO GRÁFICO DOUGHNUT PARA LAS ALARMAS
        var ctx = document.getElementById('grfAlarma').getContext('2d');
        var myDoughnutChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Online', 'Wait', 'Offline'],
                datasets: [{
                    label: 'Equipos',
                    data: [d1,d2,d3], 
                    backgroundColor: [
                        'rgb(0, 116, 75)',
                        'rgb(255, 193, 0)',
                        'rgb(233, 26, 51)'
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Estado de los Equipos'
                    }
                }
            }
        });
        */
 
        
    }catch(err){alert(err);}
    //cada 10 segundos ejecutar 
    //setInterval( async function(){ okey =  await obtenerCambio();}, 10000);
    
})

async function graficaMadurador1(info,cadena,temp,temp1){
    //$(".loader").show();
    console.log(temp);
    //console.log(info);
    //console.log(cadena);
    cambioTemp=1;
    if(temp!=temp1){cambioTemp=2;}
    dataGrafica =[];
    for (var i = 0; i < cadena.length; i++) {
        if(cadena[i]!='created_at'){
            boleto =cadena[i];
            //console.log(info[boleto]);
            if(info[boleto].config[3]==1){
                eje = "y";
            }else if(info[boleto].config[3]==3){eje = "y1";
            }else{eje="y2";}
            if(info[boleto].config[3]==4){fillx=true;}else{fillx=false;}
            nombrelabel =info[boleto].config[0] ;

            datito = (info[boleto].config[3]==1 && cambioTemp==2)? dato_procesado(info[boleto].data,temp1):info[boleto].data ;

            if(nombrelabel.includes('Set') ||nombrelabel.includes('Sp')|| info[boleto].config[3]==4){
                displayX =false;

            }else{
                displayX ='auto';
                datito =datito
                //datito =replaceOutliers(datito);
            }
            obj = {
                label : info[boleto].config[0],
                data : datito  ,
                backgroundColor: info[boleto].config[2], // Color de fondo
                borderColor: info[boleto].config[2], // Color del borde
                borderWidth: 3,// Ancho del borde
                yAxisID : eje,
                pointRadius: 0,
                cubicInterpolationMode: 'monotone',
                tension: -0.2,
                hidden :info[boleto].config[1],
                fill: fillx,
                spanGaps: true,
                datalabels: {
                    display: displayX,
                    clip :'true',
                    clamp :'true',
                    align: 'start',  
                    //anchor:'start' 
                  },
            };
            dataGrafica.push(obj);
        }
        //console.log(cadena[i]);
    }
    console.log(dataGrafica);
    //console.log(X1)
    if (typeof X1 !== 'undefined') {X1.destroy();}
    const getOrCreateLegendList = (chart, id) => {
        const legendContainer = document.getElementById(id);
        let listContainer = legendContainer.querySelector('div');  
        if (!listContainer) {
          listContainer = document.createElement('div');
          listContainer.className = "row justify-content-center row-cols-4 row-cols-sm-4 row-cols-md-4 ";
          listContainer.style.display = 'flex';
          listContainer.style.flexDirection = 'row';
          listContainer.style.margin = 0;
          listContainer.style.padding = 0;
          legendContainer.appendChild(listContainer);
        } 
        return listContainer;
      };  
      const htmlLegendPlugin = {
        id: 'htmlLegend',
        afterUpdate(chart, args, options) {
          const ul = getOrCreateLegendList(chart, options.containerID);
          // Remove old legend items
          while (ul.firstChild) {
            ul.firstChild.remove();
          }
          // Reuse the built-in legendItems generator
          const items = chart.options.plugins.legend.labels.generateLabels(chart);
          items.forEach(item => {
            const sdiv = document.createElement('div');
            sdiv.style.paddingLeft = '2px';
            sdiv.style.paddingRight = '2px';
            sdiv.className = "col-4 col-lg-1 col-md-2 col-sm-3";
            //sdiv.addClass('col-xs-6 col-1 ');
            //sdiv.class = 'col-xs-6 col-1 ';
            sdiv.id = item.text;
            cambio ="'"+item.text+"'";
            //papa=document.querySelector('Set Point');
            //papa.className='col-xs-6 col-1';
            //$(cambio).addClass('col-xs-6 col-1 ');
            //$(cambio).addClass('col-xs-6 col-1 ');
            //sdiv[item.text].addClass('col-xs-6 col-1 ');
            //style="padding-left: 2px;padding-right: 2px;"
            //sdiv.style.flexDirection = 'row';
            //sdiv.style.marginLeft = '10px';
            const li = document.createElement('li');
            li.style.alignItems = 'center';
            li.style.cursor = 'pointer';
            li.style.display = 'flex';
            li.style.flexDirection = 'row';
            li.style.marginLeft = '10px'; 
            li.onclick = () => {
              const {type} = chart.config;
              if (type === 'pie' || type === 'doughnut') {
                // Pie and doughnut charts only have a single dataset and visibility is per item
                chart.toggleDataVisibility(item.index);
              } else {
                chart.setDatasetVisibility(item.datasetIndex, !chart.isDatasetVisible(item.datasetIndex));
              }
              chart.update();
            };
            // Color box
            const boxSpan = document.createElement('span');
            boxSpan.style.background = item.fillStyle;
            boxSpan.style.borderColor = item.strokeStyle;
            boxSpan.style.borderWidth = item.lineWidth + 'px';
            boxSpan.style.display = 'inline-block';
            boxSpan.style.flexShrink = 0;
            boxSpan.style.height = '20px';
            boxSpan.style.marginRight = '10px';
            boxSpan.style.width = '20px';
            // Text
            const textContainer = document.createElement('p');
            textContainer.style.color = item.fontColor;
            textContainer.style.margin = 0;
            textContainer.style.padding = 0;
            textContainer.style.textDecoration = item.hidden ? 'line-through' : '';
            tx = item.text;
            tx1=tx.split(' ');
            //const text = document.createTextNode(item.text);
            if(tx1[0].length >7){
                tx1[0]=tx1[0].substr(-20, 8);
                //if(tx1[0]=="cargo_4_temp")
            }
            const text = document.createTextNode(tx1[0]);   
            textContainer.appendChild(text);   
            li.appendChild(boxSpan);
            li.appendChild(textContainer);
            sdiv.appendChild(li);
            ul.appendChild(sdiv);
            //ul.appendChild(li);
          });
        }
      };
      const textCenter = {
        id:'textCenter',
        afterDatasetsDraw(chart,args,plugins){
            const {ctx,chartArea:{top,bottom,left,right,width,height}}=chart;
            ctx.save();
            ctx.font = 'bold 15px sans-serif';
            ctx.fillStyle ='grey';
            ctx.fillText(tituloGrafica.textContent,(width*45)/100 ,(height*9)/10);
        }
      }
      const plugin = {
        id : 'customCanvasBackgroundColor',
        beforeDraw : (chart ,args ,options) => {
          const {ctx} = chart;
          ctx.save();
          ctx.globalCompositeOperation = 'destination-over';
          ctx.fillStyle = options.color || '#000000';
          ctx.fillRect(0,0,chart.width,chart.height);
          ctx.restore();
        }
      }
    X1 =new Chart(grafica1, {
        type: 'line',// Tipo de gráfica
        data: {
            labels: info['created_at'].data,
            datasets: dataGrafica,
        },
        options: {
            animation: {
                onComplete: function () {
                    //bajarGrafica.href= X1.toBase64Image();
                    //bajarGrafica.download = tituloGrafica.textContent+"_"+fechaInicial.value+"_"+fechaFin.value;                   
                },
            },
            responsive : true,
            //aspectRatio:3|1,
            backgroundColor: '#fff',
            interaction :{
                mode : 'index',
                intersect :false,
            },
            stacked :false,
            scales: {
                //min:3,
                x:{
                    type:'time',
                    //display: false,
                    //position: 'right',
                    //beginAtZero: true,
                    title: {
                        display: true,
                        text: 'ZTRACK - Live Telematic',
                        color: '#212529',
                        font: { 
                            size: 20,
                            style: 'normal',
                            lineHeight: 1.1
                        },
                        padding: {top: -5, left: 0, right: 0, bottom: 0}
                      },
                    //offset:true,
                    alignToPixels:true,
                    time:{
                        minUnit:'minute',
                    },
                    clip :false,
                    ticks:{
                        major:{
                            enabled:true,
                            width:4
                        },
                        font :(context)=>{
                            //console.log(context.tick && context.tick.major)
                            const boldedTicks = context.tick && context.tick.major ? 'bold' :'';
                            return {weight:boldedTicks}
                        },
                        //padding:15,
                    }
                },
                y: {
                    type: 'linear',
                    position: 'left',
                    display: true,
                    title: {
                        display: false,
                        text: 'temperature',
                        color: '#1a2c4e',
                        //reverse:true,
                        font: {     
                            size: 20,
                            style: 'normal',
                            lineHeight: 1.2
                        },
                        padding: {top: 30, left: 0, right: 0, bottom: 0}
                    },
                    ticks:{
                        color:"blue",
                        callback :(value,index,ticks) =>{
                            return `${value}${c_f(temp1)}\u00B0`;
                        }
                    },
                    suggestedMin: c_f(temp1,10),
                    suggestedMax: c_f(temp1,20)
                },
                y1: {
                    type: 'linear',
                    display: false,
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: false,
                        text: 'Ethylene(ppm)',
                        color: '#1a2c4e',
                        font: { 
                            size: 20,
                            style: 'bold',
                            lineHeight: 1.2
                        },
                        padding: {top: 30, left: 0, right: 0, bottom: 0}
                      },
                      suggestedMin: 0,
                      suggestedMax: 250,
                      grid: {
                        drawOnChartArea: false, // only want the grid lines for one axis to show up
                      },
                 },
                y2: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: false,
                        text: 'Percentage (%)',
                        color: '#1a2c4e',
                        font: {                      
                            size: 20,
                            style: 'normal',
                            lineHeight: 1.2
                        },
                        padding: {top: 30, left: 0, right: 0, bottom: 0}
                    },
                    ticks:{
                        color:"red",
                        callback :(value,index,ticks) =>{
                            return `${value}\u2052`;
                        }
                    },
                    grid: {
                        drawOnChartArea: false, // only want the grid lines for one axis to show up
                    },
                    suggestedMin: 0,
                    suggestedMax: 100,
                },
            },
            plugins: {
                htmlLegend: {
                    // ID of the container to put the legend in
                    containerID: 'legend-container',
                  },
                datalabels: {
                    color: function(context) {
                      return context.dataset.backgroundColor;
                    },
                    font: {
                      weight: 'bold'
                    },          
                    padding: 6,
                  },
                title: {
                    display: false,
                    text: "prueba",
                    color: '#1a2c4e',
                    font: {                        
                        size: 30,
                        style: 'normal',
                        lineHeight: 1.2
                    },
                    padding: {top: 30, left: 0, right: 0, bottom: 0}
                },
                zoom: {
                    limits: {
                        x: {min: 'original', max: 'original',minRange:2}
                     },
                    pan :{
                        enabled :true,
                        mode: 'x',
                    },


                     //minRange:10000,
                    zoom: {
                        wheel: {
                            enabled: true,
                            speed:0.05
                        },
                        pinch: {
                            enabled: true
                        },
                        drag:{
                            enabled:false
                        },

                        mode: 'x',

                        //scaleMode :'x',
                    }
                },
                customCanvasBackgroundColor : {
                    color :'#fff',
                },
                legend : {
                    display:false,
                    position :'top',
                    align : 'center',
                    labels : {
                        boxWidth :20 ,
                        boxHeight : 20,
                        color :'#1a2c4e',
                        padding :15 ,
                        textAlign : 'left',
                        font: {
                            size: 12,
                            style: 'normal',
                            lineHeight: 1.2
                          },
                        title : {
                            text :'Datos Graficados:',
                        },
                    },
                },

            }           
        },
        plugins : [plugin,ChartDataLabels,htmlLegendPlugin,textCenter],       
    })
    //if (typeof X1 !== 'undefined') {X1.destroy();}

    //$("#interfazGrafica").modal("show");
    //setInterval(  function(){ $(".loader").fadeOut("fast"); }, 1000);

}
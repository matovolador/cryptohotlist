<?php include("../_config.php"); ?>
<div class="panel-body  center-text ">
<?php
$name = "Bitcoin Cash";
$symbol = "BCH";

?>
<div class="row">
<div class="col-sm-0 col-md-5"></div>
<div class="sampler col-sm-12 col-md-2">
<label for='time-sampler'>Sampling: </label>
<select class="form-control" id='time-sampler'>
	<option value='1' selected>5 seconds</option>
	<option value='12'>1 minute</option>
	<option value='60'>5 minutes</option>
	<option value='120'>10 minutes</option>
	<option value='180'>15 minutes</option>
	<option value='360'>30 minutes</option>
</select>
</div>
<div class="col-sm-0 col-md-5"></div>
</div>
<div class="row">
<div class="col-sm-0 col-md-10"></div>
<div class="col-sm-12 col-md-2">
<div id='countdown'>0</div>
</div>
</div>
<canvas class="chart-single" id="<?php echo $name?>" ></canvas>

<script type="text/javascript">

$(document).ready(function(){
var sampling = 1;
$("#time-sampler").change(function(){
	sampling = $("#time-sampler").val();
});
var symbol = "<?php echo $symbol ?>";
var namey = "<?php echo $name ?>";
var myOptions =  {
	    	
	    	legend: {
	    		display: true
	    	},
	        scales: {
	            yAxes: [{
	                ticks: {
	                    beginAtZero:false
	                },
	                display:true
	            }],
	            xAxes: [{
	            	ticks: {
	                    beginAtZero:false
	                },
	                display:true
	            }]
	        },
	        animation: {
	        	duration: 0
	        },
	        responsive: true,
	        maintainAspectRatio: true,
	        title: {
	            display: true,
	            text: namey
	        },
	        elements: {
	        	line: {
			    	fill : false,
			    	borderWidth: 1

			    },
			    point: {
			    	radius : 0
			    }

	        }
	    };
var ctx = document.getElementById(namey).getContext('2d');
var myChart = new Chart(ctx, {
	    type: 'line',
	    options: myOptions,
	    data:{
	    	labels: null,
	    	datasets : []
	    }
	    });
/*
ctx.canvas.width = 1300;
ctx.canvas.height = 600;
windowHeight=$(window).height();
windowWidth=$(window).width();
if (windowWidth>ctx.canvas.width) ctx.canvas.width = windowWidth;
if (windowHeight>ctx.canvas.height) ctx.canvas.height = windowHeight; 
*/
function addDatasets(chart, label, myDatasets) {
    chart.data.labels = label;
    chart.data.datasets=myDatasets;
    
    chart.update();
}
function removeDatasets(chart) {
    chart.data.labels.pop();
    for (i=0;i<chart.data.datasets.length;i++){
    	chart.data.datasets=[];
    }
    chart.update();
}
updateData();
countDown();

var reloadChart = setInterval(updateData,5000);
var countdown = setInterval(countDown,1000);
var countdownVar = 0;
function countDown(){
	countdownVar++;
	$("#countdown").html(countdownVar);
}

function getVolMod(lastPriceData,centerVolData){
	volTop = 0;
	volTop = lastPriceData/1.2;  //500 / 2 = 250
	thisVolTop = centerVolData;  // = 180000 * X = 250
	volMod = volTop/thisVolTop;   // 
	return volMod
	

}

function updateData(){
	
	var bitfinex = false;
	var bitfinex2 = false;
	var kraken = false;
	var kraken2 = false;
	var viabtc = false;
	var viabtc2 = false;
	var bithumb = false;
	var bithumb2 = false;
	var hitbtc = false;
	var hitbtc2 = false;
	var bittrex = false;
	var bittrex2 = false;
	$.ajax({
		url: SITE_URL+"actions/get-volume.php",
		data: {name:namey,sampling:sampling},
		dataType: "json",
		method: "post",
		async: false,
		success: function(json){
			bitfinex = json[0];
			bitfinex2 = json[1];
			kraken = json[2];
			kraken2 = json[3];
			viabtc = json[4];
			viabtc2 = json[5];
			bithumb = json[6];
			bithumb2 = json[7];
			hitbtc = json[8];
			hitbtc2 = json[9];
			bittrex = json[10];
			bittrex2 = json[11];
			total = json['total'];
		},
		error: function (xhr, ajaxOptions, thrownError) {
	    	//alert(xhr.status+ " "+xhr.responseText+" "+thrownError);
	    }
	});
	
	var chartData = [];
	var colorData = [];
	var labelData = [];

	var chartData2 = [];
	var colorData2 = [];
	var labelData2 = [];
	
	var chartDataKr = [];
	var colorDataKr = [];
	var labelDataKr = [];
	var chartDataKr2 = [];
	var colorDataKr2 = [];
	var labelDataKr2 = [];

	var chartDataVia = [];
	var colorDataVia = [];
	var labelDataVia = [];
	var chartDataVia2 = [];
	var colorDataVia2 = [];
	var labelDataVia2 = [];

	var chartDataHumb = [];
	var colorDataHumb = [];
	var labelDataHumb = [];
	var chartDataHumb2 = [];
	var colorDataHumb2 = [];
	var labelDataHumb2 = [];

	var chartDataHit = [];
	var colorDataHit = [];
	var labelDataHit = [];
	var chartDataHit2 = [];
	var colorDataHit2 = [];
	var labelDataHit2 = [];

	var chartDataTrex = [];
	var colorDataTrex = [];
	var labelDataTrex = [];
	var chartDataTrex2 = [];
	var colorDataTrex2 = [];
	var labelDataTrex2 = [];

	var chartLength = total;
	
	var chartMid = parseInt(chartLength/2);
	var volMod = 1;
	
	


	volMod = getVolMod(kraken2[kraken2.length-1]['price'],kraken[chartMid]['volume']);

	for (i=0;i<kraken.length;i++){

		chartDataKr[i] = kraken[i]['volume']*volMod;
		colorDataKr[i] = "rgba(0, 215, 215,1)";
		str = kraken[i]['time_created'].split(" ");
		labelDataKr[i] = str[1];
	}
	for (i=0;i<kraken2.length;i++){
		chartDataKr2[i] = kraken2[i]['price'];
		colorDataKr2[i] = "rgba(0, 215, 215,1)";
		str = kraken2[i]['time_created'].split(" ");
		labelDataKr2[i] = str[1];
	}
	if (kraken.length<chartLength){
		for (i=0;i<chartLength-kraken.length;i++){
			chartDataKr.unshift(0);
			colorDataKr.unshift(colorDataKr[0]);
			labelDataKr.unshift(labelDataKr[0]);
			chartDataKr2.unshift(0);
			colorDataKr2.unshift(colorDataKr2[0]);
			labelDataKr2.unshift(labelDataKr2[0]);
		}
	}



	volMod = getVolMod(bitfinex2[bitfinex2.length-1]['price'],bitfinex[parseInt(bitfinex.length/2)]['volume']);
	
	for (i=0;i<bitfinex.length;i++){
		chartData[i] = bitfinex[i]['volume']*volMod;
		colorData[i] = "rgba(215, 215, 0,1)";
		str = bitfinex[i]['time_created'].split(" ");
		labelData[i] = str[1];


		
	}
	for (i=0;i<bitfinex2.length;i++){
		chartData2[i] = bitfinex2[i]['price'];
		colorData2[i] = "rgba(215, 215, 0,1)";
		str = bitfinex2[i]['time_created'].split(" ");
		labelData2[i] = str[1];
	}



	if (bitfinex.length<chartLength){
		for (i=0;i<chartLength-bitfinex.length;i++){
			chartData.unshift(0);
			colorData.unshift(colorData[0]);
			labelData.unshift(labelData[0]);
			chartData2.unshift(0);
			colorData2.unshift(colorData2[0]);
			labelData2.unshift(labelData2[0]);
		}
	}
	volMod = getVolMod(viabtc2[viabtc2.length-1]['price'],viabtc[parseInt(viabtc.length/2)]['volume']);
	for (i=0;i<viabtc.length;i++){

		chartDataVia[i] = viabtc[i]['volume']*volMod;
		colorDataVia[i] = "rgba(134, 255, 14,1)";
		str = viabtc[i]['time_created'].split(" ");
		labelDataVia[i] = str[1];
	}
	for (i=0;i<viabtc2.length;i++){
		chartDataVia2[i] = viabtc2[i]['price'];
		colorDataVia2[i] = "rgba(134, 255, 14,1)";
		str = viabtc2[i]['time_created'].split(" ");
		labelDataVia2[i] = str[1];
	}
	if (viabtc.length<chartLength){
		for (i=0;i<chartLength-viabtc.length;i++){
			chartDataVia.unshift(0);
			colorDataVia.unshift(colorDataVia[0]);
			labelDataVia.unshift(labelDataVia[0]);
			chartDataVia2.unshift(0);
			colorDataVia2.unshift(colorDataVia2[0]);
			labelDataVia2.unshift(labelDataVia2[0]);
		}
	}

	volMod = getVolMod(bithumb2[bithumb2.length-1]['price'],bithumb[parseInt(bithumb.length/2)]['volume']);
	for (i=0;i<bithumb.length;i++){

		chartDataHumb[i] = bithumb[i]['volume']*volMod;
		colorDataHumb[i] = "rgba(209, 50, 100,1)";
		str = bithumb[i]['time_created'].split(" ");
		labelDataHumb[i] = str[1];
	}
	for (i=0;i<bithumb2.length;i++){
		chartDataHumb2[i] = bithumb2[i]['price'];
		colorDataHumb2[i] = "rgba(209, 50, 100,1)";
		str = bithumb2[i]['time_created'].split(" ");
		labelDataHumb2[i] = str[1];
	}
	if (bithumb.length<chartLength){
		for (i=0;i<chartLength-bithumb.length;i++){
			chartDataHumb.unshift(0);
			colorDataHumb.unshift(colorDataHumb[0]);
			labelDataHumb.unshift(labelDataHumb[0]);
			chartDataHumb2.unshift(0);
			colorDataHumb2.unshift(colorDataHumb2[0]);
			labelDataHumb2.unshift(labelDataHumb2[0]);
		}
	}
	volMod = getVolMod(hitbtc2[hitbtc2.length-1]['price'],hitbtc[parseInt(hitbtc.length/2)]['volume']);
	for (i=0;i<hitbtc.length;i++){

		chartDataHit[i] = hitbtc[i]['volume']*volMod;
		colorDataHit[i] = "rgba(67, 155, 48,1)";
		str = hitbtc[i]['time_created'].split(" ");
		labelDataHit[i] = str[1];
	}
	for (i=0;i<hitbtc2.length;i++){
		chartDataHit2[i] = hitbtc2[i]['price'];
		colorDataHit2[i] = "rgba(67, 155, 48,1)";
		str = hitbtc2[i]['time_created'].split(" ");
		labelDataHit2[i] = str[1];
	}
	if (hitbtc.length<chartLength){
		for (i=0;i<chartLength-hitbtc.length;i++){
			chartDataHit.unshift(0);
			colorDataHit.unshift(colorDataHit[0]);
			labelDataHit.unshift(labelDataHit[0]);
			chartDataHit2.unshift(0);
			colorDataHit2.unshift(colorDataHit2[0]);
			labelDataHit2.unshift(labelDataHit2[0]);
		}
	}

	volMod = getVolMod(bittrex2[bittrex2.length-1]['price'],bittrex[parseInt(bittrex.length/2)]['volume']);
	for (i=0;i<bittrex.length;i++){

		chartDataTrex[i] = bittrex[i]['volume']*volMod;
		colorDataTrex[i] = "rgba(10, 10, 10,1)";
		str = bittrex[i]['time_created'].split(" ");
		labelDataTrex[i] = str[1];
	}
	for (i=0;i<bittrex2.length;i++){
		chartDataTrex2[i] = bittrex2[i]['price'];
		colorDataTrex2[i] = "rgba(10, 10, 10,1)";
		str = bittrex2[i]['time_created'].split(" ");
		labelDataTrex2[i] = str[1];
	}
	if (bittrex.length<chartLength){
		for (i=0;i<chartLength-bittrex.length;i++){
			chartDataTrex.unshift(0);
			colorDataTrex.unshift(colorDataTrex[0]);
			labelDataTrex.unshift(labelDataTrex[0]);
			chartDataTrex2.unshift(0);
			colorDataTrex2.unshift(colorDataTrex2[0]);
			labelDataTrex2.unshift(labelDataTrex2[0]);
		}
	}


	var myDatasets =[
	
		{
            label: 'Volume Kraken',
            data: chartDataKr,
            borderColor: colorDataKr,
            borderDash: [5,5]
        },
        {
            label: 'Price Kraken $'+parseFloat(chartDataKr2[chartDataKr2.length-1]).toFixed(2),
            data: chartDataKr2,
            borderColor: colorDataKr2
        },
		{

            label: 'Volume BitFinex',
            data: chartData,
            borderColor: colorData,
            borderDash: [5,5]
        },
        {
            label: 'Price BitFinex $'+parseFloat(chartData2[chartData2.length-1]).toFixed(2),
            data: chartData2,
            borderColor: colorData2

        },
        
        {
            label: 'Volume ViaBTC',
            data: chartDataVia,
            borderColor: colorDataVia,
            borderDash: [5,5]
        },
        {
            label: 'Price ViaBTC $'+parseFloat(chartDataVia2[chartDataVia2.length-1]).toFixed(2),
            data: chartDataVia2,
            borderColor: colorDataVia2
        },
        {
            label: 'Volume Bithumb',
            data: chartDataHumb,
            borderColor: colorDataHumb,
            borderDash: [5,5]
        },
        {
            label: 'Price Bithumb $'+parseFloat(chartDataHumb2[chartDataHumb2.length-1]).toFixed(2),
            data: chartDataHumb2,
            borderColor: colorDataHumb2
        },
        {
            label: 'Volume HitBTC',
            data: chartDataHit,
            borderColor: colorDataHit,
            borderDash: [5,5]
        },
        {
            label: 'Price HitBTC $'+parseFloat(chartDataHit2[chartDataHit2.length-1]).toFixed(2),
            data: chartDataHit2,
            borderColor: colorDataHit2
        },
        {
            label: 'Volume Bittrex',
            data: chartDataTrex,
            borderColor: colorDataTrex,
            borderDash: [5,5]
        },
        {
            label: 'Price Bittrex $'+parseFloat(chartDataTrex2[chartDataTrex2.length-1]).toFixed(2),
            data: chartDataTrex2,
            borderColor: colorDataTrex2
        }		 	   
	
	];
	removeDatasets(myChart);
	addDatasets(myChart,labelDataKr2,myDatasets);
	    
	countdownVar=0;
	$("#countdown").html(countdownVar);
	countdown = setInterval(countDown,1000);
	
}


});

</script>	
</div>
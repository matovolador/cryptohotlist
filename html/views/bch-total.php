<?php include("../_config.php"); ?>
<div class="panel-body  center-text ">
<?php
$name = "Bitcoin Cash";
$symbol = "BCH";

?>

<canvas class="chart-single" id="<?php echo $name?>" ></canvas>

<script type="text/javascript">

$(document).ready(function(){
var sampling = 1;
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

function updateData(){
	
	
	var bitfinex2 = false;
	
	var kraken2 = false;
	
	var viabtc2 = false;
	
	var bithumb2 = false;
	
	var hitbtc2 = false;
	
	var bittrex2 = false;
	$.ajax({
		url: SITE_URL+"actions/get-historical.php",
		data: {name:namey,sampling:sampling},
		dataType: "json",
		method: "post",
		async: false,
		success: function(json){
			bitfinex2 = json[0];
			kraken2 = json[1];
			viabtc2 = json[2];
			bithumb2 = json[3];
			hitbtc2 = json[4];
			bittrex2 = json[5];
			total = json['total'];
		},
		error: function (xhr, ajaxOptions, thrownError) {
	    	alert(xhr.status+ " "+xhr.responseText+" "+thrownError);
	    }
	});
	

	var chartData2 = [];
	var colorData2 = [];
	var labelData2 = [];
	
	var chartDataKr2 = [];
	var colorDataKr2 = [];
	var labelDataKr2 = [];

	var chartDataVia2 = [];
	var colorDataVia2 = [];
	var labelDataVia2 = [];

	var chartDataHumb2 = [];
	var colorDataHumb2 = [];
	var labelDataHumb2 = [];

	var chartDataHit2 = [];
	var colorDataHit2 = [];
	var labelDataHit2 = [];

	var chartDataTrex2 = [];
	var colorDataTrex2 = [];
	var labelDataTrex2 = [];

	var chartLength = total;
	
	var chartMid = parseInt(chartLength/2);
	var volMod = 1;
	
	

	
	

	
	for (i=0;i<kraken2.length;i++){
		chartDataKr2[i] = kraken2[i]['price'];
		colorDataKr2[i] = "rgba(0, 215, 215,1)";
		labelDataKr2[i] = kraken2[i]['time_created'];
		
	}
	if (kraken2.length<chartLength){
		for (i=0;i<chartLength-kraken2.length;i++){
			chartDataKr2.unshift(0);
			colorDataKr2.unshift(colorDataKr2[0]);
			labelDataKr2.unshift(labelDataKr2[0]);
		}
	}

	for (i=0;i<bitfinex2.length;i++){
		chartData2[i] = bitfinex2[i]['price'];
		colorData2[i] = "rgba(215, 215, 0,1)";
		labelData2[i] = bitfinex2[i]['time_created'];
		
	}
	if (bitfinex2.length<chartLength){
		for (i=0;i<chartLength-bitfinex2.length;i++){
			chartData2.unshift(0);
			colorData2.unshift(colorData2[0]);
			labelData2.unshift(labelData2[0]);
		}
	}
	
	for (i=0;i<viabtc2.length;i++){
		chartDataVia2[i] = viabtc2[i]['price'];
		colorDataVia2[i] = "rgba(134, 255, 14,1)";
		labelDataVia2[i] = viabtc2[i]['time_created'];
		
	}
	if (viabtc2.length<chartLength){
		for (i=0;i<chartLength-viabtc2.length;i++){
			chartDataVia2.unshift(0);
			colorDataVia2.unshift(colorDataVia2[0]);
			labelDataVia2.unshift(labelDataVia2[0]);
		}
	}

	
	for (i=0;i<bithumb2.length;i++){
		chartDataHumb2[i] = bithumb2[i]['price'];
		colorDataHumb2[i] = "rgba(209, 50, 100,1)";
		labelDataHumb2[i] = bithumb2[i]['time_created'];
		
	}
	if (bithumb2.length<chartLength){
		for (i=0;i<chartLength-bithumb2.length;i++){
			chartDataHumb2.unshift(0);
			colorDataHumb2.unshift(colorDataHumb2[0]);
			labelDataHumb2.unshift(labelDataHumb2[0]);
		}
	}
	
	for (i=0;i<hitbtc2.length;i++){
		chartDataHit2[i] = hitbtc2[i]['price'];
		colorDataHit2[i] = "rgba(67, 155, 48,1)";
		labelDataHit2[i] = hitbtc2[i]['time_created'];
		
	}
	if (hitbtc2.length<chartLength){
		for (i=0;i<chartLength-hitbtc2.length;i++){
			chartDataHit2.unshift(0);
			colorDataHit2.unshift(colorDataHit2[0]);
			labelDataHit2.unshift(labelDataHit2[0]);
		}
	}

	
	for (i=0;i<bittrex2.length;i++){
		chartDataTrex2[i] = bittrex2[i]['price'];
		colorDataTrex2[i] = "rgba(10, 10, 10,1)";
		labelDataTrex2[i] = bittrex2[i]['time_created'];
		
	}
	if (bittrex2.length<chartLength){
		for (i=0;i<chartLength-bittrex2.length;i++){
			chartDataTrex2.unshift(0);
			colorDataTrex2.unshift(colorDataTrex2[0]);
			labelDataTrex2.unshift(labelDataTrex2[0]);
		}
	}


	var myDatasets =[
	

		
        {
            label: 'Price Kraken $'+parseFloat(chartDataKr2[chartDataKr2.length-1]).toFixed(2),
            data: chartDataKr2,
            borderColor: colorDataKr2
        },
		
        {
            label: 'Price BitFinex $'+parseFloat(chartData2[chartData2.length-1]).toFixed(2),
            data: chartData2,
            borderColor: colorData2

        },
        
        
        {
            label: 'Price ViaBTC $'+parseFloat(chartDataVia2[chartDataVia2.length-1]).toFixed(2),
            data: chartDataVia2,
            borderColor: colorDataVia2
        },
        
        {
            label: 'Price Bithumb $'+parseFloat(chartDataHumb2[chartDataHumb2.length-1]).toFixed(2),
            data: chartDataHumb2,
            borderColor: colorDataHumb2
        },
        
        {
            label: 'Price HitBTC $'+parseFloat(chartDataHit2[chartDataHit2.length-1]).toFixed(2),
            data: chartDataHit2,
            borderColor: colorDataHit2
        },
        
        {
            label: 'Price Bittrex $'+parseFloat(chartDataTrex2[chartDataTrex2.length-1]).toFixed(2),
            data: chartDataTrex2,
            borderColor: colorDataTrex2
        }		 	   	 	   
	
	];
	//removeDatasets(myChart);
	addDatasets(myChart,labelDataKr2,myDatasets);
	    
	
	
}


});

</script>	
</div>
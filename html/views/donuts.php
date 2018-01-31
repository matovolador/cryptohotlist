<?php include("../_config.php"); ?>
<div class="panel-body  center-text ">
<?php

$CHL = new CryptoHotlist();

$symbols = $CHL->getTopRanks(100,1,25);
for ($h=0;$h<count($symbols);$h++){
	$symbol = $symbols[$h]['symbol'];
	$name = $symbols[$h]['name'];
	$res = $CHL->getPriceChange($name,1);
	?>
	
	<canvas class="donut-single" id="<?php echo $name?>" ></canvas>
	
	<script type="text/javascript">

	$(document).ready(function(){
		var res = <?php echo json_encode($res); ?>;
		res.reverse();
		var symbol = "<?php echo $symbol ?>";
		var namey = "<?php echo $name ?>";
		var myData = [];
		var numberFlag = []
		for (i=0;i<res.length;i++){
			if (res[i]['price_five']<0){
				amount = ""+res[i]['price_five'] * -1;
				numberFlag = "-";
			}else{
				amount = ""+res[i]['price_five'];
				numberFlag = "";
			}
			myData[i] = {price : amount, numberFlag: numberFlag, time: res[i]['time']};	
		}
		

		var chartData = [];
		var colorData = [];
		var labelData = [];
		for (i=0;i<myData.length;i++){
			chartData[i] = myData[i]['price'];
			if (myData[i]['numberFlag']=="-"){
				colorData[i] = "rgba(215, 0, 0,1)";
				labelData[i] = "Time: "+myData[i]['time']+" - Price Dropped %";
			}else if (myData[i]['price']==0){
				colorData[i] = "rgba(210, 210, 210,1)";
				labelData[i] = "Time: "+myData[i]['time']+" Price didn't move %";
			}else{
				colorData[i] = "rgba(0, 215, 0,1)";
				labelData[i] = "Time: "+myData[i]['time']+" Price Climbed %";
			}
			
			
		}
		
		var ctx = document.getElementById(namey).getContext('2d');
		var myChart = new Chart(ctx, {
		    type: 'pie',
		    data: {
		        labels: labelData,
		        datasets: [{
		            label: '# of Votes',
		            data: chartData,
		            backgroundColor: colorData,
		            borderWidth: 0
		        }]
		    },
		    options: {
		    	legend: {
		    		display: false
		    	},
		        scales: {
		            yAxes: [{
		                ticks: {
		                    beginAtZero:true
		                },
		                display:false
		            }],
		            xAxes: [{
		            	ticks: {
		                    beginAtZero:true
		                },
		                display:false
		            }]
		        },
		        cutoutPercentage: 50,
		        responsive: false,
		        title: {
		            display: true,
		            text: namey
		        }
		    }
		});

	});

	</script>	
<?php
}
?>
</div>
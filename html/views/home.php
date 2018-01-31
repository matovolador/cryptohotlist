<?php include("../_config.php"); 
session_start();
if (isset($_GET['ref']) && !isset($_SESSION['ref'])){
	$Refs = new Refs();
	$_SESSION['ref']=$_GET['ref'];
	$Refs->registerRef($_SESSION['ref']);
}
$CHL = new CryptoHotlist();
$symbol = "BTC";
$percent = 0;
$page = 1;
$sample = 1;
$index= 1;
$max = 10;
if (isset($_GET['symbol'])){
	$symbol = $_GET['symbol'];
}
if (isset($_GET['percent'])){
	$percent = $_GET['percent'];
}
if (isset($_GET['sample'])){
	$sample = intval($_GET['sample']);
}
if (isset($_GET['page'])){
	$page = intval($_GET['page']);
}
switch($page){
	case 1:
		$index = 1;
		$max = 25;
		break;
	case 2:
		$index = 26;
		$max = 50;
		break;
	case 3:
		$index = 51;
		$max = 75;
		break;
	case 4:
		$index = 76;
		$max = 100;
		break;
	case 50:
		$index = 1;
		$max = 50;
		break;
	case 100:
		$index = 1;
		$max = 100;
		break;
	default:
		$index = 1;
		$max = 25;
		break;
}



?>

<div class="panel-body">
	<h1>Crypto Hotlist</h1>
	<hr>
	<div class="row my-content-box">
	<div class="col-sm-0 col-md-3"></div>
	<div class="col-sm-12 col-md-6">This is a list of the top most valuable cryptocurrencies (by $ value), there are over 1000 like this and more are being created every day. You can buy and sell many of these on various exchanges. <!--If you like this site or would like us to add stuff please email us on crypto.hotlist@gmx.com--></div>
	<div class="col-sm-0 col-md-3"></div>
	</div>
	
	<div>
	<div id="time"></div>
	<div id="last-api-call">
		<?php
		$res = $CHL->getLastAPICall();
		echo "Last call: ".$res[0]['time_created'];
		?>
	</div>
	<div>Displaying data for the last 24 hours. <a href="#" onclick="reloadApp(); return false;">Reload <i class="fa fa-refresh" aria-hidden="true"></i></a></div>
	</div>
	<div>
	<?php 
	if ($sample==1) {
		echo "<a class='btn btn-primary panel-btn' href='".SITE_URL."?page=".$page."&sample=1' >Sample every 5 minutes</a>";
	}else{
		echo "<a class='btn btn-default panel-btn' href='".SITE_URL."?page=".$page."&sample=1' >Sample every 5 minutes</a>";
	}
	if ($sample==6){
		echo "<a class='btn btn-primary panel-btn' href='".SITE_URL."?page=".$page."&sample=6' >Sample every 30 minutes</a>";
	}else{
		echo "<a class='btn btn-default panel-btn' href='".SITE_URL."?page=".$page."&sample=6' >Sample every 30 minutes</a>";
	}
	if ($sample==12){
		echo "<a class='btn btn-primary panel-btn' href='".SITE_URL."?page=".$page."&sample=12' >Sample every 1 hour</a>";
	}else{
		echo "<a class='btn btn-default panel-btn' href='".SITE_URL."?page=".$page."&sample=12' >Sample every 1 hour</a>";
	}
	?>
	</div>
	<?php 
	$names = $CHL->getTopRanks(100,$index,$max);
	
	echo "<div>Showing ".$index." - ".$max." results.</div>";
	?>
	<div class="row">
	<div class="col-sm-0 col-md-4"></div>
	<div class="col-sm-12 col-md-4">
	<form id="form" action="" method="get" >
	<div class="form-group">
	<label for='page'>Select below to see data for up to 100 of the top 1000+ cryptos.</label>
	<select name="page" class="form-control" onchange="submitPage();">
		<option selected disabled>See more results</option>
		<option value='1'>1 - 25</option>
		<option value='2'>26 - 50</option>
		<option value='3'>50 - 75</option>
		<option value='4'>76 - 100</option>
		<option value='50'>top 50 (Slow)</option>
		<option value='100'>top 100 (Very slow)</option>
	</select>
	<input type="hidden" name="sample" value="<?php echo $sample?>">
	</div>
	</form>
	<div class="col-sm-0 col-md-4"></div>
	</div>
	</div>
	<script type="text/javascript">
		function submitPage(){
  			$("#form").submit();
  		}
	</script>
	<div class="table-desc">Click a cell for more information.</div>
	<?php
	echo "<div>Zoom in or out to see more data on screen.</div>";
	echo "<div class='Zoom' id='ZoomIn'>Zoom In <i class='fa fa-search-plus' aria-hidden='true'></i></div>";
	echo "<div class='Zoom' id='ZoomOut'>Zoom Out <i class='fa fa-search-minus' aria-hidden='true'></i></div>";
	
	echo "<div class='clear-fix'></div>";
	echo "<div class='my-table-container'  id='zoomTarget'>";
	echo "<table class='table table-striped my-table' id='table'>";
	$max_cols = 0;
	for ($h=0;$h<count($names);$h++){
		$res = $CHL->getPriceChange($names[$h]['name'],$sample);
		if ($h==0){
			$max_cols = count($res);
			echo "<thead>";
			echo "<tr>";
			echo "<th onmouseover='showHeaderDetails(0,\"Sort by total market capital value, i.e. total USD$ value of all the cryptocurrency valuation in circulation\");' onmouseout='hideHeaderDetails(0);'>Rank</th>";
			echo "<div class='popup' id='popheader-0' ></div>";
			echo "<th onmouseover='showHeaderDetails(1,\"Full name of specified cryptocurrency\");' onmouseout='hideHeaderDetails(1);'>Name</th>";
			echo "<div class='popup' id='popheader-1' ></div>";
			echo "<th>Currency</th>";
			echo "<th onmouseover='showHeaderDetails(2,\"Price in USD$ for one unit\");' onmouseout='hideHeaderDetails(2);'>Price USD</th>";
			echo "<div class='popup' id='popheader-2' ></div>";
			echo "<th onmouseover='showHeaderDetails(3,\"The volatility of specified cryptocurrency based on the total displayed data\");' onmouseout='hideHeaderDetails(3);'>Volatility</th>";
			echo "<div class='popup' id='popheader-3' ></div>";
			echo "<th onmouseover='showHeaderDetails(4,\"The total % the price moved based on the total displayed data\");' onmouseout='hideHeaderDetails(4);'>Total %</th>";
			echo "<div class='popup' id='popheader-4' ></div>";
			for ($i=0;$i<count($res);$i++){
	
				if ($i==0){
					echo "<th onmouseover='showHeaderDetails(5,\"The amount the price moved in the last X minutes\");' onmouseout='hideHeaderDetails(5);'>Now</th>";
					echo "<div class='popup' id='popheader-5' ></div>";
				}else{
					$minutes = 5 * $i * $sample;
					$hours = 0;
					while($minutes>55){
						$hours++;
						$minutes -= 60;
					}
					if ($hours == 1){
						$timeString = $hours." h";
					}else if($hours > 1){
						$timeString = $hours." h";
					}else if ($hours == 0){
						$timeString = "";
					}
					if ($timeString == ""){
						$timeString = $minutes." m";
					}else{
						$timeString .= " ".$minutes." m";
					}
					echo "<th>".$timeString."</th>";	
				}
				
			}	
			echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
		}
		
		echo "<tr>";
		echo "<td onmouseover='showHeaderDetails(0,\"some text\");' onmouseout='hideHeaderDetails(0);'>".$names[$h]['rank']."</td>";
		echo "<td>".$names[$h]['name']."</td>";
		echo "<td>".$names[$h]['symbol']."</td>";
		echo "<td>$".number_format($res[0]['price_usd'],2)."</td>";
		echo "<td>".$res[0]['vol']."</td>";

		echo "<td class='".$res[0]['style_move']."'>".$res[0]['move']."</td>";
		for ($i=0;$i<count($res);$i++){
			if (count($res)<=$max_cols){
				$res[$i]['price_usd']=number_format((float)$res[$i]['price_usd'],2);
				$res[$i]['price_five']=number_format((float)$res[$i]['price_five'],2);
				$res[$i]['price_half']=number_format((float)$res[$i]['price_half'],2);
				$res[$i]['price_hour']=number_format((float)$res[$i]['price_hour'],2);
				if ($sample==1){
					$price_percent = $res[$i]['price_five'];
					$style = $res[$i]['style_five'];
				}else if($sample==6){
					$price_percent = $res[$i]['price_half'];
					$style = $res[$i]['style_half'];
				}else if ($sample==12){
					$price_percent = $res[$i]['price_hour'];
					$style = $res[$i]['style_hour'];
				}
				
				echo "<td class='".$style." my-table-data' onclick='showDetails(".$i.",".json_encode($res[$i]).",".$sample.")' onmouseout='hideDetails(".$i.")'  >%".$price_percent."</td>";
				echo "<div class='popup' id='popup-".$i."' ></div>";	
				if ($max_cols>count($res) && $i==count($res)-1){
					for ($j=$i+1;$j<$max_cols;$j++){
						echo "<td>-</td>";	
					}
				}
				
			}
			
			
		}
		echo "</tr>";
		
	}
	echo "</tbody>";
	echo "</table>";
	echo "</div>";
	?>
	<hr>
	
	<div class="donations">
	<h5>If you find this site useful please tip us below.</h5>
	<div>BTC - 1LC1wszk6sfthAAkJv9XfZZMznwzeTZr1R</div>
	<div>LTC - LgJ5awULoSFzSqFsht9kzDEG8i6SQXAjfg</div>
	<div>ETH - 0xCEA4136E3485CB7398Bb84d91759711dBf1EB51a</div> 
	</div>
	<div class="space"></div>
	<script type="text/javascript">
	var dataTable;
	$(document).ready(function(){
		var currentTime = setInterval(getCurrentTime,500);	
	    $('#table').dataTable( {
			"searching" : true,
			"paging" : false,
			"lengthChange" :false,
			"info" : false,
			"ordering" : true
		});

	});
	$(document).ready(function(){
		$("footer").show();
	});

	function getCurrentTime(){
		$.ajax({
			url : SITE_URL+"actions/get-date.php",
			type: "post",
			success: function(date){
				$("#time").html("Current Time: "+date);
			}
		});
		
	}

	function reloadApp(){
		window.location = window.location.href;
	}
	
	function showDetails(index,json,samples){
		json = JSON.stringify(json);
		json = JSON.parse(json);
		price_percent = "0.00";
		if (samples == 1){
			price_percent = json['price_five'];
		}else if(samples == 6){
			price_percent = json['price_half'];
		}else if(samples == 12){
			price_percent = json['price_hour'];
		}
		//alert(json);
		$("#popup-"+index).html("<h4>"+json['name']+"</h4>%"+price_percent+"<br />$"+json['price_usd']+"<br />"+json['time']);
		$("#popup-"+index).show();
		if (currentMousePos.x > $(document).width()/2){
			$("#popup-"+index).css({left: currentMousePos.x - $("#popup-"+index).width(),top: currentMousePos.y});	
		}else{
			$("#popup-"+index).css({left: currentMousePos.x,top: currentMousePos.y});	
		}
		
	}
	function hideDetails(index){
		$("#popup-"+index).hide();	
	}

	function showHeaderDetails(index,text){
		$("#popheader-"+index).html(text);
		$("#popheader-"+index).show();
		if (currentMousePos.x > $(document).width()/2){
			$("#popheader-"+index).css({left: currentMousePos.x - $("#popheader-"+index).width(),top: currentMousePos.y});	
		}else{
			$("#popheader-"+index).css({left: currentMousePos.x,top: currentMousePos.y});	
		}
	}
	function hideHeaderDetails(index){
		$("#popheader-"+index).hide();	
	}
	
	var currentMousePos = { x: -1, y: -1 };
	var scale = 1;
    $(document).mousemove(function(event) {
        currentMousePos.x = event.pageX * scale ;
        currentMousePos.y = event.pageY * scale ;
    });

    $("#ZoomIn").click(function(event) {
    	event.preventDefault();
    	$("#zoomTarget").css({"zoom":1,"-moz-transform":1});
    	$(".popup").css({"zoom":1,"-moz-transform":1});
        scale = 1;
        
    });
    $("#ZoomOut").click(function(event) {
    	event.preventDefault();
    	zoom = $("#zoomTarget").css("zoom");
    	if (zoom <= 0.25) return;
		$("#zoomTarget").css({"zoom":zoom-0.25,"-moz-transform":zoom-0.25});
		popup = $(".popup").css("zoom");
		$(".popup").css({"zoom":popup*1.50,"-moz-transform":popup*1.50});
		scale = 1;
    });

	</script>
</div>
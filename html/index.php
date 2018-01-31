<?php include("_config.php");
session_start();
//ROUTING-----
$routes = new Routes();
//echo $route->getCurrentUri();
$viewFile = $routes->getView($routes->getCurrentUri());
//echo $viewFile;
//------------------
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Crypto Hotlist</title>
        <!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		
		<link rel="stylesheet" href="<?php echo SITE_URL ?>css/bootstrap-sandstone.min.css?v=0.0.2" >
		<link rel="stylesheet" href="<?php echo SITE_URL ?>css/peace.css">
		<link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
		<link rel="stylesheet" href="<?php echo SITE_URL ?>css/style.css?v=0.1.2">
		
		<script type="text/javascript">var SITE_URL = "<?php echo SITE_URL;?>"</script>
		<!--JQuery minified -->
		<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
		<script src="<?php echo SITE_URL ?>js/jquery-validation-1.15.0.js"></script>
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
		<script src="https://use.fontawesome.com/c8845d434a.js"></script>
		<script src="<?php echo SITE_URL ?>js/pace.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>
    </head>
    <body>
    <script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-104702132-1', 'auto');
	  ga('send', 'pageview');

	</script>
    <!--Load views in this div -->
    <div id="main-content"></div>
    <!-- -->
    <footer>
		<div class="panel panel-default">
			<div class="panel-footer">
				&copy; Crypto Hotlist 2017
			</div>
		</div>
		
	</footer>
		<script type="text/javascript">
		var viewFile = "<?php echo $viewFile ?>";
		var mainFile = "";
		$(document).ready(function(){

			$("#main-content").load(SITE_URL+"views/"+viewFile);

			
		});

		function signout() {
	      	$.ajax({
	           type: "POST",
	           url: SITE_URL+'actions/logout.php',
	           data:{action:'logout'},
	           success:function(txt) {
	           		alert(txt);
					window.location.reload();
	           }

	      	});
		}
		</script>
	</body>
</html>

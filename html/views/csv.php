<?php include("../_config.php");

$Hit = new HitBTCBot();
$res = $Hit->getCSV("Bitcoin Cash");


$array = $res;


$header= "";
foreach ($array[0] as $key=>$value){
	$header .= "$key,";
}
$header=substr($header, 0, -1);
$header.="\n";

$contents = "";

for ($i=0;$i<count($array);$i++){
	foreach ($array[$i] as $key=>$value){
		$contents .= $value.",";
	}
	$contents=substr($contents, 0, -1);	
	$contents .="\n";
}
$fp = fopen('../files/data.csv', 'w');
fwrite($fp,$header);
fwrite($fp,$contents);
fclose($fp);

?>
<script type="text/javascript">
$(document).ready(function(){
	window.location=SITE_URL+"files/data.csv";
});


</script>
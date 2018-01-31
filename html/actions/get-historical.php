<?php include("../_config.php");

if (!isset($_POST['name'])) die("bad params");
$KrakenBot = new KrakenBot();
$BFbot = new BitfinexBot();
$ViaBTCBot = new ViaBTCBot();
$BithumbBot = new BithumbBot();
$HitBTCBot = new HitBTCBot();
$BittrexBot = new BittrexBot();



//$sample = intval($total*100/SAMPLE_TIME_CHART);
$sample= 360;


$res1 = $BFbot->getHistorical($_POST['name'],$sample);


$res2 = $KrakenBot->getHistorical($_POST['name'],$sample);

$res3 = $ViaBTCBot->getHistorical($_POST['name'],$sample);


$res4 = $BithumbBot->getHistorical($_POST['name'],$sample);


$res5 = $HitBTCBot->getHistorical($_POST['name'],$sample);


$res6 = $BittrexBot->getHistorical($_POST['name'],$sample);


//$totals = array($KrakenBot->getLastId($_POST['name']),$BFbot->getLastId($_POST['name']),$ViaBTCBot->getLastId($_POST['name']),$BithumbBot->getLastId($_POST['name']),$HitBTCBot->getLastId($_POST['name']),$BittrexBot->getLastId($_POST['name']));



$totals = array(count($res1),count($res2),count($res3),count($res4),count($res5),count($res6));
$max = max($totals);


$total=$max;

$array = [0 =>$res1,1=>$res2,2=>$res3, 3=>$res4,4=>$res5, 5=>$res6,"total"=>$total];
echo json_encode($array);

<?php include("../_config.php");

if (!isset($_POST['name'])) die("bad params");
$BFbot = new BitfinexBot();
$KrakenBot = new KrakenBot();
$ViaBTCBot = new ViaBTCBot();
$BithumbBot = new BithumbBot();
$HitBTCBot = new HitBTCBot();
$BittrexBot = new BittrexBot();

$res = $BFbot->getVolumes($_POST['name'],$_POST['sampling']);
$res2 = $BFbot->getPrices($_POST['name'],$_POST['sampling']);

$res3 = $KrakenBot->getVolumes($_POST['name'],$_POST['sampling']);
$res4 = $KrakenBot->getPrices($_POST['name'],$_POST['sampling']);

$res5 = $ViaBTCBot->getVolumes($_POST['name'],$_POST['sampling']);
$res6 = $ViaBTCBot->getPrices($_POST['name'],$_POST['sampling']);

$res7 = $BithumbBot->getVolumes($_POST['name'],$_POST['sampling']);
$res8 = $BithumbBot->getPrices($_POST['name'],$_POST['sampling']);

$res9 = $HitBTCBot->getVolumes($_POST['name'],$_POST['sampling']);
$res10 = $HitBTCBot->getPrices($_POST['name'],$_POST['sampling']);

$res11 = $BittrexBot->getVolumes($_POST['name'],$_POST['sampling']);
$res12 = $BittrexBot->getPrices($_POST['name'],$_POST['sampling']);

$totals = array(count($res),count($res2),count($res3),count($res4),count($res5),count($res6),count($res7),count($res8),count($res9),count($res10),count($res11),count($res12));
$max = max($totals);


$array = [0 =>$res,1=>$res2,2=>$res3, 3=>$res4,4=>$res5, 5=>$res6,6=>$res7,7=>$res8,8=>$res9,9=>$res10,10=>$res11,11=>$res12,"total"=>$max];
echo json_encode($array);

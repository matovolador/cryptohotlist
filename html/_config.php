<?php
$env = "pub";


if ($env == "dev"){
	//development:
	//LOCAL
	define('DB_HOST', "localhost");
	define('DB_NAME', "crypto_hotlist");
	define('DB_USER', "root");
	define('DB_PASS', "secret");
	ini_set("display_errors",true);
	error_reporting(-1);
	define("SITE_URL","http://".$_SERVER["HTTP_HOST"]."/cryptohotlist/html/");	
}else if ($env=="pub"){
	//publishing:
	//Server:
	error_reporting(E_ERROR);

	define('DB_HOST', "localhost");
	define('DB_NAME', "crypto_hotlist");
	define('DB_USER', "root");
	define('DB_PASS', "secret");
	define("SITE_URL","https://".$_SERVER["HTTP_HOST"]."/");
}


define("SAMPLE_TIME_CHART",2000);

define("SAMPLE_TIME_CHART_TOTAL",5000);




include_once("classes/Routes.php");
include_once("classes/PDOdb.php");
include_once("classes/CryptoHotlist.php");
include_once("classes/Refs.php");
include_once("classes/BitfinexBot.php");
include_once("classes/KrakenBot.php");
include_once("classes/ViaBTCBot.php");
include_once("classes/BithumbBot.php");
include_once("classes/HitBTCBot.php");
include_once("classes/BittrexBot.php");
?>
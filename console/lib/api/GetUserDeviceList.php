<?php

include $_SERVER['DOCUMENT_ROOT'] .  '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/UserDeviceDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/LogDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $json_path . "/JSONObject.php";


$username='';
$deviceList;

session_start();
if (isset($_SESSION['login_obj'])) {
	$login_obj = unserialize($_SESSION['login_obj']);
	$username = $login_obj ->resultObj->username;
	$platform = 'WEB';
	$deviceList = getUserDeviceList($username, $platform);
	saveNewLog("API","GetUserDeviceList",$username,$platform,"");
}else{
	$response=new Response();
	$response -> resultCode = 'KO';
	$response -> message = 'Utente non loggato';
	$response -> errorDescription = 'KO_LOGIN';
	$deviceList=$response;
} 



$jsonString = json_encode($deviceList);
echo $jsonString;
?>
<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/SchedulingDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $json_path . "/JSONObject.php";

$username;
$deviceId='';
$typeList;

if (isset($_POST['deviceId'])) {
	$deviceId=$_POST['deviceId'];
}

session_start();
if (isset($_SESSION['login_obj'])) {
	$login_obj = unserialize($_SESSION['login_obj']);
	$username = $login_obj ->resultObj->username;	
	$platform = 'WEB';
	$typeList = getSchedulingType($username, $deviceId,$platform);
	
}else{
	$response=new Response();
	$response -> resultCode = 'KO';
	$response -> message = 'Utente non loggato';
	$response -> errorDescription = 'KO_LOGIN';
	$typeList=$response;
} 

$jsonString = json_encode($typeList);
echo $jsonString;
?>
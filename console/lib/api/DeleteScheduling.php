<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/SchedulingDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/LogDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $json_path . "/JSONObject.php";

$username;
$schedulingId='';
$resultDelete;
$deviceId='';

if (isset($_POST['deviceId'])) {
	$deviceId=$_POST['deviceId'];
}
if (isset($_POST['schedulingId'])) {
	$schedulingId=$_POST['schedulingId'];
}

session_start();
if (isset($_SESSION['login_obj'])) {
	$login_obj = unserialize($_SESSION['login_obj']);
	$username = $login_obj ->resultObj->username;	
	$platform = 'WEB';
	$resultDelete = deleteSchedulingId($username, $schedulingId,$deviceId, $platform);
	saveNewLog("API","DeleteScheduling: ".$schedulingId,$username,$platform,$deviceId);
}else{
	$response=new Response(); 	
	$response -> resultCode = 'KO';
	$response -> message = 'Utente non loggato';
	$response -> errorDescription = 'KO_LOGIN';
	$resultDelete=$response;
} 

$jsonString = json_encode($resultDelete);
echo $jsonString;
?>
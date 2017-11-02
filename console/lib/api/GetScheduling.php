<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/SchedulingDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/LogDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $json_path . "/JSONObject.php";

$username;

$deviceId='';

$deviceList;

if (isset($_POST['deviceId'])) {
	$deviceId=$_POST['deviceId'];
}

session_start();
if (isset($_SESSION['login_obj'])) {
	$login_obj = unserialize($_SESSION['login_obj']);
	$username = $login_obj ->resultObj->username;	
	$platform = 'WEB';
	$deviceList = getSchedulingList($username, $deviceId,$platform);
	saveNewLog("API","GetScheduling",$username,$platform,$deviceId);
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
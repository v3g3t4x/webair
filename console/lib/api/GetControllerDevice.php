<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/ControllerDeviceDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/LogDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $json_path . "/JSONObject.php";
$username='';
$deviceId='';
$controllerDeviceList;

if (isset($_POST['deviceId'])) {
	$deviceId=$_POST['deviceId'];
}
session_start();
if (isset($_SESSION['login_obj'])) {
	$login_obj = unserialize($_SESSION['login_obj']);
	$username = $login_obj ->resultObj->username;	
	$platform = 'WEB';
	$controllerDeviceList= getControllerDevice($username, $deviceId,$platform);
	saveNewLog("API","GetControllerDevice",$username,$platform,$deviceId);
}else{
	$response=new Response();
	$response -> resultCode = 'KO';
	$response -> message = 'Utente non loggato';
	$response -> errorDescription = 'KO_LOGIN';
	$controllerDeviceList=$response;
} 

$jsonString = json_encode($controllerDeviceList);
echo $jsonString;
?>
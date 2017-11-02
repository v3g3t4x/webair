<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/StatusDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/LogDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $json_path . "/JSONObject.php";

$username;
$deviceId='';
$statusObj;
session_start();
if (isset($_SESSION['login_obj'])) {
	$deviceId=$_POST['deviceId'];
	$login_obj = unserialize($_SESSION['login_obj']);
	$username = $login_obj ->resultObj->username;	
	$platform = 'WEB';
	$statusObj = getStatus($username, $deviceId, $platform);
	saveNewLog("API","GetStatus",$username,$platform,$deviceId);
}else{
	$response=new Response();
	$response -> resultCode = 'KO';
	$response -> message = 'Utente non loggato';
	$response -> errorDescription = 'KO_LOGIN';
	$statusObj=$response;
} 


$jsonString = json_encode($statusObj);
echo $jsonString;
?>
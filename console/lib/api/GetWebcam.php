<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/MediaDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/LogDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $json_path . "/JSONObject.php";
$deviceId='';
if (isset($_POST['deviceId'])) {
	$deviceId=$_POST['deviceId'];
}

$username;
session_start();
if (isset($_SESSION['login_obj'])) {
	$login_obj = unserialize($_SESSION['login_obj']);
	$username = $login_obj ->resultObj->username;	
	$platform = 'WEB';
	$mediaList = getMediaList($username, $deviceId,$platform);
	saveNewLog("API","GetWebcam",$username,$platform,$deviceId);
}else{
	$response=new Response();
	$response -> resultCode = 'KO';
	$response -> message = 'Utente non loggato';
	$response -> errorDescription = 'KO_LOGIN';
	$mediaList=$response;
} 




$jsonString = json_encode($mediaList);
echo $jsonString;
?>
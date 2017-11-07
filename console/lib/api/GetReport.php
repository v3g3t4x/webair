<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/ReportDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/LogDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $json_path . "/JSONObject.php";

if (isset($_POST['deviceId'])) {
	$deviceId=$_POST['deviceId'];
}

if (isset($_POST['reportType'])) {
	$deviceId=$_POST['reportType'];
}

if (isset($_POST['platform'])) {
	$platform=$_POST['platform'];
}

session_start();
if (isset($_SESSION['login_obj'])) {
	$login_obj = unserialize($_SESSION['login_obj']);
	$username = $login_obj ->resultObj->username;
	$platform = 'WEB';
	$reportObj=getReport($deviceId, $reportType, $platform);
	saveNewLog("API","GetReport",$username,$platform,$deviceId);
}else{
	$response=new Response();
	$response -> resultCode = 'KO';
	$response -> message = 'Utente non loggato';
	$response -> errorDescription = 'KO_LOGIN';
	$reportObj=$response;
}

$jsonString = json_encode($reportObj);
echo $jsonString;
?>
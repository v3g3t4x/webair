<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/StatusDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/LogDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $json_path . "/JSONObject.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/UserDeviceDAO.php";


$platform='';
$privateIp='';
$deviceId='';
$uniqueId='';
$crypt='';
$externalTemp='';
$internalHum='';
$location='';
$internalTemp='';
$publicIp='';
$nameStatus='';
$passPhrase='';


if (isset($_GET['platform'])) {
	$platform=$_GET['platform'];
}
if (isset($_GET['privateIp'])) {
	$privateIp=$_GET['privateIp'];
}
if (isset($_GET['deviceId'])) {
	$deviceId=$_GET['deviceId'];
}
if (isset($_GET['uniqueId'])) {
	$uniqueId=$_GET['uniqueId'];
}
if (isset($_GET['crypt'])) {
	$crypt=$_GET['crypt'];
}
if (isset($_GET['externalTemp'])) {
	$externalTemp=$_GET['externalTemp'];
}
if (isset($_GET['externalHum'])) {
	$externalHum=$_GET['externalHum'];
}
if (isset($_GET['internalHum'])) {
	$internalHum=$_GET['internalHum'];
}
if (isset($_GET['location'])) {
	$location=$_GET['location'];
}
if (isset($_GET['internalTemp'])) {
	$internalTemp=$_GET['internalTemp'];
}

if (isset($_GET['publicIp'])) {
	$publicIp=$_GET['publicIp'];
}

if (isset($_GET['nameStatus'])) {
	$nameStatus=$_GET['nameStatus'];
}
$secretKey=getSecretKeyForDevice($deviceId, $platform);
echo "\n\nSECRET_KEY RECUPERATO DA DB: "+$secretKey+" \n";
//Comparo crypt ricevuto con crypt generato in locale...se uguale procedo...
echo "\n\nCOMPARAZIONE\n";
echo "VALORE DB: ".$secretKey."\n";
echo "VALORE RICEVUTO: ".$crypt."\n";
if (strcmp($crypt, $secretKey)==0) {
	$platform = 'WEB';
	$resultSaveStatus = saveStatus($platform,$uniqueId,$deviceId,$privateIp,$publicIp,$crypt,$internalTemp,$externalTemp,$internalHum,$location,$nameStatus,$externalHum);
	saveNewLog("API","SetStatus","AUTO",$platform,$deviceId); 	
}else{
	$response=new Response(); 	
	$response -> resultCode = 'KO';
	$response -> message = 'Tentativo di intrusione. Indirizzo IP tracciato, verrai segnalato!';
	$response -> errorDescription = 'KO_LOGIN';
	$resultSaveStatus=$response;
} 

$jsonString = json_encode($resultSaveStatus);
echo $jsonString;

?>
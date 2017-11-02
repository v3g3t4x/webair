<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/StatusDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/LogDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $json_path . "/JSONObject.php";



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



//Recupero la stringa segreta di questo device..
$secretValue = file_get_contents('/webair_support/secret_raspberry/secret.key', true);

echo "KEY--->".$secretValue;
//Genero crypt concatenando uniqueId+deviceId+segreto
$concatenazione=$uniqueId . $deviceId . $secretValue;
echo "**".$concatenazione."**";
//Aggiungo /n perchè la versione bash lo fa e quindi altrimenti non sarebbero gli stessi valori
$cryptGenerato=hash('sha256', $concatenazione);


//Comparo crypt ricevuto con crypt generato in locale...se uguale procedo...
echo "\n\nCOMPARAZIONE\n";
echo "VALORE GENERATO: ".$cryptGenerato."\n";
echo "VALORE RICEVUTO: ".$crypt."\n";
//if (strcmp($crypt, $cryptGenerato)==0) {
	
	$platform = 'WEB';
	$resultSaveStatus = saveStatus($platform,$uniqueId,$deviceId,$privateIp,$publicIp,$crypt,$internalTemp,$externalTemp,$internalHum,$location,$nameStatus,$externalHum);
	saveNewLog("API","SetStatus","AUTO",$platform,$deviceId); 	
//}else{
//	$response=new Response(); 	
//	$response -> resultCode = 'KO';
//	$response -> message = 'Tentativo di intrusione. Indirizzo IP tracciato, verrai segnalato!';
//	$response -> errorDescription = 'KO_LOGIN';
//	$resultSaveStatus=$response;
//} 

$jsonString = json_encode($resultSaveStatus);
echo $jsonString;

?>
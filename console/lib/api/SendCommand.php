<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $util_path . "/BashWrapper.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $json_path . "/JSONObject.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/LogDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/UserDeviceDAO.php";

$username;
session_start();
if (isset($_SESSION['login_obj'])) {
	$login_obj = unserialize($_SESSION['login_obj']);
	$username = $login_obj -> resultObj -> username;
	$platform = 'WEB';
	$commandId = $_POST['commandId'];
	$deviceId = $_POST['deviceId'];
	$resutComman = runCommand($username, $deviceId, $commandId);
	saveNewLog("API","SendCommand: ".$commandId,$username,$platform,$deviceId);
} else {
	$secretKey=getSecretKeyForDevice($deviceId, $platform);
	echo "\n\nSECRET_KEY RECUPERATO DA DB: "+$secretKey+" \n";
	$crypt=$_POST['crypt'];//Recupero da parametri input....
	echo "\n\nCOMPARAZIONE\n";
	echo "VALORE da input: " . $crypt. "\n";
	echo "VALORE da db: " . $secretKey. "\n";
	if (strcmp($crypt, $secretKey) == 0) {
		$platform = 'WEB';
		$username = $login_obj -> resultObj -> username;
		$commandId = $_POST['commandId'];
		$username = $_POST['username'];
		$deviceId = $_POST['deviceId'];
		$resutComman = runCommand($username, $deviceId, $commandId);
		saveNewLog("API","SendCommand: ".$commandId,"AUTO",$platform,$deviceId);
	} else {
		$response = new Response();
		$response -> resultCode = 'KO';
		$response -> message = 'Tentativo di intrusione. Indirizzo IP tracciato, verrai segnalato!';
		$response -> errorDescription = 'KO_LOGIN';
		$resultSaveStatus = $response;
	}
}

$jsonString = json_encode($resutComman);
echo $jsonString;
?>
<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $util_path . "/BashWrapper.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $json_path . "/JSONObject.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/LogDAO.php";

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
	//Recupero la stringa segreta di questo device..
	$secretValue = file_get_contents('/webair_support/secret_raspberry/secret.key', true);
	echo "KEY--->" . $secretValue;
	//Genero crypt concatenando uniqueId+deviceId+segreto
	$concatenazione = $uniqueId . $deviceId . $secretValue;
	echo "**" . $concatenazione . "**";
	//Aggiungo /n perchè la versione bash lo fa e quindi altrimenti non sarebbero gli stessi valori
	$cryptGenerato = hash('sha256', $concatenazione);
	//Comparo crypt ricevuto con crypt generato in locale...se uguale procedo...
	echo "\n\nCOMPARAZIONE\n";
	echo "VALORE GENERATO: " . $cryptGenerato . "\n";
	echo "VALORE RICEVUTO: " . $crypt . "\n";
	//if (strcmp($crypt, $cryptGenerato) == 0) {
		$platform = 'WEB';
		$username = $login_obj -> resultObj -> username;
		$platform = 'WEB';
		$commandId = $_POST['commandId'];
		$username = $_POST['username'];
		$deviceId = $_POST['deviceId'];
		$resutComman = runCommand($username, $deviceId, $commandId);
		saveNewLog("API","SendCommand: ".$commandId,"AUTO",$platform,$deviceId);
	//} else {
		//$response = new Response();
		//$response -> resultCode = 'KO';
		//$response -> message = 'Tentativo di intrusione. Indirizzo IP tracciato, verrai segnalato!';
		//$response -> errorDescription = 'KO_LOGIN';
		//$resultSaveStatus = $response;
	//}
}

$jsonString = json_encode($resutComman);
echo $jsonString;
?>
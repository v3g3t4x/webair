<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/AllCommandDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/LogDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $json_path . "/JSONObject.php";
$username='';
$ruolo='';
$commandList;
session_start();
if (isset($_SESSION['login_obj'])) {
	$login_obj = unserialize($_SESSION['login_obj']);
	$username = $login_obj ->resultObj->username;	
	$ruolo =$login_obj ->resultObj->ruolo;
	$platform = $_POST['platform'];
	$commandList = getAllCommandAdmin($username, $ruolo, $platform);
	saveNewLog("API","GetAllCommandAdmin",$username,$platform,"");
}else{	
	$response=new Response();
	$response -> resultCode = 'KO';
	$response -> message = 'Utente non loggato';
	$response -> errorDescription = 'KO_LOGIN';
	$commandList=$response;
} 

$jsonString = json_encode($commandList);
echo $jsonString;
?>
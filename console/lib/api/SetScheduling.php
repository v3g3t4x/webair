<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/SchedulingDAO.php";

require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/LogDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $json_path . "/JSONObject.php";

$shutdown="";

if (isset($_POST['username'])) {
	$username=$_POST['username'];
}
if (isset($_POST['deviceId'])) {
	$deviceId=$_POST['deviceId'];
}
if (isset($_POST['commandId'])) {
	$commandId=$_POST['commandId'];
}
if (isset($_POST['schedulingType'])) {
	$schedulingType=$_POST['schedulingType'];
}
if (isset($_POST['schedulingValue'])) {
	$schedulingValue=$_POST['schedulingValue'];
}
if (isset($_POST['startTime'])) {
	$startTime=$_POST['startTime'];
}
if (isset($_POST['endTime'])) {
	$endTime=$_POST['endTime'];
}
if (isset($_POST['validityStart'])) {
	$validityStart=$_POST['validityStart'];
}
if (isset($_POST['validityEnd'])) {
	$validityEnd=$_POST['validityEnd'];
}
if (isset($_POST['priority'])) {
	$priority=$_POST['priority'];
}
if (isset($_POST['description'])) {
	$description=$_POST['description'];
}
if (isset($_POST['platform'])) {
	$platform=$_POST['platform'];
}
if (isset($_POST['shutdown'])) {
	$shutdown=$_POST['shutdown'];
}

session_start();
if (isset($_SESSION['login_obj'])) {
	$login_obj = unserialize($_SESSION['login_obj']);
	$username = $login_obj ->resultObj->username;	
	$platform = 'WEB';
	
	//$isMyDevice=checkDeviceOfUser($username, $deviceId, $platform); 
	$isMyDevice=True;
	if(!$isMyDevice){
		$response=new Response(); 	
		$response -> resultCode = 'KO';
		$response -> message = 'DeviceId not recognized';
		$response -> errorDescription = 'KO_DEVICE_ID';
		$resultSaveNew=$response;
	}else{
		$resultSaveNew = saveNewScheduling($username,$deviceId,$commandId,$schedulingType,$schedulingValue,$startTime,$endTime,$validityStart,$validityEnd,$priority,$description,$platform,$shutdown);
		saveNewLog("API","SetScheduling",$username,$platform,$deviceId);
		//Se è stato selezionato il check SPEGNI ALLA FINE devo creare un'altra schedulazione...
		if( strcmp($shutdown,"true") == 0){
			$resultSaveNew = saveNewSchedulingSpegnimento($username,$deviceId,"COMMAND_ALL_OFF",$schedulingType,$schedulingValue,$startTime,$endTime,$validityStart,$validityEnd,$priority,$description . " (Spegnimento)",$platform,$shutdown);
			saveNewLog("API","SetScheduling Spegnimento",$username,$platform,$deviceId);	
		}
	}//End if else...mydevice
}else{
	$response=new Response(); 	
	$response -> resultCode = 'KO';
	$response -> message = 'Utente non loggato';
	$response -> errorDescription = 'KO_LOGIN';
	$resultSaveNew=$response;
} 

$jsonString = json_encode($resultSaveNew);
echo $jsonString;

//$jsonString = "{\"errorDescription\":\"\",\"message\":\"Schedulazione salvata correttamente\",\"resultCode\":\"OK\",\"resultObj\":\"\",\"systemTime\":1350051393}";
//echo $jsonString;
?>
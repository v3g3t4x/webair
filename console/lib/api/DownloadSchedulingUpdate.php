<?php
include $_SERVER['DOCUMENT_ROOT'].'/config.php';
include $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . '/DBConnection.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $json_path . "/JSONObject.php";

$platform = 'WEB';
$username =$_GET['username'];
$deviceId =$_GET['deviceId'];

$link = GET_DB_CONNECTION();
$result = $link -> prepare('SELECT ID,DEVICE_ID, ID_COMMAND,SCHEDULING_TYPE, SCHEDULING_VALUE, START_TIME,END_TIME,VALIDITY_RANGE_START,VALIDITY_RANGE_END,PRIORITY,DESCRIPTION,USERNAME,IS_ENABLED,CREATION_DATE,UPDATE_DATE FROM WEBAIR_DB.AIR_SCHEDULING WHERE DEVICE_ID=:deviceId');
$result -> bindValue(':deviceId', $deviceId);
$result -> execute(); 
if (!$result) die('Couldn\'t fetch records' );
	$headers = array();
	$fp = fopen('php://output', 'w');
	if ($fp && $result){
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="export.csv"');
		header('Pragma: no-cache');
		header('Expires: 0');
		$rows = $result -> fetchall(PDO::FETCH_ASSOC);
		foreach ($rows as $row){
			fputcsv($fp, $row);
		}
		fclose($fp);
		die;
	}
?>
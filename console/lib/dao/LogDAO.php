<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
//include $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . '/DBConnection.php';
//require $_SERVER['DOCUMENT_ROOT'] . "/" . $bean_path . '/Response.php';

function saveNewLog($logType,$event,$username,$platform,$deviceId) {
	
	$link = GET_DB_CONNECTION();
	$result = $link -> prepare("insert into WEBAIR_DB.AIR_LOG(LOG_TYPE,EVENT,USERNAME,PLATFORM,DEVICE_ID)  values(:logType,:event,:username,:platform,:deviceId);");
	$result -> bindValue(':logType', $logType);
	$result -> bindValue(':event', $event);
	$result -> bindValue(':username', $username);
	$result -> bindValue(':platform', $platform);
	$result -> bindValue(':deviceId', $deviceId);
	$result -> execute();
	return ("LOG");
}
?>
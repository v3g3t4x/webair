<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . '/DBConnection.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $bean_path . '/Response.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $bean_path . '/UserDeviceResponse.php';

function getUserDeviceList($username, $platform) {

	$deviceList = new Response();
	$deviceList -> resultCode = 'KO';
	$deviceList -> errorDescription = 'Generic Error';
	if (empty($username) || empty($platform)) {
		$deviceList -> errorDescription = 'KO_INPUT_MANCANTI';
		$deviceList -> message = 'Parametri di input mancanti';
	} else {
		$link = GET_DB_CONNECTION();
		$result = $link -> prepare("SELECT device.ID_DEVICE, device.NAME_DEVICE, device.DESCRIPTION, device.UNIQUE_WWW_ID FROM WEBAIR_DB.AIR_USER user, WEBAIR_DB.AIR_USER_DEVICE user_device,  WEBAIR_DB.AIR_DEVICE device WHERE device.ID_DEVICE=user_device.ID_DEVICE and user.USERNAME=user_device.USERNAME and user.USERNAME=:username");
		$result -> bindValue(':username', $username);
		$result -> execute();
		if (!$result) {
			$deviceList -> errorDescription = 'KO_DB_INTERACTION';
			$deviceList -> message = 'Impossibile interagire con il DB';
		} else {
			$num_rows = $result -> rowCount();
			$deviceList -> resultObj = array();
			if ($num_rows > 0) {
				
				$rows = $result -> fetchAll();
				foreach ($rows as $row) {
					$userDeviceObj = new UserDeviceResponse();
					$userDeviceObj -> id = $row['ID_DEVICE'];
					$userDeviceObj -> nameDevice = $row['NAME_DEVICE'];
					$userDeviceObj -> uniqueId = $row['UNIQUE_WWW_ID'];
					$userDeviceObj -> description = $row['DESCRIPTION'];
					
					array_push($deviceList -> resultObj, $userDeviceObj);
				}
				$deviceList -> resultCode = 'OK';
				$deviceList -> errorDescription = '';
				$deviceList -> message = 'Lista dispositivi recuperata correttamente';
			}else{
				$deviceList -> resultCode = 'OK';
				$deviceList -> errorDescription = 'NO_USER_DEVICE';
				$deviceList -> message = 'Nessun dispositivo associato a questo account';
			}
		}
	}
	return ($deviceList);
}



function getSecretKeyForDevice($deviceId, $platform) {
	$secretKeyOutput ='KO';
	if (!empty($username) && !empty($platform) && !empty($deviceId)) {
		$link = GET_DB_CONNECTION();	
		$result = $link -> prepare("SELECT device.SECRET_KEY FROM WEBAIR_DB.AIR_DEVICE device WHERE device.ID_DEVICE=:deviceId");
		$result -> bindValue(':deviceId', $deviceId);
		$result -> execute();
		$num_rows = $result -> rowCount();
		$deviceList -> resultObj = array();
		if ($num_rows > 0) {
			$rows = $result -> fetchAll();
			foreach ($rows as $row) {
				$secretKeyOutput =  $row['SECRET_KEY'];
			}
		}
	}
	return ($secretKeyOutput);
}

function checkDeviceOfUser($username, $deviceId, $platform) {
	if (empty($username) || empty($platform) || empty($deviceId)) {
		return(false);
	} else {
		$link = GET_DB_CONNECTION();
		$result = $link -> prepare("SELECT device.ID_DEVICE, device.NAME_DEVICE, device.DESCRIPTION, device.UNIQUE_WWW_ID FROM WEBAIR_DB.AIR_USER user, WEBAIR_DB.AIR_USER_DEVICE user_device,  WEBAIR_DB.AIR_DEVICE device WHERE device.ID_DEVICE=user_device.ID_DEVICE and user.USERNAME=user_device.USERNAME and user.USERNAME=:username and user_device.ID_DEVICE=:deviceId");
		$result -> bindValue(':username', $username);
		$result -> bindValue(':deviceId', $deviceId);
		$result -> execute();
		if (!$result) {
			return(false);
		} else {
			$num_rows = $result -> rowCount();
			if ($num_rows > 0) {
				return(true);
			}else{
				return(false);
			}
		}
	}
	return (false);
}
?>
<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . '/DBConnection.php';
include $_SERVER['DOCUMENT_ROOT'] . "/" . $bean_path . '/StatusResult.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $bean_path . '/Response.php';


function getStatus($username, $deviceId, $platform) {
	$tempSymbol=" &deg;C";//Recuperarle in futuro da config
	$humSymbol=" %";//Recuperarle in futuro da config
	$statusObj = new Response();
	$statusObj -> resultCode = 'KO';
	$statusObj -> errorDescription = 'Generic Error';
	if (empty($username) || empty($deviceId) || empty($platform)) {
		$statusObj -> message = 'Parametri di input mancanti';
		$statusObj -> errorDescription = 'KO_INPUT_MANCANTI';
	} else {//Se i parametri non sono vuoti...
		$link = GET_DB_CONNECTION();
		$result = $link -> prepare("SELECT device.ID_DEVICE, device.NAME_DEVICE,status.ID_STATUS,status.NAME_STATUS,status.DESCRIPTION,device.UNIQUE_WWW_ID,device.PUBLIC_IP,device.PRIVATE_IP,deviceStatus.INTERNAL_TEMP,deviceStatus.INTERNAL_HUM,deviceStatus.EXTERNAL_TEMP ,deviceStatus.EXTERNAL_HUM,device.LOCATION FROM WEBAIR_DB.AIR_USER user,WEBAIR_DB.AIR_USER_DEVICE user_device, WEBAIR_DB.AIR_DEVICE device,WEBAIR_DB.AIR_DEVICE_STATUS deviceStatus,WEBAIR_DB.AIR_STATUS status WHERE device.ID_DEVICE=user_device.ID_DEVICE and user.USERNAME=user_device.USERNAME and deviceStatus.ID_DEVICE=device.ID_DEVICE and status.ID_STATUS=deviceStatus.ID_STATUS and user.USERNAME=:username and device.ID_DEVICE=:deviceId");
		$result->bindValue(':deviceId',  $deviceId);
		$result->bindValue(':username',  $username);
		$result -> execute();
		if (!$result) {
			$statusObj -> errorDescription = 'KO_DB_INTERACTION';
			$statusObj -> message = 'Impossibile interagire con il DB';
		} else {
			$num_rows = $result->rowCount();
			if ($num_rows > 0) {
				$row =$result->fetch(PDO::FETCH_ASSOC);
				$statusRes = new StatusResult();
				$statusRes -> deviceId = $row['ID_DEVICE'];
				$statusRes -> deviceName = $row['NAME_DEVICE'];
				$statusRes -> description = $row['DESCRIPTION'];
				$statusRes->externalHum = $row['EXTERNAL_HUM'] . $humSymbol;
				$statusRes-> externalTemp = $row['EXTERNAL_TEMP'] . $tempSymbol;
				$statusRes-> geolocation = $row['LOCATION'];
				$statusRes-> internalHum = $row['INTERNAL_HUM'] . $humSymbol;
				$statusRes-> internalTemp = $row['INTERNAL_TEMP'] . $tempSymbol;
				$statusRes-> privateIp = $row['PRIVATE_IP'];
				$statusRes-> publicIp = $row['PUBLIC_IP'];
				$statusRes-> status = $row['NAME_STATUS'];
				$statusRes-> uniqueId = $row['UNIQUE_WWW_ID'];
				$statusObj->resultCode='OK';
				$statusObj->errorDescription='';
				$statusObj->message='';
				$statusObj -> resultObj = $statusRes;
			}//Enf if...
		}
	}//End if..else..parametri vuoti...
	return ($statusObj);
}


function saveStatus($platform,$uniqueId,$deviceId,$privateIp,$publicIp,$crypt,$internalTemp,$externalTemp,$internalHum,$location,$nameStatus,$externalHum) {
	
	//Prima di salvare devo verificare se il deviceId è veramente di questo utente
	//Se shutdown = Y devo creare una riga per spegnimento...
	$statusUpdate = new Response();
	$statusUpdate -> resultCode = 'KO';
	$statusUpdate -> errorDescription = 'Generic Error';
	
	if (empty($uniqueId) || empty($platform) || empty($deviceId)  || empty($location)) {
		$statusUpdate -> errorDescription = 'KO_INPUT_MANCANTI';
		$statusUpdate -> message = 'Parametri di input mancanti';
	} else {
		$link = GET_DB_CONNECTION();

		$result = $link -> prepare("update WEBAIR_DB.AIR_DEVICE_STATUS set  INTERNAL_TEMP=:internalTemp, INTERNAL_HUM=:internalHum, EXTERNAL_HUM=:externalHum, EXTERNAL_TEMP=:externalTemp, EXTERNAL_HUM=:externalHum where ID_DEVICE=:deviceId");
		$result -> bindValue(':internalTemp', $internalTemp);
		$result -> bindValue(':deviceId', $deviceId);
		$result -> bindValue(':internalHum', $internalHum);
		$result -> bindValue(':externalTemp', $externalTemp);
		$result -> bindValue(':externalHum', $externalHum);
		$result -> execute();
		if (!$result) {
			$statusUpdate -> errorDescription = 'Impossibile interagire con il DB';
			$statusUpdate -> errorDescription = 'KO_DB_INTERACTION';
		} else {
						
			$result = $link -> prepare("update WEBAIR_DB.AIR_DEVICE set PUBLIC_IP=:publicIp, PRIVATE_IP=:privateIp, LOCATION=:location where UNIQUE_WWW_ID=:uniqueId and ID_DEVICE=:deviceId");
			$result -> bindValue(':publicIp', $publicIp);
			$result -> bindValue(':location', $location);
			$result -> bindValue(':privateIp', $privateIp);
			$result -> bindValue(':uniqueId', $uniqueId);
			$result -> bindValue(':deviceId', $deviceId);
			$result -> execute();
			$num_rows = $result -> rowCount();
			if ($num_rows > 0) {
				$statusUpdate -> resultCode = 'OK';
				$statusUpdate -> errorDescription = '';
				$statusUpdate -> message = 'Update eseguito correttamente';
			}else{
				$statusUpdate -> resultCode = 'KO';
				$statusUpdate -> errorDescription = 'KO_UPDATE_STATUS_ERROR';
				$statusUpdate -> message = 'Errore nel salvataggio dello status';
			}//Enf if...
		}	
	}
	return ($statusUpdate);
}
?>
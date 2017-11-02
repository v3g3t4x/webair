<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . '/DBConnection.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $bean_path . '/Response.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $bean_path . '/ControllerDeviceResponse.php';

function getControllerDevice($username, $deviceId, $platform) {

	$controllerDeviceList = new Response();
	$controllerDeviceList -> resultCode = 'KO';
	$controllerDeviceList -> errorDescription = 'Generic Error';
	$controllerDeviceList -> message = 'Generic Error';

	if (empty($username) || empty($platform) || empty($deviceId)) {
		$controllerDeviceList -> errorDescription = 'KO_INPUT_MANCANTI';
		$controllerDeviceList -> message = 'Parametri di input mancanti';
	} else {
		$link = GET_DB_CONNECTION();
		$ruolo='admin';
		$result = $link -> prepare("SELECT device.ID_COMMAND, ORDER_COMMAND,ROW_NUM,device.DESCRIPTION, ICON_URL, comm.COMMAND_TYPE,  devdev.PRIVATE_IP FROM WEBAIR_DB.AIR_CONTROLLER_DEVICE device, WEBAIR_DB.AIR_USER user, WEBAIR_DB.AIR_COMMAND comm, WEBAIR_DB.AIR_DEVICE devdev WHERE device.IS_ENABLED=1 AND  (device.ID_DEVICE=:deviceId or device.ID_DEVICE='%') AND  user.RUOLO =:ruolo and user.USERNAME=:username and comm.ID_COMMAND=device.ID_COMMAND and devdev.ID_DEVICE=:deviceId and comm.IS_ENABLED=1 AND  comm.ID_DEVICE='DEVICE_TEST'");
		$result->bindValue(':username',  $username);
		$result->bindValue(':deviceId',  $deviceId);
		$result->bindValue(':ruolo',  $ruolo);
		$result -> execute();
		if (!$result) {
			$controllerDeviceList -> errorDescription = 'KO_DB_INTERACTION';
			$controllerDeviceList -> message = 'Impossibile interagire con il DB';
		} else {
			$num_rows = $result->rowCount();
		//	if ($num_rows > 0) {
				$controllerDeviceList -> resultObj=array();
				$rows = $result -> fetchAll();

				foreach ($rows as $row) {
					$controllerDeviceObj = new ControllerDeviceResponse();
					$controllerDeviceObj -> idCommand = $row['ID_COMMAND'];
					$controllerDeviceObj -> order = $row['ORDER_COMMAND'];
					$controllerDeviceObj -> rowNum = $row['ROW_NUM'];
					$controllerDeviceObj -> description = $row['DESCRIPTION'];
					$controllerDeviceObj -> iconUrl= $row['ICON_URL'];
					$controllerDeviceObj -> commandType= $row['COMMAND_TYPE'];
					$controllerDeviceObj -> privateIp= $row['PRIVATE_IP'];
					array_push($controllerDeviceList -> resultObj,$controllerDeviceObj);
					//echo "DESC>>: " . $controllerDeviceObj -> description;
				}//End for..
				
				$controllerDeviceList -> resultCode = 'OK';
				$controllerDeviceList -> errorDescription = '';
				$controllerDeviceList -> message = 'Telecomando virtuale recuperato';
		//	}//Enf if...
		}
	}
	return ($controllerDeviceList);
}
?>
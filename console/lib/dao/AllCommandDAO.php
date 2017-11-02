<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . '/DBConnection.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $bean_path . '/Response.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $bean_path . '/AllCommandResponse.php';

function getAllCommandAdmin($username, $ruolo, $platform) {

	$commandList = new Response();
	$commandList -> resultCode = 'KO';
	$commandList -> errorDescription = 'Generic Error';

	if (empty($username) || empty($platform) || empty($ruolo)) {
		$commandList -> errorDescription = 'KO_INPUT_MANCANTI';
		$commandList -> message = 'Parametri di input mancanti';
	} else if (strcmp($ruolo, 'admin') != 0) {
		$commandList -> errorDescription = 'KO_NOT_ADMIN';
		$commandList -> message = 'Non sei un utente amministratore';
	} else {
		$link = GET_DB_CONNECTION();
		$result = $link -> prepare("SELECT ID_COMMAND,ID_DEVICE,NAME_COMMAND,DESCRIPTION,SCRIPT_PATH, SCRIPT_TYPE,DEVICE_TYPE,COMMAND_TYPE FROM WEBAIR_DB.AIR_COMMAND where IS_ENABLED=1;");
		$result -> execute();
		if (!$result) {
			$commandList -> errorDescription = 'Impossibile interagire con il DB';
			$commandList -> message = 'KO_DB_INTERACTION';
		} else {
			$num_rows = $result -> rowCount();
			if ($num_rows > 0) {
				$commandList -> resultObj = array();
				$rows = $result -> fetchAll();
				foreach ($rows as $row) {
					$commandListObj = new AllCommandResponse();
					$commandListObj -> idCommand = $row['ID_COMMAND'];
					$commandListObj -> nameCommand = $row['NAME_COMMAND'];
					$commandListObj -> description = $row['DESCRIPTION'];
					$commandListObj -> deviceType = $row['DEVICE_TYPE'];
					$commandListObj -> scriptPath = $row['SCRIPT_PATH'];
					$commandListObj -> scriptType = $row['SCRIPT_TYPE'];
					$commandListObj -> commandType = $row['COMMAND_TYPE'];
					array_push($commandList -> resultObj, $commandListObj);
				}//End for...
				$commandList -> resultCode = 'OK';
				$commandList -> errorDescription = '';
				$commandList -> message = 'Comandi recuperati';
				//$commandList -> resultObj = new ArrayObject($commandListObj);
			}//End if...
		}//End if..else...
	}
	return ($commandList);
}


function getSingleCommand($username,$deviceId,$commandId) {

	$commandExecution = new Response();
	$commandExecution -> resultCode = 'KO';
	$commandExecution -> errorDescription = 'Generic Error';

	if (empty($username) || empty($deviceId) || empty($commandId)) {
		$commandExecution -> errorDescription = 'KO_INPUT_MANCANTI';
		$commandExecution -> message = 'Parametri di input mancanti';
	} else {
		$link = GET_DB_CONNECTION();
		$result = $link -> prepare("SELECT command.ID_COMMAND,DEVICE_TYPE,command.ID_DEVICE,NAME_COMMAND,command.DESCRIPTION,SCRIPT_PATH,SCRIPT_TYPE FROM WEBAIR_DB.AIR_COMMAND command,  WEBAIR_DB.AIR_CONTROLLER_DEVICE controller, WEBAIR_DB.AIR_USER user WHERE command.IS_ENABLED=1 and user.USERNAME=:username and command.ID_COMMAND=controller.ID_COMMAND and (controller.ID_DEVICE=command.ID_DEVICE or controller.ID_DEVICE='%') and command.ID_DEVICE like :deviceId and command.ID_COMMAND=:commandId and controller.RUOLO=user.RUOLO;");
		$result->bindValue(':username', $username);
		$result->bindValue(':deviceId', $deviceId);
		$result->bindValue(':commandId', $commandId);
		$result -> execute();
		if (!$result) {
			$commandExecution -> errorDescription = 'Impossibile interagire con il DB';
			$commandExecution -> errorDescription = 'KO_DB_INTERACTION';
		} else {
			$num_rows = $result -> rowCount();
			if ($num_rows > 0) {
				$commandExecution -> resultObj = array();
				$rows = $result -> fetchAll();
				foreach ($rows as $row) {
					$commandExecutionObj = new AllCommandResponse();
					$commandExecutionObj -> idCommand = $row['ID_COMMAND'];
					$commandExecutionObj -> nameCommand = $row['NAME_COMMAND'];
					$commandExecutionObj -> description = $row['DESCRIPTION'];
					$commandExecutionObj -> deviceType = $row['DEVICE_TYPE'];
					$commandExecutionObj -> scriptPath = $row['SCRIPT_PATH'];
					$commandExecutionObj -> scriptType = $row['SCRIPT_TYPE'];
					array_push($commandExecution -> resultObj, $commandExecutionObj);
				}//End for...
				$commandExecution -> resultCode = 'OK';
				$commandExecution -> errorDescription = '';
				$commandExecution -> message = 'Comando recuperato';
				//$commandExecution -> resultObj = new ArrayObject($commandExecutionObj);
			}//End if...
		}//End if..else...
	}
	return ($commandExecution);
}
?>
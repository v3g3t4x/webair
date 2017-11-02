<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/AllCommandDAO.php";

function runCommand($username, $deviceId, $commandId) {

	$resutCommand = getSingleCommand($username, $deviceId, $commandId);
	if (isset($resutCommand -> resultObj)) {
		$command = $resutCommand -> resultObj[0] -> scriptPath;
		$commandExecution = shell_exec($command);
		$resutCommand -> resultCode = 'OK';
		$resutCommand -> errorDescription = 'OK';
		$resutCommand -> message = $commandExecution;
	} else {
		$resutCommand -> resultCode = 'KO';
		$resutCommand -> errorDescription = 'KO_FIND_COMMAND';
		$resutCommand -> message = 'Errore nell\'recupero del comando nel sistema';
	}
	return $resutCommand;
}
?>
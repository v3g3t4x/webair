<?php

include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . '/DBConnection.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $bean_path . '/Response.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $bean_path . "/LoginResult.php";

function checkLogin($username, $password, $platform) {
	$userObj = new Response();
	$userObj -> resultCode = 'KO';
	$userObj -> errorDescription = 'Generic Error';
	if (empty($username) || empty($password) || empty($platform)) {
		$userObj -> errorDescription = 'KO_INPUT_MANCANTI';
		$userObj -> message = 'Parametri di input mancanti';
	} else {//Se i parametri non sono vuoti...
		$link = GET_DB_CONNECTION();
		

		$result = $link->prepare("select user.USERNAME, user.NOME, user.COGNOME, user.RUOLO, user.LATEST_ACCESS FROM WEBAIR_DB.AIR_USER user WHERE user.USERNAME=:username and user.PASSWORD=:password and user.IS_ENABLED=1");
		$result->bindValue(':username', $username);
		$result->bindValue(':password',  $password);
		$result->execute();
		if (!$result) {
			$userObj -> errorDescription = 'KO_DB_INTERACTION';
			$userObj -> message = 'Impossibile interagire con il DB';
		} else {
			$num_rows = $result->rowCount();
		
			if ($num_rows > 0) {
				$row =$result->fetch(PDO::FETCH_ASSOC);
				$loginRes = new LoginResult();
				$loginRes -> name = $row['NOME'];
				$loginRes -> surname = $row['COGNOME'];
				$loginRes -> username = $row['USERNAME'];
				$loginRes -> ruolo = $row['RUOLO'];
				$loginRes -> latestAccess = $row['LATEST_ACCESS'];
				$userObj->resultCode='OK';
				$userObj->errorDescription='OK';
				$userObj->message='Benvenuto ' . $loginRes -> name;
				$userObj -> resultObj = $loginRes;
				saveLastLogin($username, $platform);
			}//Enf if...
		}
	}//End if..else..parametri vuoti...
	return ($userObj);
}

function saveLastLogin($username, $platform) {
	$link = GET_DB_CONNECTION();
	$result = $link->prepare("UPDATE WEBAIR_DB.AIR_USER SET LATEST_ACCESS=UNIX_TIMESTAMP(NOW()) WHERE USERNAME=:username ");
	$result->bindValue(':username', $username);
	$count = $result->execute();
}

?>
<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . '/DBConnection.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $bean_path . '/Response.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $bean_path . '/SchedulingResponse.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $bean_path . '/SchedulingTypeResponse.php';


function checkSchedulingUpdate($username, $deviceId,$platform) {

	$schedulingList = new Response();
	$schedulingList -> resultCode = 'KO';
	$schedulingList -> errorDescription = 'Generic Error';
	if (empty($username) || empty($platform) || empty($deviceId)) {
		$schedulingList -> errorDescription = 'KO_INPUT_MANCANTI';
		$schedulingList -> message = 'Parametri di input mancanti';
	} else {
		$link = GET_DB_CONNECTION();
		$result = $link -> prepare("SELECT max(CREATION_DATE) AS MAX_DATE  FROM WEBAIR_DB.AIR_SCHEDULING SCH WHERE DEVICE_ID=:deviceId and USERNAME=:username ");
		$result -> bindValue(':deviceId', $deviceId);
		$result -> bindValue(':username', $username);
		$result -> execute();
		if (!$result) {
			$schedulingList -> message = 'Impossibile interagire con il DB';
			$schedulingList -> errorDescription = 'KO_DB_INTERACTION';
		} else {
			$num_rows = $result -> rowCount();
			$schedulingList -> resultObj=array();
			if ($num_rows > 0) {
				
				$rows = $result -> fetchAll();
				foreach ($rows as $row) {
					$schedulingList -> resultObj = $row['MAX_DATE'];
					
				}
				
				$schedulingList -> resultCode = 'OK';
				$schedulingList -> errorDescription = '';
				$schedulingList -> message = 'Scheduling update recuperato';
			}else{
				$schedulingList -> resultCode = 'OK';
				$schedulingList -> errorDescription = 'NO_SCHEDULING';
				$schedulingList -> message = 'Non è presente nessuna schedulazione';
				
			}
		}	
	}
	return ($schedulingList);
}


function getSchedulingList($username, $deviceId,$platform) {

	$schedulingList = new Response();
	$schedulingList -> resultCode = 'KO';
	$schedulingList -> errorDescription = 'Generic Error';
	if (empty($username) || empty($platform) || empty($deviceId)) {
		$schedulingList -> errorDescription = 'KO_INPUT_MANCANTI';
		$schedulingList -> message = 'Parametri di input mancanti';
	} else {
		$link = GET_DB_CONNECTION();
		$result = $link -> prepare("SELECT ID, CMD.ID_COMMAND, SCH.SCHEDULING_TYPE AS SCHTYPE, SCH.SCHEDULING_VALUE AS VALUESCH, SCHTYPE.DESCRIPTION AS DESCTYPE, SCH.DESCRIPTION, START_TIME, END_TIME, PRIORITY, VALIDITY_RANGE_START, VALIDITY_RANGE_END,CMD.DESCRIPTION AS CMDDESC  FROM WEBAIR_DB.AIR_SCHEDULING SCH, WEBAIR_DB.AIR_SCHEDULING_TYPE SCHTYPE, WEBAIR_DB.AIR_COMMAND CMD WHERE SCH.SCHEDULING_TYPE=SCHTYPE.SCHEDULING_TYPE AND SCH.ID_COMMAND=CMD.ID_COMMAND AND SCH.DEVICE_ID=:deviceId  and SCH.IS_ENABLED=1 AND CMD.ID_DEVICE=SCH.DEVICE_ID ORDER BY SCH.DESCRIPTION ASC, VALIDITY_RANGE_START ASC, PRIORITY ASC , SCH.SCHEDULING_TYPE ASC, START_TIME ASC");
		$result -> bindValue(':deviceId', $deviceId);
		$result -> execute();
		if (!$result) {
			$schedulingList -> message = 'Impossibile interagire con il DB';
			$schedulingList -> errorDescription = 'KO_DB_INTERACTION';
		} else {
			$num_rows = $result -> rowCount();
			$schedulingList -> resultObj=array();
			if ($num_rows > 0) {
				
				$rows = $result -> fetchAll();
				foreach ($rows as $row) {
					$schedulingObj = new SchedulingResponse();
					
					$schedulingObj -> commandId = $row['ID_COMMAND'];
					$schedulingObj -> schedulingTypeDescription = $row['DESCTYPE'];
					$schedulingObj -> schedulingType = $row['SCHTYPE'];
					$schedulingObj -> schedulingValue = $row['VALUESCH'];
					$schedulingObj -> priority= $row['PRIORITY'];
					$schedulingObj -> startTimeStamp = $row['START_TIME'];
					$schedulingObj -> endTimeStamp = $row['END_TIME'];
					$schedulingObj -> description = $row['DESCRIPTION'];
					$schedulingObj -> schedulingId = $row['ID'];
					$schedulingObj -> validityRangeStart = $row['VALIDITY_RANGE_START'];
					$schedulingObj -> validityRangeEnd = $row['VALIDITY_RANGE_END'];
					$schedulingObj -> commandDescription = $row['CMDDESC'];
					array_push($schedulingList -> resultObj,$schedulingObj);
				}
				$schedulingList -> resultCode = 'OK';
				$schedulingList -> errorDescription = '';
				$schedulingList -> message = 'Schedulazione correttamente recuperata';
			}else{
				$schedulingList -> resultCode = 'OK';
				$schedulingList -> errorDescription = 'NO_SCHEDULING';
				$schedulingList -> message = 'Non è presente nessuna schedulazione';
				
			}
		}	
	}
	return ($schedulingList);
}

function getSchedulingType($username, $deviceId,$platform) {

	$schedulingType = new Response();
	$schedulingType -> resultCode = 'KO';
	$schedulingType -> errorDescription = 'Generic Error';
	if (empty($username) || empty($platform) || empty($deviceId)) {
		$schedulingType -> errorDescription = 'KO_INPUT_MANCANTI';
		$schedulingType -> message = 'Parametri di input mancanti';
	} else {
		$link = GET_DB_CONNECTION();
		$result = $link -> prepare("SELECT SCHEDULING_TYPE, DESCRIPTION, ORDER_ID FROM WEBAIR_DB.AIR_SCHEDULING_TYPE ORDER BY ORDER_ID ASC");
		$result -> bindValue(':deviceId', $deviceId);
		$result -> execute();
		if (!$result) {
			$schedulingType -> message = 'Impossibile interagire con il DB';
			$schedulingType -> errorDescription = 'KO_DB_INTERACTION';
		} else {
			$num_rows = $result -> rowCount();
			if ($num_rows > 0) {
				$schedulingType -> resultObj=array();
				$rows = $result -> fetchAll();
				foreach ($rows as $row) {
					$schedulingTypeObj = new SchedulingTypeResponse();
					$schedulingTypeObj -> schedulingType = $row['SCHEDULING_TYPE'];
					$schedulingTypeObj -> description = $row['DESCRIPTION'];
					$schedulingTypeObj -> orderId= $row['ORDER_ID'];
					array_push($schedulingType -> resultObj,$schedulingTypeObj);
				}
				$schedulingType -> resultCode = 'OK';
				$schedulingType -> errorDescription = '';
				$schedulingType -> message = 'Schedulazione correttamente recuperata';
			}else{
				$schedulingType -> resultCode = 'OK';
				$schedulingType -> errorDescription = 'NO_SCHEDULING';
				$schedulingType -> message = 'Non è presente nessuna schedulazione';
			}
		}	
	}
	return ($schedulingType);
}


function deleteSchedulingId($username,$id, $deviceId,$platform) {

	$schedulingDel = new Response();
	$schedulingDel -> resultCode = 'KO';
	$schedulingDel -> errorDescription = 'Generic Error';
	if (empty($username) || empty($platform) || empty($deviceId)) {
		$schedulingDel -> errorDescription = 'KO_INPUT_MANCANTI';
		$schedulingDel -> message = 'Parametri di input mancanti';
	} else {
		$link = GET_DB_CONNECTION();
		$result = $link -> prepare("DELETE FROM WEBAIR_DB.AIR_SCHEDULING WHERE ID=:schedulingId and USERNAME=:username and DEVICE_ID=:deviceId");
		$result -> bindValue(':schedulingId', $id);
		$result -> bindValue(':username', $username);
		$result -> bindValue(':deviceId', $deviceId);
		$result -> execute();
		if (!$result) {
			$schedulingDel -> errorDescription = 'Impossibile interagire con il DB';
			$schedulingDel -> errorDescription = 'KO_DB_INTERACTION';
		} else {
			$num_rows = $result -> rowCount();
			if ($num_rows > 0) {
				$schedulingDel -> resultCode = 'OK';
				$schedulingDel -> errorDescription = '';
				$schedulingDel -> message = 'Schedulazione correttamente eliminata';
			}else{
				$schedulingDel -> resultCode = 'KO';
				$schedulingDel -> errorDescription = 'KO_SCHEDULING_NOT_FOUND';
				$schedulingDel -> message = 'Errore nella cancellazione dello scheduling';
			}//Enf if...
		}	
	}
	return ($schedulingDel);
}


function saveNewScheduling($username,$deviceId,$commandId,$schedulingType,$schedulingValue,$startTime,$endTime,$validityStart,$validityEnd,$priority,$description,$platform,$shutdown) {
	
	//Prima di salvare devo verificare se il deviceId è veramente di questo utente
	//Se shutdown = Y devo creare una riga per spegnimento...
	$schedulingNew = new Response();
	$schedulingNew -> resultCode = 'KO';
	$schedulingNew -> errorDescription = 'Generic Error';
	if (empty($username) || empty($platform) || empty($deviceId)) {
		$schedulingNew -> errorDescription = 'KO_INPUT_MANCANTI';
		$schedulingNew -> message = 'Parametri di input mancanti';
	} else {
		$link = GET_DB_CONNECTION();
		
		
//		if(strcmp($shutdown,"true") != 0){
			$result = $link -> prepare("insert into WEBAIR_DB.AIR_SCHEDULING(USERNAME,DEVICE_ID,ID_COMMAND,SCHEDULING_TYPE,SCHEDULING_VALUE,START_TIME,END_TIME,VALIDITY_RANGE_START,VALIDITY_RANGE_END,PRIORITY,DESCRIPTION, IS_ENABLED)  values(:username,:deviceId,:commandId,:schedulingType,:schedulingValue,:startTime,:endTime,:validityStart,:validityEnd,:priority,:description,'1');");			
	//	}else{
		//	if(strcmp($startTime, $endTime)>0){//Data start > data end
			//	$result = $link -> prepare("insert into WEBAIR_DB.AIR_SCHEDULING(USERNAME,DEVICE_ID,ID_COMMAND,SCHEDULING_TYPE,SCHEDULING_VALUE,START_TIME,END_TIME,VALIDITY_RANGE_START,VALIDITY_RANGE_END,PRIORITY,DESCRIPTION, IS_ENABLED)  values(:username,:deviceId,:commandId,:schedulingType,:schedulingValue,:startTime,:endTime,ADDDATE(:validityStart, INTERVAL 1 DAY),ADDDATE(:validityEnd, INTERVAL 1 DAY),:priority,:description,'1');");				
			//}else{
				//$result = $link -> prepare("insert into WEBAIR_DB.AIR_SCHEDULING(USERNAME,DEVICE_ID,ID_COMMAND,SCHEDULING_TYPE,SCHEDULING_VALUE,START_TIME,END_TIME,VALIDITY_RANGE_START,VALIDITY_RANGE_END,PRIORITY,DESCRIPTION, IS_ENABLED)  values(:username,:deviceId,:commandId,:schedulingType,:schedulingValue,:startTime,:endTime,:validityStart,:validityEnd,:priority,:description,'1');");
			//}			
	//	}
		
		//Se la data di fine è minore della data di inizio significa che devo programmare la fine il giorno dopo...
//		if(strcmp($startTime, $endTime)>0 && strcmp($shutdown,"true") == 0)  {
			

	//	}else{

		//}
		$result -> bindValue(':username', $username);
		$result -> bindValue(':deviceId', $deviceId);
		$result -> bindValue(':commandId', $commandId);
		$result -> bindValue(':schedulingType', $schedulingType);
		$result -> bindValue(':schedulingValue', $schedulingValue);
		$result -> bindValue(':startTime', $startTime);
		$result -> bindValue(':endTime', $endTime);
		$result -> bindValue(':validityStart', $validityStart);
		$result -> bindValue(':validityEnd', $validityEnd);
		$result -> bindValue(':priority', $priority);
		$result -> bindValue(':description', $description);
		$result -> execute();
		if (!$result) {
			$schedulingNew -> errorDescription = 'Impossibile interagire con il DB';
			$schedulingNew -> errorDescription = 'KO_DB_INTERACTION';
		} else {
			$num_rows = $result -> rowCount();
			if ($num_rows > 0) {
				$schedulingNew -> resultCode = 'OK';
				$schedulingNew -> errorDescription = '';
				$schedulingNew -> message = 'Schedulazione correttamente salvata';
			}else{
				$schedulingNew -> resultCode = 'KO';
				$schedulingNew -> errorDescription = 'KO_SCHEDULING_NOT_FOUND';
				$schedulingNew -> message = 'Errore nel salvataggio della nuova schedulazione';
			}//Enf if...
		}	
	}
	return ($schedulingNew);
}

function saveNewSchedulingSpegnimento($username,$deviceId,$commandId,$schedulingType,$schedulingValue,$startTime,$endTime,$validityStart,$validityEnd,$priority,$description,$platform,$shutdown) {
	
	//Prima di salvare devo verificare se il deviceId è veramente di questo utente
	//Se shutdown = Y devo creare una riga per spegnimento...
	$schedulingNew = new Response();
	$schedulingNew -> resultCode = 'KO';
	$schedulingNew -> errorDescription = 'Generic Error';
	if (empty($username) || empty($platform) || empty($deviceId)) {
		$schedulingNew -> errorDescription = 'KO_INPUT_MANCANTI';
		$schedulingNew -> message = 'Parametri di input mancanti';
	} else {
		$link = GET_DB_CONNECTION();
		
		
			if(strcmp($startTime, $endTime)>0){//Data start > data end
				$result = $link -> prepare("insert into WEBAIR_DB.AIR_SCHEDULING(USERNAME,DEVICE_ID,ID_COMMAND,SCHEDULING_TYPE,SCHEDULING_VALUE,START_TIME,END_TIME,VALIDITY_RANGE_START,VALIDITY_RANGE_END,PRIORITY,DESCRIPTION, IS_ENABLED)  values(:username,:deviceId,:commandId,:schedulingType,:schedulingValue,:endTime,:endTime,ADDDATE(:validityStart, INTERVAL 1 DAY),ADDDATE(:validityEnd, INTERVAL 1 DAY),:priority,:description,'1');");				
			}else{
				$result = $link -> prepare("insert into WEBAIR_DB.AIR_SCHEDULING(USERNAME,DEVICE_ID,ID_COMMAND,SCHEDULING_TYPE,SCHEDULING_VALUE,START_TIME,END_TIME,VALIDITY_RANGE_START,VALIDITY_RANGE_END,PRIORITY,DESCRIPTION, IS_ENABLED)  values(:username,:deviceId,:commandId,:schedulingType,:schedulingValue,:endTime,:endTime,:validityStart,:validityEnd,:priority,:description,'1');");
			}			
		
		
		//Se la data di fine è minore della data di inizio significa che devo programmare la fine il giorno dopo...
//		if(strcmp($startTime, $endTime)>0 && strcmp($shutdown,"true") == 0)  {
			

	//	}else{

		//}
		$result -> bindValue(':username', $username);
		$result -> bindValue(':deviceId', $deviceId);
		$result -> bindValue(':commandId', $commandId);
		$result -> bindValue(':schedulingType', $schedulingType);
		$result -> bindValue(':schedulingValue', $schedulingValue);
		$result -> bindValue(':startTime', $startTime);
		$result -> bindValue(':endTime', $endTime);
		$result -> bindValue(':validityStart', $validityStart);
		$result -> bindValue(':validityEnd', $validityEnd);
		$result -> bindValue(':priority', $priority);
		$result -> bindValue(':description', $description);
		$result -> execute();
		if (!$result) {
			$schedulingNew -> errorDescription = 'Impossibile interagire con il DB';
			$schedulingNew -> errorDescription = 'KO_DB_INTERACTION';
		} else {
			$num_rows = $result -> rowCount();
			if ($num_rows > 0) {
				$schedulingNew -> resultCode = 'OK';
				$schedulingNew -> errorDescription = '';
				$schedulingNew -> message = 'Schedulazione correttamente salvata';
			}else{
				$schedulingNew -> resultCode = 'KO';
				$schedulingNew -> errorDescription = 'KO_SCHEDULING_NOT_FOUND';
				$schedulingNew -> message = 'Errore nel salvataggio della nuova schedulazione';
			}//Enf if...
		}	
	}
	return ($schedulingNew);
}


?>
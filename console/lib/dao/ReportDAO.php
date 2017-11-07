<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . '/DBConnection.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $bean_path . '/Response.php';


function getReport($deviceId, $reportType, $platform) {
	
	$reportObj = new Response();
	$reportObj-> resultCode = 'KO';
	$reportObj-> errorDescription = 'Generic Error';
	
	if (empty($deviceId) ||empty($reportType) |empty($platform)) {
		$reportObj -> message = 'Parametri di input mancanti';
		$reportObj -> errorDescription = 'KO_INPUT_MANCANTI';
	} else {
		$link = GET_DB_CONNECTION();
		$result = $link -> prepare("SELECT report.ID_REPORT, report.REPORT_TYPE, report.ID_DEVICE, report.VALUE, report.TIMESTAMP FROM WEBAIR_DB.AIR_REPORT report WHERE report.ID_DEVICE=:deviceId and report.REPORT_TYPE=:reportType order by report.TIMESTAMP asc");
		$result->bindValue(':deviceId',  $deviceId);
		$result->bindValue(':reportType',  $reportType);
		$result -> execute();
		if (!$result) {
			$reportObj -> errorDescription = 'KO_DB_INTERACTION';
			$reportObj -> message = 'Impossibile interagire con il DB';
		} else {
			$num_rows = $result->rowCount();
			if ($num_rows > 0) {
				$row =$result->fetch(PDO::FETCH_ASSOC);
				$reportObj-> resultObj = array();
				
				foreach ($rows as $row) {
					$reportRow = new ReportRow();
					$reportRow -> idReport = $row['ID_REPORT'];
					$reportRow -> idDevice= $row['ID_DEVICE'];
					$reportRow -> reportType= $row['REPORT_TYPE'];
					$reportRow -> value= $row['VALUE'];
					$reportRow -> timeStamp= $row['TIMESTAMP'];
					array_push($reportObj-> resultObj, $reportRow);
				}
				
				$reportObj->resultCode='OK';
				$reportObj->errorDescription='';
				$reportObj->message='';
				
			}else{
				$reportObj-> resultCode = 'OK';
				$reportObj-> errorDescription = 'REPORT_EMPTY';
				$reportObj-> message = 'Nessun report';
			}
		}
	}
	return ($reportObj);
}

?>
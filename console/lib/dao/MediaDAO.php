<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . '/DBConnection.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $bean_path . '/Response.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $bean_path . '/MediaResponse.php';

function getMediaList($username,$deviceId, $platform) {

	$mediaList = new Response();
	$mediaList -> resultCode = 'KO';
	$mediaList -> errorDescription = 'Generic Error';
	if (empty($username) || empty($platform) || empty($deviceId)) {
		$mediaList -> errorDescription = 'KO_INPUT_MANCANTI';
		$mediaList -> message = 'Parametri di input mancanti';
	} else {
		$link = GET_DB_CONNECTION();
		$result = $link -> prepare("SELECT ID_MEDIA,MEDIA_TYPE, MEDIA_LABEL,MEDIA_TIME_STAMP,MEDIA_THUMBNAIL, MEDIA_URL FROM WEBAIR_DB.AIR_MEDIA where DEVICE_ID=:deviceId;");
		$result -> bindValue(':deviceId', $deviceId);
		$result -> execute();
		if (!$result) {	
			$mediaList -> errorDescription = 'KO_DB_INTERACTION';
			$mediaList -> message = 'Impossibile interagire con il DB';
		} else {
			$num_rows = $result -> rowCount();
			if ($num_rows > 0) {
				$mediaList -> resultObj=array();
				$rows = $result -> fetchAll();
				foreach ($rows as $row) {
					$mediaObj = new MediaResponse();
					$mediaObj -> idMedia= $row['ID_MEDIA'];
					$mediaObj -> mediaLabel = $row['MEDIA_LABEL'];
					$mediaObj -> mediaThumbnail = $row['MEDIA_THUMBNAIL'];
					$mediaObj -> mediaTimeStamp = $row['MEDIA_TIME_STAMP'];
					$mediaObj -> mediaType = $row['MEDIA_TYPE'];
					$mediaObj -> mediaUrl = $row['MEDIA_URL'];
					array_push($mediaList -> resultObj,$mediaObj);
				}
				$mediaList -> resultCode = 'OK';
				$mediaList -> errorDescription = '';
				$mediaList -> message = 'Media recuparati correttamente';
			}//Enf if...
		}
	}
	return ($mediaList);
}


?>
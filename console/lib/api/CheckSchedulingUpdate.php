<?php
include $_SERVER['DOCUMENT_ROOT'].'/config.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/SchedulingDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $json_path . "/JSONObject.php";


$platform = 'WEB';
$username =$_GET['username'];
$deviceId =$_GET['deviceId'];
$resutCheck=checkSchedulingUpdate($username,$deviceId, $platform);
$jsonString=json_encode($resutCheck);
echo $jsonString;
?>
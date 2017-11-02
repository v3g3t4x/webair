<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/LoginDAO.php";
require $_SERVER['DOCUMENT_ROOT'] . "/" . $dao_path . "/LogDAO.php";
$username = $_POST['username'];
$password = $_POST['password'];
$platform =$_POST['platform'];
$userObj = checkLogin($username, $password, $platform);
saveNewLog("API","Login",$username,$platform,"");

$jsonString=json_encode($userObj);
echo $jsonString;

?>
<?php
require("../json/JSONObject.php");

session_start();

unset($_SESSION['login_obj']);
session_unset();
session_destroy();
$_SESSION = array();
$jsonString = "{\"errorDescription\":\"OK\",\"message\":\"Logout done\",\"resultCode\":\"OK\",\"resultObj\":null,\"systemTime\":1350051393}";

echo $jsonString;
?>


<?php
include $_SERVER['DOCUMENT_ROOT'].'/config.php';
$jsonString = "{\"errorDescription\":\"\",\"message\":\"\",\"resultCode\":\"OK\",\"resultObj\":[{\"parameterName\":\"LANGUAGE\",\"parameterValue\":\"it-IT\",\"parameterDescription\":\"Italiano\"},{\"parameterName\":\"LANGUAGE_LIST\",\"parameterValue\":\"it-IT,en-EN\",\"parameterDescription\":\"Italiano,Inglese\"}],\"systemTime\":1350051393}";
echo $jsonString;
?>
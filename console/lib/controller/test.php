<?php
include $_SERVER['DOCUMENT_ROOT'] . '/DBConnection.php';
ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(-1);
echo "CIAO--->";

$milliseconds = round(microtime(true) * 1000);
echo($milliseconds);
echo $_SERVER['DOCUMENT_ROOT'];





?>
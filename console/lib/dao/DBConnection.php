<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

function GET_DB_CONNECTION() {

$hostname = "localhost";
	$username = "user";
	$password = "pass";

	try {
		$link = new PDO("mysql:host=$hostname;dbname=mysql", $username, $password, array(PDO::ATTR_PERSISTENT => true));
	} catch(PDOException $e) {
		print "Errore! " . $e -> getMessage() . "</br>";
		die();
	}
	return ($link);
}
?>
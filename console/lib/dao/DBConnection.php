<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

function GET_DB_CONNECTION() {

	$hostname = "<SERVER>";
	$username = "USER";
	$password = "PASSWORD";

	try {
		$link = new PDO("mysql:host=$hostname;dbname=mysql", $username, $password, array(PDO::ATTR_PERSISTENT => true));
	} catch(PDOException $e) {
		print "Errore! " . $e -> getMessage() . "</br>";
		die();
	}
	return ($link);
}
?>
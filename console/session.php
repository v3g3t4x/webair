<?php

	include $_SERVER[ 'DOCUMENT_ROOT' ] . '/config.php';
	require $_SERVER[ 'DOCUMENT_ROOT' ] . "/" . $json_path . "/JSONObject.php";

	$a = $_POST[ 'json' ];
	$b = $_POST[ 'type' ];

	if (!isset($a) || !isset($b)) die();

	$class = new JSONObject( $a );

	session_start();

	if (strcmp( $b, 'login' ) == 0) {
		$_SESSION[ 'login_obj' ] = serialize( $class );
		die();
	}
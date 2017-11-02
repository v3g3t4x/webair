<?php

	$a = $_POST[ 'action' ];
	$b = $_POST[ 'value' ];

	if (!isset($a) || !isset($b)) die();

	if (strcmp( $a, "create_language" ) == 0) {

		$cookie_name = "language";
		setcookie( $cookie_name, $b, time() + (86400 * 30), "/" );
		die();

	}

	if (strcmp( $a, "delete_language" ) == 0) {

		$cookie_name = "language";
		setcookie( $cookie_name, "", time() - 3600, "/" );
		die();

	}
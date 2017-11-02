<?php

	/****************
	 * IN THIS SECTION WE HAVE SET THE DEBUG
	 ****************/

	$DEBUG = true;

	if ($DEBUG) {

		ini_set( 'display_errors', 1 );
		ini_set( 'display_startup_errors', 1 );
		error_reporting( -1 );

	}

	/****************
	 * IN THIS SECTION WE HAVE SET THE FOLDER/FILE PATH
	 ****************/

	/*************** FOLDER ***************/

	/**
	 * PROJECT ROOT
	 */
	$root = "/console";

	/**
	 * CSS FOLDER
	 */
	$css = "css";

	/**
	 * SCRIPT FOLDER
	 */
	$script = "scripts";

	/**
	 * WEB FOLDER
	 */
	$platform = "platform";

	/**
	 * LIB FOLDER
	 */
	$lib = "lib";

	/**
	 * LIB-API FOLDER
	 */
	$api = "api";

	/**
	 * LIB-BEAN FOLDER
	 */
	$bean = "bean";

	/**
	 * LIB-CONTROLLER FOLDER
	 */
	$controller = "controller";

	/**
	 * LIB-DAO FOLDER
	 */
	$dao = "dao";

	/**
	 * LIB-JSON FOLDER
	 */
	$json = "json";

	/**
	 * UTIL FOLDER
	 */
	$util = "util";

	/**
	 * LIB-LANGUAGES FOLDER
	 */
	$languages = "languages";

	/*************** PATH ***************/

	/**
	 * CSS PATH
	 */
	$css_path = $root . "/" . $css;

	/**
	 * PLATFORM PATH
	 */
	$web_path = $root . "/" . $platform;

	/**
	 * SCRIPT PATH
	 */
	$script_path = $root . "/" . $script;

	/**
	 * LIB PATH
	 */
	$languages_path = $root . "/" . $languages;

	/**
	 * LIB PATH
	 */
	$lib_path = $root . "/" . $lib;

	/**
	 * LIB-API PATH
	 */
	$api_path = $lib_path . "/" . $api;

	/**
	 * LIB-BEAN PATH
	 */
	$bean_path = $lib_path . "/" . $bean;

	/**
	 * LIB-CONTROLLER PATH
	 */
	$controller_path = $lib_path . "/" . $controller;

	/**
	 * LIB-DAO PATH
	 */
	$dao_path = $lib_path . "/" . $dao;

	/**
	 * LIB-UTIL PATH
	 */
	$util_path = $lib_path . "/" . $util;

	/**
	 * LIB-JSON PATH
	 */
	$json_path = $lib_path . "/" . $json;
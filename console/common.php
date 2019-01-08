<?php

include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . "/" . $json_path . "/JSONObject.php";

class Common
{

    private $Login_Obj = null;

    function __construct()
    {
        $this->Check_Login_Status();
    }

    private function Check_Login_Status()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['login_obj'])) $this->Login_Obj = unserialize($_SESSION['login_obj']);
    }

    public function Is_User_Logged()
    {
        if ($this->Get_Login_Data() != null) return true;

        return false;
    }

    public function Get_Login_Data()
    {
        return $this->Login_Obj;
    }

    public function Is_Language_Set()
    {
        if (isset($_COOKIE['language'])) return $_COOKIE['language'];

        return "not_set";
    }

    public function Get_Language()
    {
        include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
        if (!strcmp($this->Is_Language_Set(), "not_set") == 0) return $_SERVER['DOCUMENT_ROOT'] . $languages_path . "/" . $this->Is_Language_Set() . ".php";

        return $_SERVER['DOCUMENT_ROOT'] . $languages_path . "/it-IT.php";
    }

    public function Get_Header()
    {
        $head = "";
        $head .= $this->Get_Common_Header_Script();
        $head .= $this->Get_Common_Header();
        $head .= $this->Get_Favicon();

        return $head;
    }

    public function getFooterScript()
    {
        return '<script src="/console/script/bootstrap.min.js"></script>
					<script src="/console/script/angular.min.js"></script>
					<script src="/console/script/angular-animate.min.js"></script>
					<script src="/console/script/angular-aria.min.js"></script>
					<script src="/console/script/angular-messages.min.js"></script>
					<script src="/console/script/angular-material.min.js"></script>
                    <script type="text/javascript" src="/console/script/jquery.timepicker.min.js"></script>
                    <link rel="stylesheet" type="text/css" href="/console/script/jquery.timepicker.css"/>
                    <script src="/console/script/bootstrap-datepicker.js"></script>
                    <link href="/console/script/bootstrap-datepicker3.css" rel="stylesheet">
                    <script src="/console/script/bootstrap-datepicker.it.min.js" charset="UTF-8"></script>';
    }

    private function Get_Common_Header_Script()
    {
        include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

        return '<link rel="stylesheet" href="/console/css/bootstrap.min.css">
					<link rel="stylesheet" href="/console/css/angular-material.min.css">
					<link rel="stylesheet" href="/console/css/main.css">
					<script src="/console/script/jquery.min.js"></script>';
    }

    private function Get_Common_Header()
    {
        include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

        $a = '<link href="https://fonts.googleapis.com/css?family=Roboto:400,100,300" rel="stylesheet" type="text/css">';
        $a .= '<meta name="viewport" content="width=device-width , initial-scale=1">';
        $a .= '<meta name="robots" content="noindex">';
        $a .= '<meta name="theme-color" content="#5d65a8">';

        return $a;
    }

    private function Get_Favicon()
    {
        return '<link rel = "apple-touch-icon" sizes = "57x57" href = "/console/icon/apple-icon-57x57.png" >
			<link rel = "apple-touch-icon" sizes = "60x60" href = "/console/icon/apple-icon-60x60.png" >
			<link rel = "apple-touch-icon" sizes = "72x72" href = "/console/icon/apple-icon-72x72.png" >
			<link rel = "apple-touch-icon" sizes = "76x76" href = "/console/icon/apple-icon-76x76.png" >
			<link rel = "apple-touch-icon" sizes = "114x114" href = "/console/icon/apple-icon-114x114.png" >
			<link rel = "apple-touch-icon" sizes = "120x120" href = "/console/icon/apple-icon-120x120.png" >
			<link rel = "apple-touch-icon" sizes = "144x144" href = "/console/icon/apple-icon-144x144.png" >
			<link rel = "apple-touch-icon" sizes = "152x152" href = "/console/icon/apple-icon-152x152.png" >
			<link rel = "apple-touch-icon" sizes = "180x180" href = "/console/icon/apple-icon-180x180.png" >
			<link rel = "icon" type = "image/png" sizes = "192x192"  href = "/console/icon/android-icon-192x192.png" >
			<link rel = "icon" type = "image/png" sizes = "32x32" href = "/console/icon/favicon-32x32.png" >
			<link rel = "icon" type = "image/png" sizes = "96x96" href = "/console/icon/favicon-96x96.png" >
			<link rel = "icon" type = "image/png" sizes = "16x16" href = "/console/icon/favicon-16x16.png" >
			<link rel = "manifest" href = "/icon/manifest.json" >
			<meta name = "msapplication-TileColor" content = "#ffffff" >
			<meta name = "msapplication-TileImage" content = "/console/icon/ms-icon-144x144.png" >
			<meta name = "theme-color" content = "#ffffff" > ';
    }

}
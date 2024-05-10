<?php

// This script is for use in public_docs / httpdocs php files where you want GEOIP security implemented
// Usage:
// require_once("../aftc-framework/geoip-security.php");

// Get some global paths to use: ROOT, APP_ROOT, STORAGE
$parts = explode("/", $_SERVER["DOCUMENT_ROOT"]);
array_pop($parts);
$root = implode("/", $parts);
define("ROOT", $root);
define("APP_ROOT", $root . "/aftc-framework");
define("VIEWS_PATH", $root . "/aftc-framework/Views");
define("STORAGE", $root . "/storage");
// var_dump(ROOT);
// var_dump(APP_ROOT);
// var_dump(VIEWS_PATH);
// var_dump(STORAGE);
// var_dump($_SERVER);
// die();
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


// Get utility functions
require_once(APP_ROOT . "/Helpers/functions.php");


// Autoloader (Composer modules)
//require_once(APP_ROOT . "/Config/Enums.php"); // Note till PHP 8.1 (Nov 21)
require_once(APP_ROOT . "/vendor/autoload.php");
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


// Setup config
use AFTC\Config\Config;
Config::init();
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


// Setup vars
use AFTC\Config\Vars;
Vars::init();
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


// Setup time zone
date_default_timezone_set(Config::$timeZone);
ini_set('date.timezone', Config::$timeZone);
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


// Setup error handling
if (Config::$showErrors) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}
error_reporting(E_ALL);
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -





// Set where error logs are going to be written to
ini_set("error_log", ROOT . "/error_log.txt");
// writeToLog("TEST");
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -




// Lets go
use AFTC\Libs\GeoIpCheckLib;
$geoIpCheckLib = new GeoIpCheckLib();
$access_granted = $geoIpCheckLib->checkGeoIp();
error_log($access_granted);
if ($access_granted === false){
    header("location: /blocked.html");
}


// use AFTC\Libs\RouterLib;
// new RouterLib();
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

<?php


/**
 * Global constants for path use: ROOT, APP_ROOT, VIEWS_PATH
 */
$parts = explode('/', $_SERVER['DOCUMENT_ROOT']);
array_pop($parts);
$root = implode('/', $parts);
define('ROOT', $root); // level below http_docs / public_html / www
define('APP_ROOT', $root . '/aftc-framework');
define('VIEWS_PATH', $root . '/aftc-framework/Views');

/**
 * Load global scoped helpers (project specific)
 */
require_once APP_ROOT . '/Utils/helpers.php';

/**
 * Load global scoped functions
 */
require_once APP_ROOT . '/Utils/functions.php';

/**
 * Autoloader (Composer modules)
 */
require_once APP_ROOT . '/vendor/autoload.php';

/**
 * Setup config
 */
use AFTC\Config\Config;
Config::init();

/**
 * Setup vars
 */
use AFTC\Config\Vars;
Vars::init();

/**
 * Setup time zone
 */
date_default_timezone_set(Config::$timeZone);
ini_set('date.timezone', Config::$timeZone);

/**
 * Setup error handling
 */
if (Config::$showErrors) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
}
ini_set('log_errors', '1');
error_reporting(E_ALL);

// Set where error logs are going to be written to
ini_set('error_log', ROOT . '/error_log.txt');

/**
 * Setup sessions
 */
if (Config::$enableSessions === true) {
    $secure = Config::$sessionSecure; // if you only want to receive the cookie over HTTPS
    $httponly = Config::$sessionHttpOnly; // set to true to prevent JavaScript from accessing session cookies
    $samesite = 'Strict'; // Lax or Strict

    session_set_cookie_params([
        'lifetime' => Config::$sessionLifetime,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => $secure,
        'httponly' => $httponly,
        'samesite' => $samesite,
    ]);

    session_name(Config::$sessionName);
    session_start();

    setcookie(
        session_name(),
        session_id(),
        ['expires' => time() + Config::$sessionLifetime, 'samesite' => 'Strict']
    );
}


use AFTC\Libs\GeoIpCheckLib;
if (Config::$enable_geo_check){
    $geoIpCheckLib = new GeoIpCheckLib();
    $access_granted = $geoIpCheckLib->checkGeoIp();
    AFTC\Utils\AFTCUtils::writeToLog($access_granted);
    if ($access_granted === false){
        header("location: /blocked.html");
        exit();
    }
}




// Lets go!
$routerLib = new \AFTC\Libs\RouterLib();
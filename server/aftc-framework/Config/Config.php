<?php

namespace AFTC\Config;

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


use AFTC\Utils\AFTCUtils;

class Config
{
    // Dev
    public static bool $dev = true;
    public static bool $sendAllEmailsToAdmin = false;
    public static string $adminEmail = "darcey@aftc.io";

    // Redirects
    public static string $accessDeniedUrl = "/access-denied.html";
    public static string $pageNotFoundUrl = "/404.html";
    public static string $errorUrl = "/500.html";

    // Errors and Logging
    public static bool $showErrors = false;

    // TimeZone
    public static string $timeZone = "Europe/London"; // http://php.net/manual/en/timezones.php

    // API Response Config
    // * || domain (domain will auto use the dynamically set $domain var)
    public static string $accessControlAllowOrigin = "domain";

    // SMTP PHP Mail (SMTP2GO etc)
    // MINE (FREE ACCOUNT LIMITED NO' OF EMAILS)
    public static string $smtpHost = "mail.smtp2go.com";
    public static string $smtpPort = "2525"; // 8025, 587 and 25 can also be used. Use Port 465 for SSL.
    public static string $emailApiKey = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
    public static string $emailApiUsername = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
    public static string $emailApiPassword = "xxxxxxxxxxxxxxxxxxxxxx";
    public static string $emailFrom = "no-reply@domain.dev";
    public static string $emailFromName = "website";
    public static string $emailReplyTo = "no-reply@domain.dev";


    // Sessions & cookie security
    public static bool $enableSessions = false;
    public static float $sessionLifetime = 3600 * 0.25; // 3600 = 1 hour * 0.25 (1/4) = 25 mins
    public static bool $sessionSecure = true; // If you only want to receive the cookie over HTTPS only
    public static bool $sessionHttpOnly = true; // Set to true to prevent JavaScript from accessing session cookies
    public static string $sessionName = "aftc-session";

    // JWT
    // https://github.com/firebase/php-jwt
    // iss (Issuer) Claim — The name of the entity that issued the token.
    // iat (Issued At) Claim — Identifies the time at which the JWT token was issued.
    // nbf (Not Before) Claim — Identifies the time before which the JWT token MUST NOT be accepted for processing.
    // exp (Expiration Time) — Identifies the expiration time on or after which the JWT MUST NOT be accepted for processing.

    // Generate SSH Key files with:
    // low security: ssh-keygen -t rsa -m pem -N passphrase
    // high security: ssh-keygen -t rsa -b 4096 -m PEM -N passphrase
    // aftc-framework default: ssh-keygen -t rsa -b 4096 -m PEM -N darcey1234
    public static string $jwtPrivateKeyFile = "aftc.pem";
    public static string $jwtPrivateKeyPassPhrase = "darcey1234";
    public static int $jwtLifeTime = 3600; //1800; //(60 * 12); // seconds 1800 is 30 minutes
    public static string $jwtAlgo = "RS512";
    public static string $jwtIss = ""; // DO NOT ALTER DYNAMICALLY SET
    public static string $jwtAud = ""; // DO NOT ALTER DYNAMICALLY SET
    public static string $jwtCookieName = "aftc-token";
    // public static int $nbf = 0; // set during JWT operations
    // public static int $exp = 0; // set during JWT operations

    // GEOIP
    // public static bool $enable_geo_check = false;
    // public static bool $enable_geo_check_in_dev = false;
    // public static string $geo_ip_cookie_name = "geoip_passed";
    // public static int $geo_ip_cookie_life = (24 * 3600); // 3600 = 1 hour
    // public static array $allowed_countries = ["GB"];
    // public static string $geo_ip_pass_value = "xxxxxxxxxxxxxxxxx}"; // 64 char

    // Passwords
    // https://www.php.net/manual/en/function.password-hash.php
    // https://deliciousbrains.com/php-encryption-methods/
    // default 10, increase will slow server down but is better security
    public static int $password_cost = 12;

    // 64 char ( exclude $'" )
    public static string $password_pepper = 'sQK3C$$a88G&ZdGr#Wkrt@N8fS3aU8mMxqw4vV=@8BYvaM$h*nK2AMUHfJ&NNm6J';

    // Encryption
    // Generate your random 64 Key and 16 IV strings here
    // IMPORTANT: DO NOT RE-USE SAME KEY AND IV STRING FOR ALL YOUR PROJECTS!
    public static string $encryption_key = '!zBANkuvPVb6Whw2$K&92pkB$T#$fHbYHAFB6@3BRZregk#A6Tf@bbfS&*zX36qF'; // 64 char
    public static string $encryption_iv = '#HxyMpQjd3X%Y&+U';

    // Database
    // http://www.doctrine-project.org/projects/dbal.html
    // Driver: pdo_mysql, pdo_sqlite, pdo_pgsql, pdo_oci, oci8, ibm_db2, pdo_sqlsrv, mysqli, drizzle_pdo_mysql, sqlanywhere, sqlsrv
    // Recommended: pdo_pgsql, mysqli
    public static string $databaseDriver = "pdo_mysql";
    public static string $databaseCharset = "utf8";
    public static string $databaseHost = ""; // SET IN METHOD "init()" SWITCH STATEMENT
    public static string $databasePort = ""; // SET IN METHOD "init()" SWITCH STATEMENT
    public static string $databaseName = ""; // SET IN METHOD "init()" SWITCH STATEMENT
    public static string $databaseUsername = ""; // SET IN METHOD "init()" SWITCH STATEMENT
    public static string $databasePassword = ""; // SET IN METHOD "init()" SWITCH STATEMENT

    // Dynamically set
    public static string $domain = ""; // Dynamically set

    // Fastrouter
    public static string $routerCacheFolder = ""; // Dynamically set

    // Twig configuration
    public static bool $twigEnableCache = false;
    public static bool $twigDebug = true;
    public static string $viewFolder = ""; // Dynamically set
    public static string $twigCacheFolder = ""; // Dynamically set

    // Router data (DO NOT MODIFY THIS VARIABLE, IT IS SET DYNAMICALLY)
    public static mixed $routData;


    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    public static function init(): void
    {
        // Var defs
        $parts = explode('/', $_SERVER['DOCUMENT_ROOT']);
        array_pop($parts);
        $root = implode('/', $parts);

        self::$domain = $_SERVER["SERVER_NAME"];

        // Fastrouter
        self::$routerCacheFolder = $root . '/aftc-framework/cache/router';


        // Twig
        self::$twigCacheFolder = $root . '/aftc-framework/cache/twig';
        self::$viewFolder = $root . '/aftc-framework/Views';

        // DB Connections
        // Want more configurations for other domains and subdomains add them here
        switch ($_SERVER["SERVER_NAME"]) {
            case "127.0.0.1":
                self::$databaseHost = "127.0.0.1";
                self::$databasePort = "3306";
                self::$databaseName = "aftc_framework";
                self::$databaseUsername = "root";
                self::$databasePassword = "";
                self::$jwtIss = AFTCUtils::getServerProtocol() . "://127.0.0.1";
                self::$jwtAud = self::$jwtIss;
                break;
            case "phpframework.aftc.io":
                self::$databaseHost = "127.0.0.1";
                self::$databasePort = "3306";
                self::$databaseName = "aftc_framework";
                self::$databaseUsername = "";
                self::$databasePassword = "";
                self::$jwtIss = AFTCUtils::getServerProtocol() . "://phpframework.aftc.io";
                self::$jwtAud = self::$jwtIss;
                break;
            default:
                $msg = "AFTC-Framework->Config->Unhandled Domain " . $_SERVER["SERVER_NAME"];
                AFTCUtils::writeToLog($msg);
                echo ($msg);
                break;
        }
    }
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
}

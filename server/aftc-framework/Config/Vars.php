<?php

namespace AFTC\Config;
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


class Vars
{
    public static array $languages = [
        "en" => "English"
    ];

    public static string $defaultLanguage = "en";

    // Used by SecurityLib which in turn is used by the Controllers (Web & Api) to validate user access to route
    public static array $userTypes = [
        "system admin", // Application may display debug code
        "admin",
        "product manager",
        "user"
    ];

    public static int $recordsPerPage = 40;

    public static function init():void
    {
        // self::$data = json_decode(file_get_contents(APP_ROOT . "/data/data.json"), true);
        // self::$data = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/data/config.json"), true);
        // error_log($_SERVER["DOCUMENT_ROOT"] . "/data/config.json");
        // vdd(self::$json_languages);
    }
}
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

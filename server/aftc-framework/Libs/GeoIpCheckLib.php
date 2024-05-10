<?php

namespace AFTC\Libs;

use AFTC\Config\Config;
use AFTC\Utils\AFTCUtils;
use Exception;
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -



class GeoIpCheckLib
{
    // Vars
    private string $ip;
    private bool $secure = true;
    private bool $httponly = true;
    private string $path = "/";

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    public function __construct()
    {
        $this->ip = getIp();
    }
    // - - - - - - - - - - - - - - - - - - - - - - - -


    public function checkGeoIp(): bool
    {
        // Handle dev mode
        if (Config::$dev === true && Config::$enable_geo_check_in_dev === false) {
            return true;
        }

        // Check user agent first (bit of limited extra protection)
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            if ($_SERVER['HTTP_USER_AGENT'] == null || $_SERVER['HTTP_USER_AGENT'] == "") {
                AFTCUtils::writeToLog("checkGeoIp(): Blocked User Agent: " . $this->ip . " - [" . $_SERVER['HTTP_USER_AGENT'] . "]");
                return false;
            }
        } else {
            AFTCUtils::writeToLog("checkGeoIp(): Blocked User Agent: " . $this->ip . " - [" . $_SERVER['HTTP_USER_AGENT'] . "]");
            return false;
        }



        // Check if the geo ip check is enabled, if not let them through
        if (Config::$enable_geo_check === false) {
            return true;
        }


        // Want to test GEO IP set ip manually
        // UK: 95.149.121.70  NOT UK: 95.149.121.70
        // $this->ip = Config::$geo_ip_check_dev_ip;


        // Check to see if the GEOIP has been done and whether the ip is allowed in or not
        // WARNING: Cookies should be secure so javascript cannot access them (especially if using JWTs)
        $cookie_value = $this->getCookie();
        // var_dump($cookie_value);
        // die();

        // If null - do the check, if false - return false (access denied), if true - return true (access granted)
        $access_granted = false;
        if ($cookie_value === null) {
            return $this->queryGeoIpService();
        } else if ($cookie_value === "1") {
            return true;
        } else {
            return false;
        }
    }
    // - - - - - - - - - - - - - - - - - - - - - - - -


    private function queryGeoIpService(): bool
    {

        // vars
        $api_url = 'https://api.ipstack.com/' . $this->ip . '?access_key=' . Config::$ipstack_api_key;
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $response = false; // from geoip service
        $json = false; // parsed response
        $country_code = false; // country code


        // Try CURL request on API url
        try {
            // Initialize CURL:
            // $ch = curl_init('https://api.ipstack.com/'.$ip.'?access_key='.Config::$ipstack_api_key.'');
            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_HEADER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            // var_dump($response);
            // Utils::writeToLog($response);


            // Check API response
            if ($response === false || $response === NULL) {
                $this->unsetCookie();
                AFTCUtils::writeToLog("GEO IP SERVICE FAILURE: Returned a response of FALSE or NULL: " . $url);
                // Don't want real users to get blocked so let everyone in
                return true;
            }
            // var_dump($response);
            // die();


            // Decode JSON response:
            try {
                $json = json_decode($response, true);
            } catch (Exception $e) {
                $this->unsetCookie();
                AFTCUtils::writeToLog("GEO IP SERVICE FAILURE: Unable to json decode GEO IP api response: " . $url . " - " . $response);
                // Don't want real users to get blocked so let everyone in
                return true;
            }
            // var_dump($json);
            // die();


            // Check country code
            try {
                $country_code = $json["country_code"];
            } catch (Exception $e) {
                $this->unsetCookie();
                AFTCUtils::writeToLog("GEO IP FAILURE: Unable to get country code from response json: " . $url . " - " . $json);
                // Don't want real users to get blocked so let everyone in
                return true;
            }
            // var_dump($country_code);
            // die();


            // Check country code
            if (!in_array($country_code, Config::$allowed_countries)) {
                AFTCUtils::writeToLog("checkGeoIp(): Blocked: " . $this->ip . " - " . $country_code . " - " . $url . " - " . $_SERVER['HTTP_USER_AGENT']);
                $this->setCookie("0");
                return false;
            } else {
                $this->setCookie("1");
                return true;
            }
        } catch (Exception $e) {
            $this->unsetCookie();
            AFTCUtils::writeToLog("GEO IP SERVICE FAILURE " . $e);
            // Don't want real users to get blocked so let everyone in
            return true;
        }
    }
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


    private function setCookie($value): void
    {
        setcookie(Config::$geo_ip_cookie_name, $value, Time() + Config::$geo_ip_cookie_life, $this->path, Config::$domain, $this->secure, $this->httponly);
    }
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    private function deleteCookie()
    {
        setcookie(Config::$geo_ip_cookie_name, "", Time() - 30000, $this->path, Config::$domain, $this->secure, $this->httponly);
    }
    private function unsetCookie()
    {
        $this->deleteCookie();
    }
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    private function getCookie(): ?string
    {
        if (isset($_COOKIE[Config::$geo_ip_cookie_name])) {
            return htmlspecialchars($_COOKIE[Config::$geo_ip_cookie_name]);
        } else {
            return null;
        }
    }
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

}

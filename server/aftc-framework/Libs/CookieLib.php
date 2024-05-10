<?php

namespace AFTC\Libs;

use AFTC\Config\Config;
use AFTC\Utils\AFTCUtils;

class CookieLib
{
    public function setTokenCookie(string $value): void
    {
        $cookieExpiryTime = time() + Config::$jwtLifeTime;
        setcookie(Config::$jwtCookieName, $value, $cookieExpiryTime, "/", Config::$domain, true, true);
    }

    public function unsetTokenCookie(): void
    {
        $cookieExpiryTime = time() - 3600;
        setcookie(Config::$jwtCookieName, "", $cookieExpiryTime, "/", Config::$domain, true, true);
    }

    public function getTokenCookie(): ?string
    {
        if (isset($_COOKIE[Config::$jwtCookieName])) {
            return $_COOKIE[Config::$jwtCookieName];
        } else {
            return null;
        }
    }

    public function setCookie(string $name, string $value, int $expiryTime): void
    {
        setcookie($name, $value, $expiryTime, "", "", false, false);
    }

    public function unsetCookie(string $name): void
    {
        $cookieExpiryTime = time() - 3600;
        setcookie($name, "", $cookieExpiryTime, "", "", false, false);
    }

    public function getCookie(string $name): ?string
    {
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        } else {
            AFTCUtils::writeToLog("CookieLib->getCookie(): cookie [$name] not set/found - returning null");
            return null;
        }
    }
}
<?php

namespace AFTC\Utils;
class Request
{
    public static function post(string $key): ?string
    {
        if (isset($_POST[$key])) {
            $value = $_POST[$key];

            if (is_string($value)) {
                $value = urldecode($value);
                $value = trim($value);
                return $value;
            }
        }

        return null;
    }

    public static function get(string $key): ?string
    {
        if (isset($_GET[$key])) {
            $value = $_GET[$key];

            if (is_string($value)) {
                $value = urldecode($value);
                $value = trim($value);
                return $value;
            }
        }

        return null;
    }
}
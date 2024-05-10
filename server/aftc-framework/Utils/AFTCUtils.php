<?php

namespace AFTC\Utils;

use DateTime;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class AFTCUtils {

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

    public static function getDateDifInSeconds(DateTime $start_date, DateTime $end_date): int {
        $diff = $start_date->diff($end_date);
        $daysInSecs = $diff->format('%r%a') * 24 * 60 * 60;
        $hoursInSecs = $diff->h * 60 * 60;
        $minsInSecs = $diff->i * 60;
        return $daysInSecs + $hoursInSecs + $minsInSecs + $diff->s;
    }

    public static function sendJsonResponse(mixed $data, bool $encode = false): void {
        header('Content-Type: application/json; charset=utf-8');
        echo $encode ? json_encode($data) : $data;
    }

    public static function castObjectToObject(object $source, object $target): void {
        foreach ($source as $property => $value) {
            $target->$property = $value;
        }
    }

    public static function getHeader(string $name): ?string {
        if (empty($name)) {
            self::writeToLog("Utils::getHeader(string name): Usage error of function, name is empty!");
            return null;
        }
        $headers = getallheaders();
        return $headers[$name] ?? null;
    }

    public static function getCleanAuthHeader(): ?string {
        $authHeader = self::getHeader("Authorization");
        return $authHeader !== null ? str_replace("Bearer ", "", $authHeader) : null;
    }

    public static function redirect(string $url, bool $permanent = false, bool $cache = true): never {
        if (!$cache) {
            header("Cache-Control: no-cache");
        }
        header('Location: ' . $url, true, $permanent ? 301 : 302);
        exit();
    }

    public static function writeToLog($data): void {
        $ip = "[" . self::getIp() . "]";
        $uri = isset($_SERVER['REQUEST_URI']) ? "[" . $_SERVER['REQUEST_URI'] . "]" : "UNABLE TO GET URL CALLED";

        $formattedData = self::formatData($data);

        error_log($ip . " " . $uri . " " . $formattedData);
    }

    private static function formatData($data): string {
        if (is_string($data)) {
            return "[" . $data . "]";
        } elseif (is_numeric($data)) {
            return "[" . strval($data) . "]";
        } elseif (is_bool($data)) {
            return "[" . ($data ? 'true' : 'false') . "]";
        } elseif (is_array($data)) {
            $formattedArray = "Array:\n";
            foreach ($data as $key => $value) {
                $formattedArray .= "  " . $key . " => " . self::formatData($value) . "\n";
            }
            return $formattedArray;
        } elseif (is_object($data)) {
            $formattedObject = "Object(" . get_class($data) . "):\n";
            foreach ((array)$data as $key => $value) {
                $formattedObject .= "  " . $key . " => " . self::formatData($value) . "\n";
            }
            return $formattedObject;
        } elseif (is_null($data)) {
            return "[null]";
        } else {
            return "[" . gettype($data) . "]";
        }
    }

    public static function removeDir(string $dir): void {
        if (!is_dir($dir)) {
            return;
        }
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }
        rmdir($dir);
    }

    public static function getIp(): string {
        return trim(getenv('HTTP_CLIENT_IP') ?:
            getenv('HTTP_X_FORWARDED_FOR') ?:
                getenv('HTTP_X_FORWARDED') ?:
                    getenv('HTTP_FORWARDED_FOR') ?:
                        getenv('HTTP_FORWARDED') ?:
                            getenv('REMOTE_ADDR'));
    }

    public static function getJson(bool $decodeJson = true, bool $associative = true): mixed {
        return $decodeJson ? json_decode(file_get_contents("php://input"), $associative) :
            file_get_contents("php://input");
    }

    public static function setJsonHeader(): void {
        header('Content-type:application/json;charset=utf-8');
    }

    public static function getPagedOffset(int $pageNo, int $recordsPerPage): int {
        $pageNo = max(0, $pageNo - 1);
        return $pageNo * $recordsPerPage;
    }

    public static function writeArrayToErrorLog(array $array): void {
        $output = print_r($array, true);
        error_log($output);
    }

    public static function getRandomBoolean(): bool {
        return (bool) random_int(0, 1);
    }

    public static function randomDateInRange(DateTime $start, DateTime $end): DateTime {
        $randomTimestamp = mt_rand($start->getTimestamp(), $end->getTimestamp());
        $randomDate = new DateTime();
        $randomDate->setTimestamp($randomTimestamp);
        return $randomDate;
    }

    public static function getRandomDateBetween(string $start_date, string $end_date): string {
        $min = strtotime($start_date);
        $max = strtotime($end_date);
        $val = rand($min, $max);
        return date('Y-m-d', $val);
    }

    public static function getRandomDateTimeBetween(string $start_date, string $end_date): string {
        $min = strtotime($start_date);
        $max = strtotime($end_date);
        $val = rand($min, $max);
        return date('Y-m-d H:i:s', $val);
    }

    public static function dumpJson(mixed $arg): never {
        header('Content-Type: application/json');
        http_response_code(200);
        echo self::isJson($arg) ? $arg : json_encode($arg);
        die();
    }

    public static function isJson(mixed $arg): bool {
        return is_string($arg) && is_array(json_decode($arg, true)) && (json_last_error() == JSON_ERROR_NONE);
    }

    public static function getRandomString(int $length): string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    public static function getRandomNumber(int $length): string {
        $characters = '0123456789';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    public static function isValidEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function echoPageData(array $arr, string $index): void {
        echo $arr[$index] ?? "\$page_data does not contain an index of [$index]";
    }

    public static function getDirList(string $dir): array {
        $sd = scandir($dir);
        $result = [];

        foreach ($sd as $value) {
            if (!in_array($value, [".", ".."])) {
                $result[] = $value;
            }
        }

        return $result;
    }

    public static function getServerProtocol(): string {
        return stripos($_SERVER['SERVER_PROTOCOL'], 'https') === 0 ? 'https' : 'http';
    }

    public static function isHTTPS(): bool {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || intval($_SERVER['SERVER_PORT']) == 443;
    }

    public static function instr(string $haystack, string $needle): bool {
        return strpos($haystack, $needle) !== false;
    }

    public static function sanitizeString(string $string, bool $force_lowercase = false, bool $strict = false): string {
        $string = strip_tags($string);
        $string = preg_replace('/[\r\n\t ]+/', ' ', $string);
        $string = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $string);
        $string = strtolower($string);
        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
        $string = htmlentities($string, ENT_QUOTES, 'UTF-8');
        $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string);
        $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/'), ' ', $string);
        $string = trim($string);

        if ($strict) {
            $string = preg_replace('/[^a-zA-Z0-9]/', '', $string);
        }

        if ($force_lowercase) {
            $string = strtolower($string);
        }

        return $string;
    }

    public static function summarise(string $str, int $wordCount): string {
        $array = explode(" ", $str);
        $return_string = "";
        $count = min(count($array), $wordCount + 1);
        for ($i = 0; $i < $count; $i++) {
            $return_string .= $array[$i] . " ";
        }
        return trim($return_string);
    }

    public static function getSuffixFromFileName(string $str): string {
        $arr = explode(".", $str);
        return end($arr);
    }

    public static function AFTCExceptionHandler(\Exception $ex): void {
        $trace = $ex->getTrace();
        if ($trace[0]) {
            $ExceptionFile = $trace[0]["file"];
            $ExceptionFileLine = $trace[0]["line"];
            $ExceptionFunction = $trace[0]["function"];
            $ExceptionClass = array_key_exists("class", $trace[0]) ? $trace[0]["class"] : "0";

            $out = "<div style='border:1px solid #000000; background: #CCCCCC; color: #000000;padding: 5px; font-size:14px; font-family: \"Tahoma\"'>\n";
            $out .= "<div style='font-size: 16px; color: #CC0000;'>";
            $out .= "<b>ERROR " . $ex->getCode() . ": " . basename($ExceptionFile) . " (" . $ExceptionFileLine . ")</b>";
            $out .= "</div>\n";
            $out .= "<div style='font-size: 14px; color: #550000; margin: 5px;'>";
            $out .= "<b>" . $ExceptionFile . " - " . $ExceptionFunction . "() (" . $ExceptionFileLine . ")</b><br>\n";
            $out .= "<b>" . $ex->getFile() . " (" . $ex->getLine() . ")</b><br>\n";
            $out .= "</div>\n";
            $out .= "<div style='font-size: 14px; color: #000055; border: 1px solid #000000; padding: 5px; background: #BBBBBB; margin: 5px;'>";
            $out .= "<b>Message:</b><br>\n";
            $out .= $ex->getMessage();
            $out .= "</div>\n";
            $out .= "<div style='font-size: 14px; color: #000055; border: 1px solid #000000; padding: 5px; background: #BBBBBB; margin: 5px;'>";
            $out .= "<b>Trace:</b><br>\n";
            $out .= "<span style='font-size: 12px;'>" . self::recurseExceptionTraceOut($ex->getTrace()) . "</span>";
            $out .= "</div>\n";
            $out .= "<div style='font-size: 14px; color: #000055; border: 1px solid #000000; padding: 5px; background: #BBBBBB; margin: 5px;'>";
            $out .= "<b>Previous:</b><br>\n";
            $out .= $ex->getPrevious();
            $out .= "</div>\n";
            $out .= "</div>";
            echo $out;

            $log = "[" . $ExceptionFile . " (" . $ExceptionFileLine . ")" . "] ";
            $log .= "[" . $ex->getFile() . " (" . $ex->getLine() . ")" . "] ";
            $log .= "[" . $ex->getMessage() . "]";
            error_log($log);
        } else {
            var_dump($ex);
        }
    }

    public static function recurseExceptionTraceOut(array $input): string {
        $out = "";
        if (is_array($input)) {
            foreach ($input as $value) {
                $out .= self::recurseExceptionTraceOut($value);
            }
            $out .= "<br>";
        } else {
            $out = match (gettype($input)) {
                "string", "integer" => "&nbsp;&nbsp;&nbsp;&nbsp;" . $input . "<br>\n",
                "object" => "&nbsp;&nbsp;&nbsp;&nbsp;" . " OBJECT: [" . get_class($input) . "]<br>\n",
                default => "&nbsp;&nbsp;&nbsp;&nbsp;" . " Unhandled Type: [" . gettype($input) . "] = <br>\n",
            };
        }
        return str_replace("<br><br>", "<br>", $out);
    }
}
<?php

namespace AFTC\Libs;

use AFTC\Config\Config;
use AFTC\Utils\AFTCUtils;
use AFTC\VOs\ApiResponseVo;

class ApiResponseLib
{
    /**
     * @var SessionLib $sessionLib
     */
    private SessionLib $sessionLib;

    /**
     * ApiResponseLib constructor.
     */
    public function __construct()
    {
        $this->sessionLib = new SessionLib();
    }

    /**
     * Sends the API response with appropriate headers and JSON encoded data.
     *
     * @param ApiResponseVo $apiResponseVo The API response value object.
     * @param string|null $authHeader The optional authorization header (bearer token).
     * @return void
     */
    public function sendResponse(ApiResponseVo $apiResponseVo, ?string $authHeader = null): void
    {
        // Set Access-Control-Allow-Origin header
        if (Config::$accessControlAllowOrigin === "*") {
            $allowOrigin = "*";
        } else {
            $allowOrigin = Config::$domain;
        }

        header("Access-Control-Allow-Origin: $allowOrigin");

        // Set content type and charset
        header('Content-Type: application/json; charset=utf-8');

        // Set allowed headers
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        // Set authorization header (bearer token) if provided
        if ($authHeader !== null) {
            header("Authorization: Bearer $authHeader");
        }

        // Validate ApiResponseVo
        if (!isset($apiResponseVo->status)) {
            $msg = "ApiResponseLib: Error - Please set the response 'status' code/value.";
            AFTCUtils::writeToLog($msg);
            http_response_code(500);
            echo json_encode(['error' => $msg]);
            return;
        }


        // Set HTTP status code from ApiResponseVo
        http_response_code($apiResponseVo->status);

        // Echo the JSON encoded response
        echo json_encode($apiResponseVo);
    }
}
<?php

namespace AFTC\Libs;

use AFTC\Config\Config;
use AFTC\Config\Vars;
use AFTC\Enums\eSecurityControllerMode;
use AFTC\Libs\ApiResponseLib;
use AFTC\Libs\CookieLib;
use AFTC\Libs\EncryptionLib;
use AFTC\Libs\JwtLib;
use AFTC\Libs\SessionLib;
use AFTC\Utils\AFTCUtils;
use AFTC\VOs\ApiResponseVo;

class SecurityLib
{
    private EncryptionLib $encryptionLib;
    private SessionLib $sessionLib;
    private CookieLib $cookieLib;
    private JwtLib $jwtLib;
    private ApiResponseLib $apiResponseLib;
    private ApiResponseVo $apiResponseVo;

    public function __construct()
    {
        $this->encryptionLib = new EncryptionLib();
        $this->sessionLib = new SessionLib();
        $this->cookieLib = new CookieLib();
        $this->jwtLib = new JwtLib();
        $this->apiResponseLib = new ApiResponseLib();
        $this->apiResponseVo = new ApiResponseVo();
    }

    public function isTokenValid(string $token): bool
    {
        return $this->jwtLib->validate($token);
    }

    public function getRefreshToken(): ?string
    {
        $headerToken = AFTCUtils::getCleanAuthHeader();
        if ($this->jwtLib->validate($headerToken)) {
            $payload = $this->jwtLib->decode($headerToken);
            return $this->jwtLib->encode($payload["data"]);
        } else {
            $this->logInvalidToken($headerToken);
            return null;
        }
    }

    public function isLoggedIn(): bool
    {
        $headerToken = AFTCUtils::getCleanAuthHeader();
        return $this->jwtLib->validate($headerToken);
    }

    public function getPayload(?string $token = null): ?array
    {
        if ($this->isLoggedIn()) {
            $token = $token ?? AFTCUtils::getCleanAuthHeader();
            return $this->jwtLib->decode($token);
        }
        return null;
    }

    public function allowOnlyUserTypes(array $allowOnlyUserTypes): void
    {
        $this->validateUserTypes($allowOnlyUserTypes);
        if (!$this->isLoggedIn()) {
            $this->handleUnauthorizedAccess();
        }

        $payload = $this->getPayload();
        $payloadUserId = (int) trim($payload["data"]["user_id"]);
        $payloadUserType = trim($payload["data"]["user_type"]);

        if (!in_array($payloadUserType, $allowOnlyUserTypes)) {
            $this->denyAccess($payloadUserType, $payloadUserId);
        }
    }

    private function validateUserTypes(array $allowOnlyUserTypes): void
    {
        foreach ($allowOnlyUserTypes as $allowedUserType) {
            if (!in_array($allowedUserType, Vars::$userTypes)) {
                $this->apiResponseVo->status = 500;
                $this->apiResponseVo->message = "Incorrect usage of SecurityLib, an invalid user type was supplied.";
                $this->apiResponseVo->data = $allowOnlyUserTypes;
                $this->apiResponseLib->sendResponse($this->apiResponseVo);
                exit();
            }
        }
    }

    private function handleUnauthorizedAccess(): void
    {
        if (Config::$isApi) {
            $this->apiResponseVo->status = 401;
            $this->apiResponseVo->message = "Access Denied, you are not logged in.";
            $this->apiResponseLib->sendResponse($this->apiResponseVo);
        } else {
            AFTCUtils::redirect(Config::$accessDeniedUrl);
        }
        exit();
    }

    private function denyAccess(string $userType, int $userId): void
    {
        $message = lcfirst($userType) . "s (uid: $userId) do not have access to this API endpoint.";
        AFTCUtils::writeToLog($message);

        if (Config::$isApi) {
            $this->apiResponseVo->status = 401;
            $this->apiResponseVo->message = $message;
            $this->apiResponseLib->sendResponse($this->apiResponseVo);
        } else {
            AFTCUtils::redirect(Config::$accessDeniedUrl);
        }
        exit();
    }

    private function logInvalidToken(string $headerToken): void
    {
        $payload = $this->jwtLib->decode($headerToken);
        AFTCUtils::writeToLog("SecurityLib->getRefreshToken(): header token is not valid.");
        AFTCUtils::writeToLog("HeaderToken:\n" . $headerToken);
        AFTCUtils::writeToLog("Payload:\n" . json_encode($payload));
        AFTCUtils::writeToLog("--------------------------------------------------------");
    }
}

<?php

namespace AFTC\Libs;

use AFTC\Config\Config;
use AFTC\Utils\AFTCUtils;
use DateTime;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
use OpenSSLAsymmetricKey;

class JwtLib
{
    private string $pemFilePath;
    private OpenSSLAsymmetricKey  $privateKey;
    private string $publicKey;

    public function __construct()
    {
        $this->pemFilePath = ROOT . "/aftc-framework/JWT/" . Config::$jwtPrivateKeyFile;

        // Fetch the private key and extract public key
        $this->privateKey = openssl_pkey_get_private(
            file_get_contents($this->pemFilePath),
            Config::$jwtPrivateKeyPassPhrase
        );

        $this->publicKey = openssl_pkey_get_details($this->privateKey)['key'];
    }

    /**
     * Encodes data into a JWT token.
     *
     * @param array $data The data to encode into the token.
     * @return string The JWT token.
     */
    public function encode(array $data): string
    {
        if (!is_array($data)) {
            $msg = 'JwtLib->encode($data): Function requires data to be an array.';
            AFTCUtils::writeToLog($msg);
            http_response_code(500);
            die();
        }

        $token = [
            "iss" => Config::$jwtIss,
            "aud" => Config::$jwtAud,
            "iat" => time(),
            "nbf" => time(),
            "exp" => time() + Config::$jwtLifeTime,
            "data" => $data,
        ];

        return JWT::encode($token, $this->privateKey, Config::$jwtAlgo);
    }

    /**
     * Decodes a JWT token.
     *
     * @param string|null $token The JWT token to decode.
     * @return array|null The decoded token data.
     */
    public function decode(?string $token): ?array
    {
        if (!$token) {
            return null;
        }

        $token = str_replace("Bearer ", "", $token);

        try {
            $decoded = (array)JWT::decode($token, new Key($this->publicKey, Config::$jwtAlgo));
            $decoded["data"] = (array)$decoded["data"];
            return $decoded;
        } catch (Exception $e) {
            AFTCUtils::writeToLog($e->getMessage());
            return null;
        }
    }

    /**
     * Validates a JWT token.
     *
     * @param string|null $token The JWT token to validate.
     * @return bool True if the token is valid, false otherwise.
     */
    public function validate(?string $token): bool
    {
        if (!$token) {
            return false;
        }

        $token = str_replace("Bearer ", "", $token);

        try {
            JWT::decode($token, new Key($this->publicKey, Config::$jwtAlgo));
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

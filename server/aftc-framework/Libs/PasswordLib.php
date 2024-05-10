<?php

namespace AFTC\Libs;

use AFTC\Config\Config;
use AFTC\Libs\EncryptionLib;

class PasswordLib
{
    private EncryptionLib $encLib;

    public function __construct()
    {
        $this->encLib = new EncryptionLib();
    }

    /**
     * Hashes a password using Argon2id algorithm with pepper.
     *
     * @param string $pwd The password to hash.
     * @return string The hashed password.
     */
    public function hashPassword(string $pwd): string
    {
        // Recommended options are AES-256-CTR || AES-256-CBC || AES-256-ECB
        // PHP 8 recommends PASSWORD_ARGON2ID if possible

        // Passwords are peppered using SHA512 with a password pepper set in config
        $peppered_password = hash_hmac("sha512", $pwd, Config::$password_pepper);

        $options = [
            "cost" => Config::$password_cost, // algorithmic cost that should be used default 10
        ];

        return password_hash($peppered_password, PASSWORD_ARGON2ID, $options);
    }

    /**
     * Verifies a password against its hashed version.
     *
     * @param string $pwd The password to verify.
     * @param string $pwd_hashed The hashed password.
     * @return bool True if password matches, false otherwise.
     */
    public function verifyPassword(string $pwd, string $pwd_hashed): bool
    {
        $peppered_password = hash_hmac("sha512", $pwd, Config::$password_pepper);
        return password_verify($peppered_password, $pwd_hashed);
    }
}

<?php

namespace AFTC\Libs;

use AFTC\Libs\EncryptionLib;

/**
 * Class SessionLib
 *
 * Handles session management and encryption.
 */
class SessionLib
{
    private EncryptionLib $encLib;

    /**
     * SessionLib constructor.
     *
     * Initializes the EncryptionLib instance.
     */
    public function __construct()
    {
        $this->encLib = new EncryptionLib();
    }

    /**
     * Get a value from the session.
     *
     * @param string $key The session key.
     * @return string|null The decrypted session value or null if the key doesn't exist.
     */
    public function get(string $key): ?string
    {
        if (isset($_SESSION[$key])) {
            return $this->encLib->decrypt($_SESSION[$key]);
        }

        return null;
    }

    /**
     * Set a value in the session.
     *
     * @param string $key The session key.
     * @param string $value The value to encrypt and store in the session.
     * @return void
     */
    public function set(string $key, string $value): void
    {
        $encryptedValue = $this->encLib->encrypt($value);
        $_SESSION[$key] = $encryptedValue;
    }
}
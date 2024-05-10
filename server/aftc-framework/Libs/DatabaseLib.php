<?php

namespace AFTC\Libs;

use AFTC\Config\Config;
use AFTC\Patterns\Singleton;
use AFTC\Utils\AFTCUtils;
use PDO;
use PDOException;

/**
 * Class DatabaseLib
 * @package AFTC\Libs
 */
class DatabaseLib
{
    use Singleton;

    /**
     * @var PDO|null The PDO connection instance.
     */
    public ?PDO $con = null;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Connects to the database if not already connected.
     *
     * @return void
     */
    public function connect(): void
    {
        if ($this->isConnected()) {
            return;
        }

        try {
            $this->con = new PDO(
                'mysql:host=localhost;dbname=' . Config::$databaseName,
                Config::$databaseUsername,
                Config::$databasePassword,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            AFTCUtils::writeToLog("Unable to connect to database - " . $e->getMessage());
            header('Content-Type: application/json; charset=utf-8', true, 500);
            exit(json_encode(['error' => 'Unable to connect to the database']));
        }
    }

    /**
     * Checks if the database connection is established.
     *
     * @return bool True if connected, false otherwise.
     */
    public function isConnected(): bool
    {
        return $this->con !== null;
    }

    /**
     * Closes the database connection.
     *
     * @return void
     */
    public function close(): void
    {
        $this->con = null;
    }
}
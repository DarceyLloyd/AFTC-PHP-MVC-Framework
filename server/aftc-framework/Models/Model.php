<?php

namespace AFTC\Models;

use AFTC\Config\Config;
use AFTC\Enums\eQueryMode;
use AFTC\Libs\DatabaseLib;
use AFTC\Libs\PasswordLib;
use AFTC\Utils\AFTCUtils;
use AFTC\VOs\ModelQueryVo;
use PDO;
use PDOException;

/**
 * Class Model
 * @package AFTC\Models
 */
class Model
{
    /**
     * @var PasswordLib The password library instance.
     */
    protected PasswordLib $passwordLib;

    /**
     * @var DatabaseLib The database library instance.
     */
    protected DatabaseLib $db;

    /**
     * Model constructor.
     */
    public function __construct()
    {
        // DatabaseLib
        $this->db = DatabaseLib::getInstance();

        // Connect to the db
        $this->db->connect();

        // Libs
        $this->passwordLib = new PasswordLib();
    }

    /**
     * Get the last inserted ID.
     *
     * @return int The last inserted ID.
     */
    public function getInsertId(): int
    {
        return (int)$this->db->con->lastInsertId();
    }

    /**
     * Fetch a single row from the database.
     *
     * @param string $sql The SQL query.
     * @param array|null $inserts The query inserts.
     * @return ModelQueryVo The query result.
     */
    public function fetch(string $sql, ?array $inserts = null): ModelQueryVo
    {
        return $this->queryFetcher(eQueryMode::FETCH, $sql, $inserts);
    }

    /**
     * Fetch all rows from the database.
     *
     * @param string $sql The SQL query.
     * @param array|null $inserts The query inserts.
     * @return ModelQueryVo The query result.
     */
    public function fetchAll(string $sql, ?array $inserts = null): ModelQueryVo
    {
        return $this->queryFetcher(eQueryMode::FETCHALL, $sql, $inserts);
    }

    /**
     * Execute a query without returning any results.
     *
     * @param string $sql The SQL query.
     * @param array|null $inserts The query inserts.
     * @return ModelQueryVo The query result.
     */
    public function executeQuery(string $sql, ?array $inserts = null): ModelQueryVo
    {
        return $this->queryFetcher(eQueryMode::EXECUTE, $sql, $inserts);
    }

    /**
     * Fetch the count of rows by a column.
     *
     * @param string $tableName The table name.
     * @param string $dbColumn The column name.
     * @return int|null The count of rows or null if no rows found.
     */
    public function fetchCountByColumn(string $tableName, string $dbColumn): ?int
    {
        $sql = "SELECT COUNT(`$dbColumn`) AS count FROM `$tableName`";

        // Prepare query
        $query = $this->db->con->prepare($sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);

        // Execute, get results, count & get last insert id
        $success = $query->execute();
        $count = $query->rowCount();

        if ($count === 0) {
            return null;
        }

        if ($success === true) {
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return (int)$result["count"];
        } else {
            return null;
        }
    }

    /**
     * Fetch the count of rows by a column value.
     *
     * @param string $tableName The table name.
     * @param string $dbColumn The column name.
     * @param mixed $value The column value.
     * @return int|null The count of rows or null if no rows found.
     */
    public function fetchCountByColumnValue(string $tableName, string $dbColumn, mixed $value): ?int
    {
        $sql = "SELECT COUNT(`$dbColumn`) AS count FROM `$tableName` WHERE `$dbColumn` = :value";

        // Prepare query
        $query = $this->db->con->prepare($sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);

        // Bind typed data
        $query->bindParam(':value', $value);

        // Execute, get results, count & get last insert id
        $success = $query->execute();
        $count = $query->rowCount();

        if ($count === 0) {
            return null;
        }

        if ($success === true) {
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return (int)$result["count"];
        } else {
            return null;
        }
    }

    /**
     * Truncate a table.
     *
     * @param string $tableName The table name.
     * @return void
     */
    public function truncateTable(string $tableName): void
    {
        if ($_SERVER['REMOTE_ADDR'] !== "127.0.0.1" && Config::$dev !== true) {
            exit("ACCESS DENIED");
        }
        $this->db->con->query('SET foreign_key_checks = 0');
        $sql = "TRUNCATE TABLE `$tableName`";
        $this->db->con->query($sql);
        $this->db->con->query('SET foreign_key_checks = 1');
    }

    /**
     * Perform a query based on the query mode.
     *
     * @param eQueryMode $enumQueryMode The query mode.
     * @param string $sql The SQL query.
     * @param array|null $inserts The query inserts.
     * @return ModelQueryVo The query result.
     */
    private function queryFetcher(eQueryMode $enumQueryMode, string $sql, ?array $inserts = null): ModelQueryVo
    {
        // Check number of inserts matches the number of : in the sql
        if ($inserts !== null) {
            $queryTagCount = substr_count($sql, ":");
            $insertsCount = count($inserts);

            if ($queryTagCount !== $insertsCount) {
                $data = [
                    "error_message" => "INCORRECT NUMBER OF SQL INSERTS SUPPLIED",
                    "inserts_required" => $queryTagCount,
                    "inserts_supplied" => $insertsCount,
                    "inserts" => $inserts
                ];
                AFTCUtils::writeToLog(
                    '#########################################################
ERROR INCORRECT NUMBER OF SQL INSERTS SUPPLIED
---------------------------------------
' . $sql . '
---------------------------------------
' . json_encode($inserts) . '
#########################################################'
                );
                exit(json_encode($data));
            }
        }

        $vo = new ModelQueryVo();

        $query = $this->db->con->prepare($sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $querySuccess = false;

        // Bind params by detected type from array key and value
        if ($inserts !== null) {
            foreach ($inserts as $key => $value) {
                if (str_contains($sql, $key) === true) {
                    switch (gettype($value)) {
                        case "integer":
                            $query->bindParam($key, $value, PDO::PARAM_INT);
                            break;
                        default:
                            $query->bindParam($key, $value, PDO::PARAM_STR);
                            break;
                    }
                }
            }
        }

        try {
            $querySuccess = $query->execute();
        } catch (PDOException $e) {
            AFTCUtils::writeToLog("---------------");
            AFTCUtils::writeToLog("Model->queryFetcher(): ERROR:\n$e");
            AFTCUtils::writeToLog($sql);
            AFTCUtils::writeToLog(json_encode($inserts));
            AFTCUtils::writeToLog("---------------");
            if (Config::$dev === true) {
                throw $e;
            }
        }

        $vo->success = $querySuccess;
        $vo->rows = $query->rowCount();
        $vo->insertId = (int)$this->db->con->lastInsertId();

        switch ($enumQueryMode->value) {
            case "fetchall":
                $result = $query->fetchAll(PDO::FETCH_ASSOC);

                if (gettype($result) === "boolean") {
                    AFTCUtils::writeToLog("---------------");
                    AFTCUtils::writeToLog("Model->queryFetcher(): FETCHALL ERROR:\n");
                    AFTCUtils::writeToLog($sql);
                    AFTCUtils::writeToLog(json_encode($inserts));
                    AFTCUtils::writeToLog("---------------");
                    $vo->result = [];
                    $vo->success = false;
                } else {
                    $vo->result = $result;
                }
                break;
            case "fetch":
                $result = $query->fetch(PDO::FETCH_ASSOC);

                if (gettype($result) === "boolean") {
                    AFTCUtils::writeToLog("---------------");
                    AFTCUtils::writeToLog("Model->queryFetcher(): FETCH ERROR:\n");
                    AFTCUtils::writeToLog($sql);
                    AFTCUtils::writeToLog(json_encode($inserts));
                    AFTCUtils::writeToLog("---------------");
                    $vo->result = [];
                    $vo->success = false;
                } else {
                    $vo->result = $result;
                }
                break;
            case "execute":
                // Needs to be here so default is not triggered
                break;
            default:
                $msg = "MODEL USAGE ERROR - UNHANDLED QUERY MODE - $enumQueryMode->value";
                AFTCUtils::writeToLog($msg);
                exit(json_encode($msg));
                break;
        }

        return $vo;
    }
}
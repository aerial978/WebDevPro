<?php

namespace app\core;

use PDO;
use PDOException;

class Db extends PDO
{
    private static $instance;

    private function __construct()
    {
        $config = include(__DIR__ . '/../../config/database.php');

        try {
            $dsn = 'mysql:dbname=' . $config['dbname'] . ';host=' . $config['host'];
            parent::__construct($dsn, $config['username'], $config['password']);

            $this->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
            $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {

            error_log('Database Connection Error:' . $e->getMessage());
            die('Database connection error. Please check the logs for more details');
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
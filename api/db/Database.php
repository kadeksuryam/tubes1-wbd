<?php

namespace API\DB;

define('ROOTPATH', __DIR__);

final class Database
{
    private static $instance;
    private $dbConnection;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        try {
            $rootPath = ROOTPATH;
            $this->dbConnection = new \PDO("sqlite:".ROOTPATH."/tubes1wbd.db");
            $this->dbConnection->query('PRAGMA foreign_keys = ON;');
        } catch (\Exception $e) {
            exit("Error! " . $e->getMessage());
        }
    }

    public function getDbConnection()
    {
        return $this->dbConnection;
    }
}


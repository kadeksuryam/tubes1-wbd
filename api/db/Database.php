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
            $path = "sqlite:".ROOTPATH."/tubes1wbd.db";
            // if(isset($_ENV["PHP_ENV"]) && $_ENV["PHP_ENV"] == "production")
            //     $path = "sqlite:/root/db/tubes1.wbd.db";
            $this->dbConnection = new \PDO($path);
            $this->dbConnection->query('PRAGMA foreign_keys = ON;');
            $this->dbConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\Exception $e) {
            exit("Error! " . $e->getMessage());
        }
    }

    public function getDbConnection()
    {
        return $this->dbConnection;
    }
}


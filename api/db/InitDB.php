<?php

namespace DB;

include('./query.php');

final class InitDB
{
    private $db;
    const query_create_table_user =
    "
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            email VARCHAR(256) UNIQUE NOT NULL,
            username VARCHAR(256) NOT NULL,
            password VARCHAR(256) NOT NULL,
            is_admin INTEGER NOT NULL DEFAULT 0,
            created_at DATETIME,
            updated_at DATETIME
        );
    ";
    const user_trigger_on_create = 
    "
        CREATE TRIGGER insert_user
        AFTER INSERT ON users
        BEGIN
            UPDATE users SET created_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
            UPDATE users SET updated_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
        END;
    ";
    const user_trigger_on_update =
    "
        CREATE TRIGGER update_user
        AFTER UPDATE ON users
        BEGIN
            UPDATE users SET updated_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
        END;
    ";

    const query_create_admin = 
    "
        INSERT INTO users (email, username, password, is_admin) 
        VALUES ('kadek@kadek.com', 'kadek', 'kadek', 1);
    ";

    public function __construct()
    {
        try {
            $this->db = new \PDO("sqlite:db.tubes1wbd");
        } catch (\PDOException $e) {
            die("Error! " . $e->getMessage());
        }
    }

    public function migrate()
    {
        if ($this->db->query(InitDB::query_create_table_user)) {
            $this->db->query(InitDB::user_trigger_on_create);
            $this->db->query(InitDB::user_trigger_on_update);
            echo "query success";
        } else {
            echo "query failed";
        }

        $this->db->exec(InitDB::query_create_admin);
    }
}

$initDB = new InitDB();
$initDB->migrate();

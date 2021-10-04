<?php

namespace DB;

final class InitDB
{
    private $db;
    /* user table */
    private $query_create_table_users = [
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
        ",
        "
            CREATE TRIGGER IF NOT EXISTS insert_user
            AFTER INSERT ON users
            BEGIN
                UPDATE users SET created_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
                UPDATE users SET updated_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
            END;
        ",
        "
            CREATE TRIGGER IF NOT EXISTS update_user
            AFTER UPDATE ON users
            BEGIN
                UPDATE users SET updated_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
            END;
        "
    ];

    private $query_create_admin =
    "
        INSERT INTO users (email, username, password, is_admin) 
        VALUES ('kadek@kadek.com', 'kadek', 'kadek', 1);
    ";

    /* dorayaki table */
    private $query_create_table_dorayakis = [
        "
            CREATE TABLE IF NOT EXISTS dorayakis (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                nama VARCHAR(256) UNIQUE NOT NULL,
                deskripsi TEXT,
                harga INTEGER NOT NULL,
                stok INTEGER NOT NULL,
                gambar TEXT NOT NULL,
                created_at DATETIME,
                updated_at DATETIME
            );
        ",
        "
            CREATE TRIGGER IF NOT EXISTS insert_dorayaki
            AFTER INSERT ON dorayakis
            BEGIN
                UPDATE dorayakis SET created_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
                UPDATE dorayakis SET updated_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
            END;
        ",
        "
            CREATE TRIGGER IF NOT EXISTS update_dorayaki
            AFTER UPDATE ON dorayakis
            BEGIN
                UPDATE dorayakis SET updated_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
            END;
        "
    ];

    /* user_session table */
    private $query_create_table_user_sessions = [
        "
            CREATE TABLE IF NOT EXISTS user_sessions (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                user_id INTEGER NOT NULL,
                token TEXT NOT NULL,
                lifetime INT NOT NULL,
                created_at DATETIME,
                updated_at DATETIME,
                FOREIGN KEY(user_id) REFERENCES users(id)
            );
        ",
        "
            CREATE TRIGGER IF NOT EXISTS insert_user_session
            AFTER INSERT ON user_sessions
            BEGIN
                UPDATE user_sessions SET created_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
                UPDATE user_sessions SET updated_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
            END;
        ",
        "
            CREATE TRIGGER IF NOT EXISTS update_user_session
            AFTER UPDATE ON user_sessions
            BEGIN
                UPDATE user_sessions SET updated_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
            END;
        "
    ];

    /* dorayaki_activities table */
    private $query_create_table_dorayaki_activities = [
        "
            CREATE TABLE IF NOT EXISTS dorayaki_activities (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                dorayaki_id INTEGER NOT NULL,
                user_id INTEGER NOT NULL,
                action_type VARCHAR(256) NOT NULL,
                state_before TEXT NOT NULL,
                state_after TEXT NOT NULL,
                created_at DATETIME,
                updated_at DATETIME,
                FOREIGN KEY(dorayaki_id) REFERENCES dorayakis(id),
                FOREIGN KEY(user_id) REFERENCES users(id)
            );
        ",
        "
            CREATE TRIGGER insert_dorayaki_activities
            AFTER INSERT ON dorayaki_activities
            BEGIN
                UPDATE dorayaki_activities SET created_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
                UPDATE dorayaki_activities SET updated_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
            END;
        ",
        "
            CREATE TRIGGER update_dorayaki_activities
            AFTER UPDATE ON dorayaki_activities
            BEGIN
                UPDATE dorayaki_activities SET updated_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
            END;
        "
    ];

    private $query_drop_all_tables = [
        "DROP TABLE IF EXISTS users",
        "DROP TABLE IF EXISTS dorayakis",
        "DROP TABLE IF EXISTS user_sessions",
        "DROP TABLE IF EXISTS dorayaki_activities"
    ];

    public function __construct()
    {
        try {
            $this->db = new \PDO("sqlite:tubes1wbd.db");
            $this->db->query('PRAGMA foreign_keys = ON;');
        } catch (\PDOException $e) {
            die("Error! " . $e->getMessage());
        }
    }

    public function migrate()
    {
        foreach ($this->query_drop_all_tables as $query) {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
        }
        foreach ($this->query_create_table_users as $query) {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
        }
        foreach ($this->query_create_table_dorayakis as $query) {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
        }
        foreach ($this->query_create_table_user_sessions as $query) {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
        }
        foreach ($this->query_create_table_dorayaki_activities as $query) {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
        }

        /* Seed the tables */
        $stmt = $this->db->prepare($this->query_create_admin);
        $stmt->execute();
    }
}

$initDB = new InitDB();
$initDB->migrate();

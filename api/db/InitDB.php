<?php
namespace API\DB;
require "./bootstrap.php";

$stmtDropAllTables = <<<EOS
    PRAGMA foreign_keys = OFF;
    DROP TABLE IF EXISTS users;
    DROP TABLE IF EXISTS dorayakis;
    DROP TABLE IF EXISTS user_sessions;
    DROP TABLE IF EXISTS dorayaki_activities;
    DROP TABLE IF EXISTS pembelian_dorayakis;
    PRAGMA foreign_keys = ON;
EOS;

$stmtCreateUsersTable = <<<EOS
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        email VARCHAR(256) NOT NULL,
        username VARCHAR(256) UNIQUE NOT NULL,
        password VARCHAR(256) NOT NULL,
        is_admin INTEGER CHECK((is_admin == 0 OR is_admin == 1) AND (CAST(is_admin||1 AS INTEGER) <> 0)) NOT NULL DEFAULT 0,
        created_at DATETIME,
        updated_at DATETIME
    );

    CREATE TRIGGER IF NOT EXISTS insert_user
    AFTER INSERT ON users
    BEGIN
        UPDATE users SET created_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
        UPDATE users SET updated_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
    END;


    CREATE TRIGGER IF NOT EXISTS update_user
    AFTER UPDATE ON users
    BEGIN
        UPDATE users SET updated_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
    END;
EOS;

$stmtCreateDorayakisTable = <<<EOS
    CREATE TABLE IF NOT EXISTS dorayakis (
        id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        nama VARCHAR(256) NOT NULL,
        deskripsi TEXT NOT NULL,
        harga INTEGER CHECK ((CAST(harga||1 AS INTEGER) <> 0) AND harga >= 0) NOT NULL DEFAULT 0,
        stok INTEGER CHECK ((CAST(stok||1 AS INTEGER) <> 0) AND stok >= 0) NOT NULL DEFAULT 0,
        terjual INTEGER CHECK ((CAST(terjual||1 AS INTEGER) <> 0) AND terjual >= 0) NOT NULL DEFAULT 0,
        gambar TEXT NOT NULL,
        created_at DATETIME,
        updated_at DATETIME
    );

    CREATE TRIGGER IF NOT EXISTS insert_dorayaki
    AFTER INSERT ON dorayakis
    BEGIN
        UPDATE dorayakis SET created_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
        UPDATE dorayakis SET updated_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
    END;

    CREATE TRIGGER IF NOT EXISTS update_dorayaki
    AFTER UPDATE ON dorayakis
    BEGIN
        UPDATE dorayakis SET updated_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
    END;
EOS;

$stmtCreateUserSessionsTable = <<<EOS
    CREATE TABLE IF NOT EXISTS user_sessions (
        id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        user_id INTEGER NOT NULL,
        session_id TEXT NOT NULL,
        is_admin INTEGER NOT NULL,
        created_at DATETIME,
        updated_at DATETIME,
        FOREIGN KEY(user_id) REFERENCES users(id)
    );

    CREATE TRIGGER IF NOT EXISTS insert_user_session
    AFTER INSERT ON user_sessions
    BEGIN
        UPDATE user_sessions SET created_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
        UPDATE user_sessions SET updated_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
    END;

    CREATE TRIGGER IF NOT EXISTS update_user_session
    AFTER UPDATE ON user_sessions
    BEGIN
        UPDATE user_sessions SET updated_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
    END;
EOS;

$stmtCreateDorayakiActivitiesTable = <<<EOS
    CREATE TABLE IF NOT EXISTS dorayaki_activities (
        id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        state_user TEXT NOT NULL,
        action_type VARCHAR(256) NOT NULL,
        state_before TEXT NOT NULL,
        state_after TEXT NOT NULL,
        created_at DATETIME,
        updated_at DATETIME
    );

    CREATE TRIGGER insert_dorayaki_activities
    AFTER INSERT ON dorayaki_activities
    BEGIN
        UPDATE dorayaki_activities SET created_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
        UPDATE dorayaki_activities SET updated_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
    END;
 
    CREATE TRIGGER update_dorayaki_activities
    AFTER UPDATE ON dorayaki_activities
    BEGIN
        UPDATE dorayaki_activities SET updated_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
    END;
EOS;

$stmtCreatePembelianDorayakiTable = <<<EOS
    CREATE TABLE IF NOT EXISTS pembelian_dorayakis (
        id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        dorayaki_id INTEGER NOT NULL,
        dorayaki_nama VARCHAR(256) NOT NULL,
        dorayaki_harga VARCHAR(256) NOT NULL,
        user_id INTEGER NOT NULL,
        jumlah INTEGER NOT NULL,
        created_at DATETIME,
        updated_at DATETIME
    );

    CREATE TRIGGER insert_pembelian_dorayakis
    AFTER INSERT ON pembelian_dorayakis
    BEGIN
        UPDATE pembelian_dorayakis SET created_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
        UPDATE pembelian_dorayakis SET updated_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
    END;
 
    CREATE TRIGGER update_pembelian_dorayakis
    AFTER UPDATE ON pembelian_dorayakis
    BEGIN
        UPDATE pembelian_dorayakis SET updated_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
    END;
EOS;

$stmtCreateUser = <<<EOS
    INSERT INTO users (email, username, password, is_admin) 
    VALUES (:email, :username, :password, :is_admin)
EOS;

try {
    /* Init tables */
    $dbConnection->exec($stmtDropAllTables);
    $dbConnection->exec($stmtCreateUsersTable);
    $dbConnection->exec($stmtCreateDorayakisTable);
    $dbConnection->exec($stmtCreateUserSessionsTable);
    $dbConnection->exec($stmtCreateDorayakiActivitiesTable);
    $dbConnection->exec($stmtCreatePembelianDorayakiTable);

    /* Create Admin User */
    $stmtCreateUserPrep = $dbConnection->prepare($stmtCreateUser);
    $stmtCreateUserPrep->execute(array(
        "email" => "nullPtr@admin.com",
        "username" => "nullPtr",
        "password" => password_hash("nullPtr", PASSWORD_DEFAULT),
        "is_admin" => true,
    ));
    
    echo "Success Initialization of DB!";
} catch(\Exception $e) {
    echo "test";
    exit($e->getMessage());
}

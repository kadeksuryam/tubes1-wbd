<?php
$query_create_table_user = 
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

    CREATE TRIGGER insert_user
    AFTER INSERT ON users
    BEGIN
        UPDATE users SET created_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
        UPDATE users SET updated_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
    END;

    CREATE TRIGGER update_user
    AFTER UPDATE ON users
    BEGIN
        UPDATE users SET updated_at=STRFTIME('%Y-%m-%d %H:%M:%f', 'NOW') WHERE id = NEW.id;
    END;
";
<?php
$sqliteFile = __DIR__ . '/../db/fisiere.sqlite';

if (!is_dir(__DIR__ . '/../db')) {
    mkdir(__DIR__ . '/../db', 0755, true);
}

try {
    $sqlite = new PDO('sqlite:' . $sqliteFile);
    $sqlite->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
    $sqlite->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Bootstrap the table on first connection
    $sqlite->exec("CREATE TABLE IF NOT EXISTS fisiere (
        id            INTEGER PRIMARY KEY AUTOINCREMENT,
        filename      TEXT    NOT NULL,
        original_name TEXT    NOT NULL,
        mime_type     TEXT    NOT NULL,
        size          INTEGER NOT NULL,
        continut      BLOB    NOT NULL,
        tip           TEXT    NOT NULL,
        data_upload   TEXT    DEFAULT (datetime('now'))
    )");
} catch (PDOException $e) {
    die("Eroare conexiune SQLite: " . $e->getMessage());
}

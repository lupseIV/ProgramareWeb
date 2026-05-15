<?php
require_once 'config/database.php';

$queries = [

// ── angajati — extends utilizatori (one-to-one) ──────────────────────────
"CREATE TABLE IF NOT EXISTS angajati (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    utilizator_id   INT NOT NULL UNIQUE,
    nume            TEXT NOT NULL,
    prenume         TEXT NOT NULL,
    cnp             CHAR(13),
    gen             ENUM('M','F') NULL,
    data_nasterii   DATE NULL,
    varsta          TINYINT UNSIGNED NULL,
    email           VARCHAR(150),
    telefon         VARCHAR(20),
    departament     ENUM('IT','HR','SALES','MANAGEMENT','FINANCIAR') NULL,
    functie         VARCHAR(100),
    salariu         DECIMAL(10,2) DEFAULT 0,
    data_angajare   DATE,
    poza_profil_id  INT NULL,
    FOREIGN KEY (utilizator_id) REFERENCES utilizatori(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

// ── comenzi — orders placed by employees ─────────────────────────────────
"CREATE TABLE IF NOT EXISTS comenzi (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    angajat_id      INT NOT NULL,
    tara_client     VARCHAR(100),
    client          VARCHAR(200) NOT NULL,
    adresa_livrare  VARCHAR(300),
    produs_serviciu VARCHAR(200),
    cantitate       INT DEFAULT 1,
    data_livrare    DATE NULL,
    metoda_plata    ENUM('CARD','OP','RAMBURS') DEFAULT 'OP',
    observatii      TEXT,
    valoare         DECIMAL(10,2) DEFAULT 0,
    status          ENUM('PENDING','PROCESATA','LIVRATA','ANULATA') DEFAULT 'PENDING',
    document_id     INT NULL,
    data_comanda    DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (angajat_id) REFERENCES angajati(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

// ── facturi — invoices linked to orders (the required relationship) ───────
"CREATE TABLE IF NOT EXISTS facturi (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    comanda_id      INT NOT NULL,
    numar_factura   VARCHAR(50) NOT NULL UNIQUE,
    data_emitere    DATE NOT NULL,
    data_scadenta   DATE NOT NULL,
    total           DECIMAL(10,2) NOT NULL,
    client          VARCHAR(200),
    descriere       TEXT,
    status          ENUM('EMISA','PLATITA','ANULATA') DEFAULT 'EMISA',
    document_id     INT NULL,
    FOREIGN KEY (comanda_id) REFERENCES comenzi(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

// ── contracte ─────────────────────────────────────────────────────────────
"CREATE TABLE IF NOT EXISTS contracte (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    angajat_id          INT NOT NULL,
    nr_contract         VARCHAR(50) NOT NULL UNIQUE,
    client              VARCHAR(200) NOT NULL,
    cui_client          VARCHAR(20),
    adresa_sediu        VARCHAR(300),
    valoare             DECIMAL(10,2) DEFAULT 0,
    moneda              ENUM('EUR','RON','USD') DEFAULT 'EUR',
    durata_zile         INT DEFAULT 30,
    metoda_plata        ENUM('OP','CARD','TREZ') DEFAULT 'OP',
    servicii            TEXT,
    prioritate          ENUM('std','prm') DEFAULT 'prm',
    notificare_financiar TINYINT(1) DEFAULT 0,
    arhivare_fizica     TINYINT(1) DEFAULT 0,
    observatii          TEXT,
    document_id         INT NULL,
    status              ENUM('ACTIV','EXPIRAT','ANULAT') DEFAULT 'ACTIV',
    data_creare         DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (angajat_id) REFERENCES angajati(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

// ── add missing columns to utilizatori if not present ─────────────────────
"ALTER TABLE utilizatori
    ADD COLUMN IF NOT EXISTS role ENUM('ADMIN','MANAGER','EMPLOYEE') DEFAULT 'EMPLOYEE',
    ADD COLUMN IF NOT EXISTS logged_in TINYINT(1) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS logged_at DATETIME NULL,
    ADD COLUMN IF NOT EXISTS remember_token VARCHAR(64) NULL,
    ADD COLUMN IF NOT EXISTS remember_expiry DATETIME NULL",
];

echo "<pre>";
foreach ($queries as $sql) {
    try {
        $pdo->exec($sql);
        echo "✓ OK: " . substr(trim($sql), 0, 60) . "...\n";
    } catch (PDOException $e) {
        echo "✗ ERR: " . $e->getMessage() . "\n";
    }
}
echo "\nDone. You can delete setup.php now.";
echo "</pre>";

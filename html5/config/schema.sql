CREATE TABLE utilizatori (
                             id INT AUTO_INCREMENT PRIMARY KEY,
                             username VARCHAR(50) NOT NULL UNIQUE,
                             parola VARCHAR(255) NOT NULL
);

ALTER TABLE utilizatori
ADD COLUMN role varchar(50) check in ("EMPLOYEE", "ADMIN", "MANAGER")

ALTER TABLE utilizatori
ADD COLUMN logged_in TINYINT(1) DEFAULT 0,
ADD COLUMN logged_at DATETIME NULL;

ALTER TABLE utilizatori
    ADD COLUMN remember_token VARCHAR(64) NULL,
ADD COLUMN remember_expiry DATETIME NULL;
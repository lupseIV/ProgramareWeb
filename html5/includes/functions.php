<?php
// ── Role helpers ──────────────────────────────────────────────────────────
function isAdmin(): bool   { return ($_SESSION['role'] ?? '') === 'ADMIN';    }
function isManager(): bool { return ($_SESSION['role'] ?? '') === 'MANAGER';  }
function isEmployee(): bool{ return ($_SESSION['role'] ?? '') === 'EMPLOYEE'; }
function isLogged(): bool  { return isset($_SESSION['logat']) && $_SESSION['logat'] === true; }

function hasRole(string ...$roles): bool {
    return in_array($_SESSION['role'] ?? '', $roles);
}

/**
 * Save an uploaded file into SQLite as a BLOB.
 * Returns the new SQLite row ID, or null on failure / no file.
 */
function salveazaFisier(PDO $sqlite, array $file, string $tip): ?int {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK || $file['size'] === 0) {
        return null;
    }
    $continut = file_get_contents($file['tmp_name']);
    if ($continut === false) return null;

    $stmt = $sqlite->prepare(
        "INSERT INTO fisiere (filename, original_name, mime_type, size, continut, tip)
         VALUES (:filename, :original_name, :mime_type, :size, :continut, :tip)"
    );
    $stmt->bindValue(':filename',      uniqid('f_') . '_' . basename($file['name']));
    $stmt->bindValue(':original_name', $file['name']);
    $stmt->bindValue(':mime_type',     $file['type']);
    $stmt->bindValue(':size',          $file['size'], PDO::PARAM_INT);
    $stmt->bindValue(':continut',      $continut,     PDO::PARAM_LOB);
    $stmt->bindValue(':tip',           $tip);
    $stmt->execute();

    return (int) $sqlite->lastInsertId();
}

/**
 * Delete a file from SQLite by ID.
 */
function stergeFisier(PDO $sqlite, int $id): bool {
    $stmt = $sqlite->prepare("DELETE FROM fisiere WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount() > 0;
}

/**
 * Get file metadata (without BLOB) from SQLite.
 */
function getFisierMeta(PDO $sqlite, int $id): ?array {
    $stmt = $sqlite->prepare(
        "SELECT id, filename, original_name, mime_type, size, tip, data_upload
         FROM fisiere WHERE id = :id"
    );
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/**
 * Extract gen, data_nasterii and varsta from a Romanian CNP.
 * Returns array with those keys, all null if CNP is invalid.
 */
function processCNP(string $cnp): array {
    $empty = ['gen' => null, 'data_nasterii' => null, 'varsta' => null];

    if (strlen($cnp) !== 13 || !ctype_digit($cnp)) return $empty;

    $s = (int) $cnp[0];
    $gen = in_array($s, [1, 3, 5, 7, 9]) ? 'M' : 'F';

    $yy = (int) substr($cnp, 1, 2);
    $mm = (int) substr($cnp, 3, 2);
    $dd = (int) substr($cnp, 5, 2);

    $fullYear = match(true) {
        in_array($s, [1, 2]) => 1900 + $yy,
        in_array($s, [3, 4]) => 1800 + $yy,
        in_array($s, [5, 6]) => 2000 + $yy,
        default               => 1900 + $yy,
    };

    try {
        $birth  = new DateTime(sprintf('%04d-%02d-%02d', $fullYear, $mm, $dd));
        $varsta =  $birth->diff(new DateTime())->y;
        return ['gen' => $gen, 'data_nasterii' => $birth->format('Y-m-d'), 'varsta' => $varsta];
    } catch (Exception) {
        return $empty;
    }
}

// ── Auto-number generators ────────────────────────────────────────────────

function genereazaNrContract(PDO $pdo): string {
    $an  = date('Y');
    $row = $pdo->query("SELECT COUNT(*) AS cnt FROM contracte WHERE YEAR(data_creare) = $an")->fetch();
    return 'CTR-' . $an . '-' . str_pad(($row['cnt'] ?? 0) + 1, 3, '0', STR_PAD_LEFT);
}

function genereazaNrComanda(PDO $pdo): string {
    $an  = date('Y');
    $row = $pdo->query("SELECT COUNT(*) AS cnt FROM comenzi WHERE YEAR(data_comanda) = $an")->fetch();
    return 'CMD-' . $an . '-' . str_pad(($row['cnt'] ?? 0) + 1, 4, '0', STR_PAD_LEFT);
}

function genereazaNrFactura(PDO $pdo): string {
    $an  = date('Y');
    $row = $pdo->query("SELECT COUNT(*) AS cnt FROM facturi WHERE YEAR(data_emitere) = $an")->fetch();
    return 'FCT-' . $an . '-' . str_pad(($row['cnt'] ?? 0) + 1, 4, '0', STR_PAD_LEFT);
}

/**
 * Get the angajat record for the currently logged-in user.
 * Uses MySQLi
 */
function getAngajatCurent(mysqli $mysqli): ?array {
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    if (!$userId) return null;

    $stmt = $mysqli->prepare(
        "SELECT a.*, u.username, u.role
         FROM angajati a
         JOIN utilizatori u ON u.id = a.utilizator_id
         WHERE a.utilizator_id = ?"
    );
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row    = $result->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

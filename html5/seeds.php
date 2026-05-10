<?php
/**
 * seeds.php — Insert mock users + employee profiles for testing.
 *
 * Run ONCE via browser: http://localhost/html5/seeds.php
 * Delete this file afterwards.
 *
 * Creates:
 *   ADMIN    → admin_test    / Admin123!
 *   MANAGER  → manager_test  / Manager123!
 *   EMPLOYEE → angajat_test  / Angajat123!
 *
 * Each user gets a matching angajati row so the readonly pre-fill
 * fields (Nume Angajat, ID Operator, Nr. Comandă etc.) work correctly.
 * A sample PENDING comanda is also seeded so the contract form's
 * "Comandă asociată" dropdown is not empty.
 */

require_once 'config/database.php';

echo "<pre style='font-family:monospace;font-size:14px;'>";

// ── Helper ────────────────────────────────────────────────────────────────
function insertUser(PDO $pdo, string $username, string $plainPass, string $role): ?int
{
    // Skip if username already exists
    $chk = $pdo->prepare("SELECT id FROM utilizatori WHERE username = :u");
    $chk->execute([':u' => $username]);
    if ($chk->fetch()) {
        echo "⚠ Utilizatorul «{$username}» există deja — omis.\n";
        return null;
    }

    $pdo->prepare(
        "INSERT INTO utilizatori (username, parola, role)
         VALUES (:u, :p, :r)"
    )->execute([
        ':u' => $username,
        ':p' => password_hash($plainPass, PASSWORD_DEFAULT),
        ':r' => $role,
    ]);
    $id = (int) $pdo->lastInsertId();
    echo "✓ Utilizator creat: {$username} (id={$id}, role={$role})\n";
    return $id;
}

function insertAngajat(PDO $pdo, int $utilizatorId, array $data): void
{
    $chk = $pdo->prepare("SELECT id FROM angajati WHERE utilizator_id = :uid");
    $chk->execute([':uid' => $utilizatorId]);
    if ($chk->fetch()) {
        echo "  ⚠ Profil angajat deja există pentru utilizator_id={$utilizatorId} — omis.\n";
        return;
    }

    $pdo->prepare(
        "INSERT INTO angajati
            (utilizator_id, nume, prenume, cnp, gen, data_nasterii, varsta,
             email, telefon, departament, functie, salariu, data_angajare)
         VALUES
            (:uid, :n, :pn, :cnp, :g, :dn, :v, :em, :tel, :dep, :fn, :sal, :da)"
    )->execute([
        ':uid' => $utilizatorId,
        ':n'   => $data['nume'],
        ':pn'  => $data['prenume'],
        ':cnp' => $data['cnp'],
        ':g'   => $data['gen'],
        ':dn'  => $data['data_nasterii'],
        ':v'   => $data['varsta'],
        ':em'  => $data['email'],
        ':tel' => $data['telefon'],
        ':dep' => $data['departament'],
        ':fn'  => $data['functie'],
        ':sal' => $data['salariu'],
        ':da'  => $data['data_angajare'],
    ]);
    echo "  ✓ Profil angajat creat: {$data['nume']} {$data['prenume']}\n";
}

// ── 1. ADMIN ──────────────────────────────────────────────────────────────
$adminId = insertUser($pdo, 'admin_test', 'Admin123!', 'ADMIN');
if ($adminId) {
    insertAngajat($pdo, $adminId, [
        'nume'          => 'Popescu',
        'prenume'       => 'Alexandru',
        'cnp'           => '1850315014987',
        'gen'           => 'M',
        'data_nasterii' => '1985-03-15',
        'varsta'        => (int) date_diff(date_create('1985-03-15'), date_create('today'))->y,
        'email'         => 'admin@erp.ro',
        'telefon'       => '0721000001',
        'departament'   => 'MANAGEMENT',
        'functie'       => 'Director General',
        'salariu'       => 12000.00,
        'data_angajare' => '2018-01-15',
    ]);
}

// ── 2. MANAGER ───────────────────────────────────────────────────────────
$managerId = insertUser($pdo, 'manager_test', 'Manager123!', 'MANAGER');
if ($managerId) {
    insertAngajat($pdo, $managerId, [
        'nume'          => 'Ionescu',
        'prenume'       => 'Maria',
        'cnp'           => '2900622236541',
        'gen'           => 'F',
        'data_nasterii' => '1990-06-22',
        'varsta'        => (int) date_diff(date_create('1990-06-22'), date_create('today'))->y,
        'email'         => 'manager@erp.ro',
        'telefon'       => '0722000002',
        'departament'   => 'HR',
        'functie'       => 'Manager Resurse Umane',
        'salariu'       => 8500.00,
        'data_angajare' => '2019-04-01',
    ]);
}

// ── 3. EMPLOYEE ───────────────────────────────────────────────────────────
$empId = insertUser($pdo, 'angajat_test', 'Angajat123!', 'EMPLOYEE');
if ($empId) {
    insertAngajat($pdo, $empId, [
        'nume'          => 'Dumitrescu',
        'prenume'       => 'Andrei',
        'cnp'           => '1950910013285',
        'gen'           => 'M',
        'data_nasterii' => '1995-09-10',
        'varsta'        => (int) date_diff(date_create('1995-09-10'), date_create('today'))->y,
        'email'         => 'angajat@erp.ro',
        'telefon'       => '0733000003',
        'departament'   => 'IT',
        'functie'       => 'Programator',
        'salariu'       => 5500.00,
        'data_angajare' => '2022-09-01',
    ]);
}

$angajatAdmin = $pdo->prepare("SELECT a.id FROM angajati a JOIN utilizatori u ON u.id = a.utilizator_id WHERE u.username = 'admin_test'");
$angajatAdmin->execute();
$angajatRow = $angajatAdmin->fetch();

if ($angajatRow) {
    $angajatId = $angajatRow['id'];

    $hasCmd = $pdo->query("SELECT COUNT(*) FROM comenzi WHERE client = 'TechCorp SRL'")->fetchColumn();
    if (!$hasCmd) {
        $an  = date('Y');
        $cnt = (int) $pdo->query("SELECT COUNT(*) FROM comenzi WHERE YEAR(data_comanda) = $an")->fetchColumn();
        $nr  = 'CMD-' . $an . '-' . str_pad($cnt + 1, 4, '0', STR_PAD_LEFT);

        $pdo->prepare(
            "INSERT INTO comenzi
                (angajat_id, tara_client, client, adresa_livrare, produs_serviciu,
                 cantitate, data_livrare, metoda_plata, observatii, valoare, status)
             VALUES
                (:aid, 'Romania', 'TechCorp SRL', 'Str. Victoriei 12, București',
                 'Licență ERP Core', 1, DATE_ADD(CURDATE(), INTERVAL 30 DAY),
                 'OP', 'Comandă demo pentru testare contracte.', 15000.00, 'PENDING')"
        )->execute([':aid' => $angajatId]);

        echo "\n✓ Comandă demo adăugată: {$nr} — TechCorp SRL (15.000 EUR, PENDING)\n";
    } else {
        echo "\n⚠ Comanda demo există deja — omisă.\n";
    }
}

echo <<<EOT

  ADMIN    → admin_test    / Admin123!
  MANAGER  → manager_test  / Manager123!
  EMPLOYEE → angajat_test  / Angajat123!
EOT;

echo "</pre>";

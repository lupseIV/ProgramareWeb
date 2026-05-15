<?php
if (!defined('APP_RUNNING')) { header('Location: /?page=home'); exit; }
if (!hasRole('ADMIN', 'MANAGER')) { header('Location: ?page=dashboard'); exit; }

$pageTitle = "Înregistrare Angajat";
$styles    = ["styles/adauga_contract.css"];
$scripts    = ["scripts/adauga_angajat.js"];

$success = false;
$errors  = [];
$cnpData = ['gen' => null, 'data_nasterii' => null, 'varsta' => null];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username     = trim($_POST['username']     ?? '');
    $parola       = trim($_POST['parola']        ?? '');
    $nume         = trim($_POST['nume']          ?? '');
    $prenume      = trim($_POST['prenume']       ?? '');
    $cnp          = trim($_POST['cnp']           ?? '');
    $email        = trim($_POST['email']         ?? '');
    $telefon      = trim($_POST['telefon']       ?? '');
    $departament  = trim($_POST['departament']   ?? '');
    $functie      = trim($_POST['functie']       ?? '');
    $salariu      = trim($_POST['salariu']       ?? '0');
    $data_ang     = trim($_POST['data_angajare'] ?? date('Y-m-d'));
    $role         = isAdmin() ? trim($_POST['role'] ?? 'EMPLOYEE') : 'EMPLOYEE';

    // Validate
    if (!$username) $errors[] = 'Username-ul este obligatoriu.';
    if (!$parola)   $errors[] = 'Parola este obligatorie.';
    if (!$nume)     $errors[] = 'Numele este obligatoriu.';
    if (!$prenume)  $errors[] = 'Prenumele este obligatoriu.';
    if (!in_array($role, ['EMPLOYEE', 'MANAGER'])) $role = 'EMPLOYEE';

    if ($cnp) $cnpData = processCNP($cnp);

    if (empty($errors)) {
        try {
            $chk = $pdo->prepare("SELECT id FROM utilizatori WHERE username = :u");
            $chk->execute([':u' => $username]);
            if ($chk->fetch()) {
                $errors[] = 'Username-ul există deja.';
            } else {
                $pdo->prepare(
                    "INSERT INTO utilizatori (username, parola, role)
                     VALUES (:u, :p, :r)"
                )->execute([
                    ':u' => $username,
                    ':p' => md5($parola),
                    ':r' => $role,
                ]);
                $utilizatorId = (int) $pdo->lastInsertId();

//                $cvId   = salveazaFisier($sqlite, $_FILES['cv_atasat']   ?? [], 'cv');
                $cvId = null;
                if (isset($_FILES['cv_atasat']) && $_FILES['cv_atasat']['error'] === UPLOAD_ERR_OK) {
                    $numeFisier = $_FILES['cv_atasat']['name'];
                    move_uploaded_file($_FILES['cv_atasat']['tmp_name'], __DIR__ . '/../uploads/' . $numeFisier);
                }
                $gdprId = salveazaFisier($sqlite, $_FILES['acord_gdpr']  ?? [], 'gdpr');

                $docRef = $cvId;

                $pdo->prepare(
                    "INSERT INTO angajati
                        (utilizator_id, nume, prenume, cnp, gen, data_nasterii, varsta,
                         email, telefon, departament, functie, salariu, data_angajare, poza_profil_id)
                     VALUES
                        (:uid, :n, :pn, :cnp, :g, :dn, :v, :em, :tel, :dep, :fn, :sal, :da, :pid)"
                )->execute([
                    ':uid' => $utilizatorId,
                    ':n'   => $nume,
                    ':pn'  => $prenume,
                    ':cnp' => $cnp ?: null,
                    ':g'   => $cnpData['gen'],
                    ':dn'  => $cnpData['data_nasterii'],
                    ':v'   => $cnpData['varsta'],
                    ':em'  => $email ?: null,
                    ':tel' => $telefon ?: null,
                    ':dep' => $departament ?: null,
                    ':fn'  => $functie ?: null,
                    ':sal' => (float) $salariu,
                    ':da'  => $data_ang,
                    ':pid' => $docRef,
                ]);

                $success = true;
            }
        } catch (PDOException $e) {
            $errors[] = 'Eroare bază de date: ' . $e->getMessage();
        }
    }
}
?>

<main>
  <section id="add-employee">
    <h1>Înregistrare Angajat Nou</h1>

    <?php if ($success): ?>
      <div class="alert alert-success">✓ Angajatul a fost înregistrat cu succes.</div>
    <?php endif; ?>
    <?php foreach ($errors as $e): ?>
      <div class="alert alert-error"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>


    <form id="form-angajat" action="?page=adauga_angajat" method="post" enctype="multipart/form-data" novalidate>

      <fieldset>
        <legend>Cont Utilizator</legend>
        <div class="form-row">
          <label for="username">Username:</label>
          <input type="text" id="username" name="username" required
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
        <div class="form-row">
          <label for="parola">Parolă inițială:</label>
          <input type="password" id="parola" name="parola" required>
        </div>
        <div class="form-row">
          <label for="role">Rol:</label>
          <?php if (isAdmin()): ?>
            <select id="role" name="role">
              <option value="EMPLOYEE" <?= ($_POST['role'] ?? '') === 'EMPLOYEE' ? 'selected' : '' ?>>Angajat</option>
              <option value="MANAGER"  <?= ($_POST['role'] ?? '') === 'MANAGER'  ? 'selected' : '' ?>>Manager</option>
            </select>
          <?php else: ?>
            <!-- Manager adds only employees -->
            <input type="text" value="Angajat (EMPLOYEE)" readonly style="cursor:not-allowed;background:#f1f5f9;">
            <input type="hidden" name="role" value="EMPLOYEE">
          <?php endif; ?>
        </div>
      </fieldset>

      <fieldset>
        <legend>Date Personale</legend>
        <div class="form-row">
          <label for="nume">Nume:</label>
          <input type="text" id="nume" name="nume" required
                 value="<?= htmlspecialchars($_POST['nume'] ?? '') ?>">
        </div>
        <div class="form-row">
          <label for="prenume">Prenume:</label>
          <input type="text" id="prenume" name="prenume" required
                 value="<?= htmlspecialchars($_POST['prenume'] ?? '') ?>">
        </div>
        <div class="form-row">
          <label for="cnp">CNP:</label>
          <input type="text" id="cnp" name="cnp" maxlength="13" placeholder="13 cifre"
                 value="<?= htmlspecialchars($_POST['cnp'] ?? '') ?>">
        </div>

        <!-- Readonly fields — auto-filled from CNP after POST -->
        <div class="form-row">
          <label>Gen (auto din CNP):</label>
          <input type="text" id="gen_display" readonly style="cursor:not-allowed;background:#f1f5f9;"
                 value="<?= $cnpData['gen'] ? ($cnpData['gen'] === 'M' ? 'Masculin' : 'Feminin') : '' ?>">
          <input type="hidden" id="gen_hidden" name="gen" value="<?= htmlspecialchars($cnpData['gen'] ?? '') ?>">
        </div>
        <div class="form-row">
          <label>Data Nașterii (auto):</label>
          <input type="date" id="data_nasterii_display" readonly style="cursor:not-allowed;background:#f1f5f9;"
                 value="<?= htmlspecialchars($cnpData['data_nasterii'] ?? '') ?>">
        </div>
        <div class="form-row">
          <label>Vârstă (auto):</label>
          <input type="number" id="varsta_display" readonly style="cursor:not-allowed;background:#f1f5f9;"
                 value="<?= htmlspecialchars($cnpData['varsta'] ?? '') ?>">
        </div>

        <div class="form-row">
          <label for="email">Email:</label>
          <input type="email" id="email" name="email"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-row">
          <label for="telefon">Telefon:</label>
          <input type="text" id="telefon" name="telefon"
                 value="<?= htmlspecialchars($_POST['telefon'] ?? '') ?>">
        </div>
      </fieldset>

      <fieldset>
        <legend>Detalii Profesionale</legend>
        <div class="form-row">
          <label for="departament">Departament:</label>
          <select id="departament" name="departament">
            <option value="">-- Selectează --</option>
            <?php foreach (['IT','HR','SALES','MANAGEMENT','FINANCIAR'] as $dep): ?>
              <option value="<?= $dep ?>"
                <?= ($_POST['departament'] ?? '') === $dep ? 'selected' : '' ?>>
                <?= $dep ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-row">
          <label for="functie">Funcție:</label>
          <input type="text" id="functie" name="functie"
                 value="<?= htmlspecialchars($_POST['functie'] ?? '') ?>">
        </div>
        <div class="form-row">
          <label for="salariu">Salariu (RON):</label>
          <input type="number" id="salariu" name="salariu" step="100" min="0"
                 value="<?= htmlspecialchars($_POST['salariu'] ?? '0') ?>">
        </div>
        <div class="form-row">
          <label for="data_angajare">Data Angajării:</label>
          <!-- Pre-filled with today's date from server -->
          <input type="date" id="data_angajare" name="data_angajare"
                 value="<?= htmlspecialchars($_POST['data_angajare'] ?? date('Y-m-d')) ?>">
        </div>
      </fieldset>

      <fieldset>
        <legend>Atașamente (salvate în SQLite)</legend>
        <div class="form-row">
          <label for="cv_atasat">CV (PDF):</label>
          <input type="file" id="cv_atasat" name="cv_atasat" >
        </div>
        <div class="form-row">
          <label for="acord_gdpr">Acord GDPR (PDF):</label>
          <input type="file" id="acord_gdpr" name="acord_gdpr" >
        </div>
      </fieldset>

      <div class="form-actions">
          <input type="reset" value="Anulează" onclick="window.location.href='?page=dashboard'; return false;">

          <input type="submit" value="Salvează Angajatul">
      </div>
    </form>
  </section>
</main>

<?php
if (!defined('APP_RUNNING')) { header('Location: /?page=home'); exit; }
if (!hasRole('ADMIN', 'MANAGER')) { header('Location: ?page=dashboard'); exit; }

$pageTitle = "Adăugare Contract";
$styles    = ["styles/adauga_contract.css"];
$scripts   = ["scripts/adauga_contract.js"];

$angajat    = getAngajatCurent($mysqli);
$nrContract = genereazaNrContract($pdo);
$idOperator = 'OP-' . str_pad($_SESSION['user_id'] ?? 0, 4, '0', STR_PAD_LEFT);

$comenziDeschise = $pdo->query(
    "SELECT c.id, c.client, c.valoare, c.data_comanda
     FROM comenzi c
     WHERE c.status = 'PENDING'
     ORDER BY c.data_comanda DESC
     LIMIT 50"
)->fetchAll();

$success = false;
$errors  = [];

// ── POST handler ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $client       = trim($_POST['client']       ?? '');
    $cui          = trim($_POST['cui_client']   ?? '');
    $adresa       = trim($_POST['adresa_sediu'] ?? '');
    $valoare      = (float) ($_POST['valoare']  ?? 0);
    $moneda       = trim($_POST['moneda']       ?? 'EUR');
    $durata       = (int)  ($_POST['durata']    ?? 30);
    $metoda       = trim($_POST['metoda_plata'] ?? 'OP');
    $servicii     = implode(',', $_POST['servicii'] ?? []);
    $prioritate   = trim($_POST['prioritate']   ?? 'prm');
    $notificare   = isset($_POST['notificare']) ? 1 : 0;
    $arhiva       = isset($_POST['arhiva'])     ? 1 : 0;
    $observatii   = trim($_POST['observatii']   ?? '');
    $comanda_id   = (int) ($_POST['comanda_id'] ?? 0);
    $parola_valid = $_POST['parola_validare'] ?? '';

    if (!$client)  $errors[] = 'Clientul este obligatoriu.';
    if (!$angajat) $errors[] = 'Profilul dvs. de angajat nu există.';

    // Validate operator password
    if ($parola_valid) {
        $chk = $pdo->prepare("SELECT parola FROM utilizatori WHERE id = :id");
        $chk->execute([':id' => $_SESSION['user_id']]);
        $row = $chk->fetch();
        if (!$row || !password_verify($parola_valid, $row['parola'])) {
            $errors[] = 'Parola de validare incorectă.';
        }
    }

    if (empty($errors)) {
        try {
            $docId = salveazaFisier($sqlite, $_FILES['fisier_atasat'] ?? [], 'contract');

            $pdo->prepare(
                "INSERT INTO contracte
                    (angajat_id, nr_contract, client, cui_client, adresa_sediu,
                     valoare, moneda, durata_zile, metoda_plata, servicii, prioritate,
                     notificare_financiar, arhivare_fizica, observatii, document_id)
                 VALUES
                    (:aid, :nr, :cl, :cui, :adr, :val, :mon, :dur, :mp, :svc,
                     :prio, :notif, :arh, :obs, :did)"
            )->execute([
                ':aid'   => $angajat['id'],
                ':nr'    => $nrContract,
                ':cl'    => $client,
                ':cui'   => $cui,
                ':adr'   => $adresa,
                ':val'   => $valoare,
                ':mon'   => $moneda,
                ':dur'   => $durata,
                ':mp'    => $metoda,
                ':svc'   => $servicii,
                ':prio'  => $prioritate,
                ':notif' => $notificare,
                ':arh'   => $arhiva,
                ':obs'   => $observatii,
                ':did'   => $docId,
            ]);
            $contractId = (int) $pdo->lastInsertId();

            // Auto-generate invoice if a comanda was linked
            if ($comanda_id > 0) {
                $cmd = $pdo->prepare("SELECT valoare, client FROM comenzi WHERE id = :id");
                $cmd->execute([':id' => $comanda_id]);
                $comanda = $cmd->fetch();
                if ($comanda) {
                    $pdo->prepare(
                        "INSERT INTO facturi
                            (comanda_id, numar_factura, data_emitere, data_scadenta,
                             total, client, descriere, document_id)
                         VALUES (:cid, :nrf, CURDATE(),
                                 DATE_ADD(CURDATE(), INTERVAL 30 DAY),
                                 :total, :client, :desc, :did)"
                    )->execute([
                        ':cid'    => $comanda_id,
                        ':nrf'    => genereazaNrFactura($pdo),
                        ':total'  => $comanda['valoare'],
                        ':client' => $comanda['client'],
                        ':desc'   => "Contract $nrContract",
                        ':did'    => $docId,
                    ]);
                }
            }

            $success    = true;
            $nrContract = genereazaNrContract($pdo);
        } catch (PDOException $e) {
            $errors[] = 'Eroare bază de date: ' . $e->getMessage();
        }
    }
}
?>

<main>
  <section id="add-contract">
    <h1>Adăugare Contract</h1>

    <?php if ($success): ?>
      <div class="alert alert-success">✓ Contractul a fost înregistrat cu succes.</div>
    <?php endif; ?>
    <?php foreach ($errors as $e): ?>
      <div class="alert alert-error"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>

    <form id="add-contract-form" action="?page=adauga_contract" method="post" enctype="multipart/form-data" novalidate>

      <fieldset>
        <legend>Operator</legend>
        <div class="form-row">
          <label>Nume Angajat:</label>
          <input type="text" readonly style="cursor:not-allowed;background:#f1f5f9;"
                 value="<?= htmlspecialchars(($angajat['nume'] ?? '') . ' ' . ($angajat['prenume'] ?? '') ?: $_SESSION['username']) ?>">
        </div>
        <div class="form-row">
          <label>ID Operator:</label>
          <input type="text" readonly style="cursor:not-allowed;background:#f1f5f9;"
                 value="<?= htmlspecialchars($idOperator) ?>">
        </div>
        <div class="form-row">
          <label>Nr. Contract:</label>
          <!-- Auto-generated from DB count -->
          <input type="text" readonly style="cursor:not-allowed;background:#f1f5f9;"
                 value="<?= htmlspecialchars($nrContract) ?>">
        </div>
        <div class="form-row">
          <label for="parola_validare">Parolă Validare:</label>
          <input type="password" id="parola_validare" name="parola_validare">
        </div>
      </fieldset>

      <fieldset>
        <legend>Date Contractuale Client</legend>
        <div class="form-row">
          <label for="client">Denumire Client:</label>
          <input type="text" id="client" name="client" maxlength="200" required
                 value="<?= htmlspecialchars($_POST['client'] ?? '') ?>">
        </div>
        <div class="form-row">
          <label for="cui_client">CUI:</label>
          <input type="text" id="cui_client" name="cui_client" maxlength="12"
                 value="<?= htmlspecialchars($_POST['cui_client'] ?? '') ?>">
        </div>
        <div class="form-row">
          <label for="adresa_sediu">Adresă Sediu:</label>
          <input type="text" id="adresa_sediu" name="adresa_sediu" maxlength="300"
                 value="<?= htmlspecialchars($_POST['adresa_sediu'] ?? '') ?>">
        </div>
      </fieldset>

      <fieldset>
        <legend>Detalii Financiare</legend>
        <div class="form-row">
          <label for="valoare">Valoare:</label>
          <input type="number" id="valoare" name="valoare" step="100" min="0"
                 value="<?= htmlspecialchars($_POST['valoare'] ?? '5000') ?>">
        </div>
        <div class="form-row">
          <label for="moneda">Monedă:</label>
          <select id="moneda" name="moneda">
            <?php foreach (['EUR' => 'Euro (EUR)', 'RON' => 'Lei (RON)', 'USD' => 'Dolari (USD)'] as $val => $label): ?>
              <option value="<?= $val ?>" <?= ($_POST['moneda'] ?? 'EUR') === $val ? 'selected' : '' ?>>
                <?= $label ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-row">
          <label for="durata">Durată (Zile):</label>
          <input type="number" id="durata" name="durata" step="5" min="1" max="365"
                 value="<?= (int) ($_POST['durata'] ?? 30) ?>">
        </div>
        <div class="form-row">
          <label for="metoda_plata">Metodă Plată:</label>
          <select id="metoda_plata" name="metoda_plata">
            <?php foreach (['OP' => 'Ordin Plată', 'CARD' => 'Card Bancar', 'TREZ' => 'Trezorerie'] as $val => $label): ?>
              <option value="<?= $val ?>" <?= ($_POST['metoda_plata'] ?? 'OP') === $val ? 'selected' : '' ?>>
                <?= $label ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </fieldset>

      <fieldset>
        <legend>Tip Contract</legend>
        <div class="form-row" style="align-items:flex-start">
          <label for="servicii">Servicii:</label>
          <select id="servicii" name="servicii[]" multiple size="4">
            <?php
            $sel = $_POST['servicii'] ?? [];
            foreach (['audit' => 'Audit Gestiune', 'consultanta' => 'Consultanță Resurse',
                      'it_support' => 'Suport IT', 'leasing' => 'Externalizare Logistică'] as $val => $label):
            ?>
              <option value="<?= $val ?>" <?= in_array($val, $sel) ? 'selected' : '' ?>>
                <?= $label ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <p>
          Prioritate:
          <input type="radio" id="prio_std" name="prioritate" value="std"
            <?= ($_POST['prioritate'] ?? 'prm') === 'std' ? 'checked' : '' ?>>
          <label for="prio_std">Standard</label>
          <input type="radio" id="prio_prm" name="prioritate" value="prm"
            <?= ($_POST['prioritate'] ?? 'prm') === 'prm' ? 'checked' : '' ?>>
          <label for="prio_prm">Premium</label>
        </p>
      </fieldset>

      <fieldset>
        <legend>Legătură cu Comandă (generează factură automată)</legend>
        <div class="form-row">
          <label for="comanda_id">Comandă asociată:</label>
          <select id="comanda_id" name="comanda_id">
            <option value="0">-- Fără legătură --</option>
            <?php foreach ($comenziDeschise as $cmd): ?>
              <option value="<?= $cmd['id'] ?>"
                <?= ((int)($_POST['comanda_id'] ?? 0)) === $cmd['id'] ? 'selected' : '' ?>>
                #<?= $cmd['id'] ?> — <?= htmlspecialchars($cmd['client']) ?>
                (<?= number_format($cmd['valoare'], 2) ?> EUR)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </fieldset>

      <fieldset>
        <legend>Fișiere și Observații (fișier salvat în SQLite)</legend>
        <p>
          <input type="checkbox" id="notificare" name="notificare"
            <?= isset($_POST['notificare']) ? 'checked' : 'checked' ?>>
          <label for="notificare">Notifică departament financiar</label>
          &nbsp;
          <input type="checkbox" id="arhiva" name="arhiva"
            <?= isset($_POST['arhiva']) ? 'checked' : '' ?>>
          <label for="arhiva">Arhivare fizică necesară</label>
        </p>
        <div class="form-row">
          <label for="fisier_atasat">Copie Contract (PDF):</label>
          <input type="file" id="fisier_atasat" name="fisier_atasat" accept=".pdf,.doc,.docx">
        </div>
        <div class="form-row" style="align-items:flex-start">
          <label for="observatii">Observații:</label>
          <textarea id="observatii" name="observatii" rows="4"
                    style="width:60%"><?= htmlspecialchars($_POST['observatii'] ?? '') ?></textarea>
        </div>
      </fieldset>

      <div class="form-actions">
        <input type="reset" value="Anulează" onclick="window.location.href='?page=dashboard'; return false;">
        <input type="submit" value="Înregistrare">
      </div>
        </form>
  </section>
    </main>
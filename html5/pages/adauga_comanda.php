<?php
if (!defined('APP_RUNNING')) { header('Location: /?page=home'); exit; }

$pageTitle = "Plasare Comandă";
$styles    = ["styles/adauga_contract.css"];
$scripts   = ["scripts/adauga_comanda.js"];

// ── Pre-fill data using MySQLi ────────────────────────────────────────────
$angajat   = getAngajatCurent($mysqli);
$nrComanda = genereazaNrComanda($pdo);

$success = false;
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $client       = trim($_POST['client']         ?? '');
    $tara         = trim($_POST['tara_client']    ?? '');
    $adresa       = trim($_POST['adresa_livrare'] ?? '');
    $produs       = trim($_POST['produs_serviciu']?? '');
    $cantitate    = (int) ($_POST['cantitate']    ?? 1);
    $data_liv     = trim($_POST['data_livrare']   ?? '');
    $metoda       = trim($_POST['metoda_plata']   ?? 'OP');
    $observatii   = trim($_POST['observatii']     ?? '');
    $valoare      = (float) ($_POST['valoare']    ?? 0);

    if (!$client)  $errors[] = 'Clientul este obligatoriu.';
    if (!$produs)  $errors[] = 'Produsul/Serviciul este obligatoriu.';
    if (!$angajat) $errors[] = 'Profilul dvs. de angajat nu există. Contactați un administrator.';

    if (empty($errors)) {
        try {
            $docId = salveazaFisier($sqlite, $_FILES['document_comanda'] ?? [], 'comanda');

            $pdo->prepare(
                "INSERT INTO comenzi
                    (angajat_id, tara_client, client, adresa_livrare, produs_serviciu,
                     cantitate, data_livrare, metoda_plata, observatii, valoare, document_id)
                 VALUES
                    (:aid, :tc, :cl, :adr, :ps, :cant, :dl, :mp, :obs, :val, :did)"
            )->execute([
                ':aid'  => $angajat['id'],
                ':tc'   => $tara,
                ':cl'   => $client,
                ':adr'  => $adresa,
                ':ps'   => $produs,
                ':cant' => $cantitate,
                ':dl'   => $data_liv ?: null,
                ':mp'   => $metoda,
                ':obs'  => $observatii,
                ':val'  => $valoare,
                ':did'  => $docId,
            ]);
            $success = true;
            $nrComanda = genereazaNrComanda($pdo); // refresh for display
        } catch (PDOException $e) {
            $errors[] = 'Eroare bază de date: ' . $e->getMessage();
        }
    }
}
?>

<main>
  <section id="add-order">
    <h1>Plasare Comandă Nouă</h1>

    <?php if ($success): ?>
      <div class="alert alert-success">✓ Comanda a fost înregistrată cu succes.</div>
    <?php endif; ?>
    <?php foreach ($errors as $e): ?>
      <div class="alert alert-error"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>

    <form id="form-comanda" action="?page=adauga_comanda" method="post" enctype="multipart/form-data" novalidate>

      <fieldset>
        <legend>Operator (precompletat din baza de date)</legend>
        <div class="form-row">
          <label>Angajat Responsabil:</label>
          <!-- Pre-filled readonly from angajati via MySQLi -->
          <input type="text" readonly style="cursor:not-allowed;background:#f1f5f9;"
                 value="<?= htmlspecialchars(($angajat['nume'] ?? '') . ' ' . ($angajat['prenume'] ?? '') ?: $_SESSION['username']) ?>">
        </div>
        <div class="form-row">
          <label>ID Operator:</label>
          <input type="text" readonly style="cursor:not-allowed;background:#f1f5f9;"
                 value="OP-<?= str_pad($_SESSION['user_id'] ?? 0, 4, '0', STR_PAD_LEFT) ?>">
        </div>
        <div class="form-row">
          <label>Nr. Comandă:</label>
          <input type="text" readonly style="cursor:not-allowed;background:#f1f5f9;"
                 value="<?= htmlspecialchars($nrComanda) ?>">
        </div>
        <div class="form-row">
          <label>Data Comenzii:</label>
          <!-- Pre-filled with today from server -->
          <input type="date" readonly style="cursor:not-allowed;background:#f1f5f9;"
                 value="<?= date('Y-m-d') ?>">
        </div>
      </fieldset>

      <fieldset>
        <legend>Detalii Client</legend>
        <div class="form-row">
          <label for="tara_client">Țara Clientului:</label>
          <select id="tara_client" name="tara_client">
            <option value="">-- Alege Țara --</option>
            <?php foreach (['Romania', 'Germania', 'Italia', 'Franta', 'Spania'] as $tara): ?>
              <option value="<?= $tara ?>"
                <?= ($_POST['tara_client'] ?? '') === $tara ? 'selected' : '' ?>>
                <?= $tara ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-row">
          <label for="client">Client:</label>
          <input type="text" id="client" name="client" required
                 placeholder="Denumire client"
                 value="<?= htmlspecialchars($_POST['client'] ?? '') ?>">
        </div>
        <div class="form-row">
          <label for="adresa_livrare">Adresa de Livrare:</label>
          <input type="text" id="adresa_livrare" name="adresa_livrare"
                 placeholder="Strada, Nr, Oraș"
                 value="<?= htmlspecialchars($_POST['adresa_livrare'] ?? '') ?>">
        </div>
      </fieldset>

      <fieldset>
        <legend>Specificații Comandă</legend>
        <div class="form-row">
          <label for="produs_serviciu">Produs / Serviciu:</label>
          <input type="text" id="produs_serviciu" name="produs_serviciu" required
                 placeholder="Ex: Licență ERP Core"
                 value="<?= htmlspecialchars($_POST['produs_serviciu'] ?? '') ?>">
        </div>
        <div class="form-row">
          <label for="cantitate">Cantitate:</label>
          <input type="number" id="cantitate" name="cantitate" min="1"
                 value="<?= (int) ($_POST['cantitate'] ?? 1) ?>">
        </div>
        <div class="form-row">
          <label for="valoare">Valoare (EUR):</label>
          <input type="number" id="valoare" name="valoare" step="0.01" min="0"
                 value="<?= htmlspecialchars($_POST['valoare'] ?? '0') ?>">
        </div>
        <div class="form-row">
          <label for="data_livrare">Data Livrării:</label>
          <input type="date" id="data_livrare" name="data_livrare"
                 value="<?= htmlspecialchars($_POST['data_livrare'] ?? '') ?>">
        </div>
      </fieldset>

      <fieldset>
        <legend>Metodă de Plată</legend>
        <p>
          <?php foreach (['CARD' => 'Card Bancar', 'OP' => 'Ordin de Plată', 'RAMBURS' => 'Ramburs'] as $val => $label): ?>
            <input type="radio" id="plata_<?= $val ?>" name="metoda_plata" value="<?= $val ?>"
              <?= ($_POST['metoda_plata'] ?? 'OP') === $val ? 'checked' : '' ?>>
            <label for="plata_<?= $val ?>"><?= $label ?></label>
          <?php endforeach; ?>
        </p>
        <div class="form-row" style="align-items:flex-start">
          <label for="observatii">Observații:</label>
          <textarea id="observatii" name="observatii" rows="3"
                    style="width:60%"><?= htmlspecialchars($_POST['observatii'] ?? '') ?></textarea>
        </div>
      </fieldset>

      <fieldset>
        <legend>Document (salvat în SQLite)</legend>
        <div class="form-row">
          <label for="document_comanda">Atașează Document:</label>
          <input type="file" id="document_comanda" name="document_comanda" accept=".pdf,.doc,.docx">
        </div>
      </fieldset>

      <div class="form-actions">
          <input type="reset" value="Anulează" onclick="window.location.href='?page=dashboard'; return false;">

          <input type="submit" value="Trimite Comanda">
      </div>
    </form>
  </section>
</main>

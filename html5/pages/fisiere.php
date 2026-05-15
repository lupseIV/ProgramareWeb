<?php
if (!defined('APP_RUNNING')) { header('Location: /?page=home'); exit; }
if (!hasRole('ADMIN', 'MANAGER')) { header('Location: ?page=dashboard'); exit; }

$pageTitle = "Gestionare Fișiere";
$styles    = ["styles/adauga_contract.css", "styles/fisiere.css"];

if (isset($_GET['action']) && $_GET['action'] === 'vizualizare' && isset($_GET['file'])) {
    $numeFisier = $_GET['file'];
    $cale = __DIR__ . '/../uploads/' . $numeFisier;

    ob_end_clean();
    header('Content-Type: text/plain; charset=utf-8');
    echo file_get_contents($cale);
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'descarcare' && isset($_GET['id'])) {
    $fileId = (int) $_GET['id'];

    $stmt = $sqlite->prepare(
        "SELECT original_name, mime_type, continut FROM fisiere WHERE id = :id"
    );
    $stmt->execute([':id' => $fileId]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file && $file['continut'] !== null) {
        ob_end_clean();

        $mime = $file['mime_type'] ?: 'application/octet-stream';
        $name = $file['original_name'] ?: 'fisier';

        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . rawurlencode($name) . '"');
        header('Content-Length: ' . strlen($file['continut']));
        header('Cache-Control: private, no-cache');
        echo $file['continut'];
        exit;
    }
    $errors[] = 'Fișierul cu ID-ul ' . $fileId . ' nu a fost găsit în baza de date.';
}

$success = false;
$errors  = $errors ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sterge_id'])) {
    $fileId = (int) $_POST['sterge_id'];

    $mysqlUpdates = [
        "UPDATE contracte SET document_id    = NULL WHERE document_id    = :id",
        "UPDATE comenzi   SET document_id    = NULL WHERE document_id    = :id",
        "UPDATE facturi   SET document_id    = NULL WHERE document_id    = :id",
        "UPDATE angajati  SET poza_profil_id = NULL WHERE poza_profil_id = :id",
    ];
    foreach ($mysqlUpdates as $sql) {
        $pdo->prepare($sql)->execute([':id' => $fileId]);
    }

    if (stergeFisier($sqlite, $fileId)) {
        $success = true;
    } else {
        $errors[] = 'Fișierul nu a putut fi șters.';
    }
}

$stmtList = $sqlite->query(
    "SELECT id, original_name, mime_type, size, tip, data_upload
     FROM fisiere
     ORDER BY data_upload DESC"
);
$fisiere = $stmtList->fetchAll(PDO::FETCH_ASSOC);

function formatBytes(int $bytes): string {
    if ($bytes >= 1_048_576) return round($bytes / 1_048_576, 1) . ' MB';
    if ($bytes >= 1_024)     return round($bytes / 1_024, 1)     . ' KB';
    return $bytes . ' B';
}

// Map tip codes to Romanian labels
$tipLabels = [
    'contract' => 'Contract',
    'comanda'  => 'Comandă',
    'cv'       => 'CV',
    'gdpr'     => 'Acord GDPR',
];
?>

<main>
  <section id="fisiere-section">
    <h1>Gestionare Fișiere Încărcate</h1>

    <?php if ($success): ?>
      <div class="alert alert-success">✓ Fișierul a fost șters cu succes.</div>
    <?php endif; ?>
    <?php foreach ($errors as $err): ?>
      <div class="alert alert-error"><?= htmlspecialchars($err) ?></div>
    <?php endforeach; ?>

    <?php if (empty($fisiere)): ?>
      <p class="no-files">Nu există fișiere încărcate în sistem.</p>
    <?php else: ?>
      <table class="fisiere-table">
        <thead>
          <tr>
            <th>#ID</th>
            <th>Nume fișier</th>
            <th>Tip document</th>
            <th>Format</th>
            <th>Dimensiune</th>
            <th>Data încărcării</th>
            <th>Acțiuni</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($fisiere as $f): ?>
          <tr>
            <td><?= $f['id'] ?></td>
            <td><?= htmlspecialchars($f['original_name'] ?? '—') ?></td>
            <td><?= htmlspecialchars($tipLabels[$f['tip']] ?? $f['tip'] ?? '—') ?></td>
            <td><?= htmlspecialchars($f['mime_type'] ?? '—') ?></td>
            <td><?= formatBytes((int) $f['size']) ?></td>
            <td><?= htmlspecialchars($f['data_upload'] ?? '—') ?></td>
            <td class="actiuni">
              <!-- Download -->
              <a class="btn-download"
                 href="?page=fisiere&action=descarcare&id=<?= $f['id'] ?>">
                ⬇ Descarcă
              </a>

              <!-- Delete (plain POST, no JS) -->
              <form method="post" action="?page=fisiere"
                    onsubmit="return confirm('Ești sigur că vrei să ștergi fișierul «<?= htmlspecialchars(addslashes($f['original_name'] ?? '')) ?>»?');">
                <input type="hidden" name="sterge_id" value="<?= $f['id'] ?>">
                <button type="submit" class="btn-delete">✕ Șterge</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <div class="form-actions" style="margin-top:1.5rem;">
      <a href="?page=dashboard" class="btn-back">← Înapoi la Dashboard</a>
    </div>
  </section>
</main>

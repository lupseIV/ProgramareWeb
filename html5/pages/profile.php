<?php
if (!defined('APP_RUNNING')) { header('Location: index.php'); exit; }

$styles = ["styles/main.css", "styles/adauga_contract.css"];
$pageTitle = "Profilul Meu";

$success = false;
$errors = [];

// ── 1. PROCESARE FORMULAR (POST) ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nume        = trim($_POST['nume'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $departament = trim($_POST['departament'] ?? '');
    $descriere   = trim($_POST['descriere'] ?? '');

    if (!$nume) {
        $errors[] = "Numele nu poate fi lăsat gol.";
    }

    if (empty($errors)) {
        try {
            // Folosim PDO pentru a actualiza baza de date în siguranță
            $stmt = $pdo->prepare(
                    "UPDATE angajati 
                 SET nume = :nume, email = :email, departament = :dep, functie = :func 
                 WHERE utilizator_id = :uid"
            );
            $stmt->execute([
                    ':nume'  => $nume,
                    ':email' => $email,
                    ':dep'   => $departament,
                    ':func'  => $descriere,
                    ':uid'   => $_SESSION['user_id']
            ]);

            $success = true;
        } catch (PDOException $e) {
            $errors[] = "Eroare la actualizarea profilului: " . $e->getMessage();
        }
    }
}

// ── 2. EXTRAGERE DATE (Inclusiv cele proaspăt salvate) ────────────────────
$dateAngajat = getAngajatCurent($mysqli);

if (!$dateAngajat) {
    echo "Eroare: Nu s-au putut încărca datele profilului.";
    return;
}
?>

<main>
    <section id="profil-utilizator">
        <h1>Profilul Meu</h1>

        <?php if ($success): ?>
            <div class="alert alert-success" style="color: green; padding: 10px; margin-bottom: 15px; border: 1px solid green; background: #e8f5e9; border-radius: 5px;">
                ✓ Profilul a fost actualizat cu succes.
            </div>
        <?php endif; ?>
        <?php foreach ($errors as $e): ?>
            <div class="alert alert-error" style="color: red; padding: 10px; margin-bottom: 15px; border: 1px solid red; background: #ffebee; border-radius: 5px;">
                <?= htmlspecialchars($e) ?>
            </div>
        <?php endforeach; ?>

        <form action="?page=profile" method="post">
            <fieldset>
                <legend>Informații Editabile</legend>

                <div class="form-row">
                    <label for="nume">Nume:</label>
                    <input type="text" id="nume" name="nume"
                           value="<?= htmlspecialchars($dateAngajat['nume'] ?? '') ?>">
                </div>

                <div class="form-row">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($dateAngajat['email'] ?? '') ?>">
                </div>

                <div class="form-row">
                    <label for="departament">Departament:</label>
                    <select id="departament" name="departament">
                        <option value="">-- Selectează --</option>
                        <?php
                        $departamente = ['IT', 'HR', 'SALES', 'MANAGEMENT', 'FINANCIAR'];
                        foreach ($departamente as $dep):
                            $selected = ($dateAngajat['departament'] === $dep) ? 'selected' : '';
                            ?>
                            <option value="<?= $dep ?>" <?= $selected ?>><?= $dep ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <label for="descriere">Descriere:</label>
                    <textarea id="descriere" name="descriere" rows="4" style="width: 100%; margin-left: 1.5rem"><?= htmlspecialchars($dateAngajat['functie'] ?? '') ?></textarea>
                </div>
            </fieldset>

            <div class="form-actions">
                <button type="submit">Actualizează Profilul</button>
            </div>
        </form>
    </section>
</main>
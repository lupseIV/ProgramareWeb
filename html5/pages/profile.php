<?php
if (!defined('APP_RUNNING')) { header('Location: index.php'); exit; }

$styles=["/styles/main.css","/styles/adauga_contract.css"];
$pageTitle = "Profilul Meu";
$dateAngajat = getAngajatCurent($mysqli);

if (!$dateAngajat) {
    echo "Eroare: Nu s-au putut încărca datele profilului.";
    return;
}
?>

<main>
    <section id="profil-utilizator">
        <h1>Profilul Meu</h1>
        <form action="?page=salveaza_profil" method="post">

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
                        <?php
                        $departamente = ['IT', 'HR', 'SALES', 'MANAGEMENT'];
                        foreach ($departamente as $dep):
                            // Verificăm dacă valoarea din DB coincide cu opțiunea curentă
                            $selected = ($dateAngajat['departament'] === $dep) ? 'selected' : '';
                            ?>
                            <option value="<?= $dep ?>" <?= $selected ?>><?= $dep ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <label for="descriere">Descriere:</label>
                    <textarea id="descriere" name="descriere" rows="4" style="width: 100%; margin-left: 1.5rem">
            <?= htmlspecialchars($dateAngajat['functie'] ?? '') ?>
          </textarea>
            </fieldset>

            <div class="form-actions">
                <button type="submit">Actualizează Profilul</button>
            </div>
        </form>
    </section>
</main>
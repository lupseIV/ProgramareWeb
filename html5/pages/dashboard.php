<?php
if (!defined('APP_RUNNING')) { header('Location: /?page=dashboard'); exit; }
$pageTitle = "Dashboard";
$styles  = ["styles/dashboard.css"];
$scripts = ["scripts/data.js", "scripts/dashboard.js"];
?>

<style>
    /* Stiluri pentru Modal (Pop-up) */
    .modal-overlay {
        display: none;
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6); z-index: 1000;
        justify-content: center; align-items: center;
    }
    .modal-content {
        background: #fff; padding: 25px; border-radius: 8px;
        width: 400px; max-width: 90%; box-shadow: 0 10px 15px rgba(0,0,0,0.2);
        position: relative; animation: slideDown 0.3s ease-out;
    }
    @keyframes slideDown { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .modal-close {
        position: absolute; top: 15px; right: 20px;
        font-size: 28px; cursor: pointer; color: #94a3b8; line-height: 1;
    }
    .modal-close:hover { color: #0f172a; }
    .user-details p { margin: 12px 0; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; font-size: 0.95rem; }
    .user-details strong { display: inline-block; width: 130px; color: #475569;}
    .user-row { cursor: pointer; transition: background-color 0.2s; }
    .user-row:hover { background-color: #f8fafc; }
</style>

<svg style="display: none;">
    <symbol id="add-doc-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 14h-3v3h-2v-3H8v-2h3v-3h2v3h3v2zm-3-7V3.5L18.5 9H13z"></path></symbol>
    <symbol id="add-angajat-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></symbol>
    <symbol id="add-comanda-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M11 9h2V6h3V4h-3V1h-2v3H8v2h3v3zm-4 9c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2zm-9.83-3.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.86-7.01L19.42 4h-.01l-1.1 2-2.76 5H8.53l-.13-.27L6.16 6l-.95-2-.94-2H1v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.13 0-.25-.11-.25-.25z"></path></symbol>
</svg>

<main>
    <section>
        <h2 >Acțiuni Rapide</h2>
        <div class="actions-grid">
            <?php if (isAdmin() or isManager()) : ?>
                <a href="?page=adauga_contract" class="action-card">
                    <svg class="svg-icon-animated"><use href="#add-doc-icon"></use></svg>
                    <div class="action-title">Contract Nou</div>
                </a>

                <a href="?page=adauga_angajat" class="action-card">
                    <svg class="svg-icon-animated"><use href="#add-angajat-icon"></use></svg>
                    <div class="action-title">Angajat Nou</div>
                </a>
            <?php endif; ?>
            <a href="?page=adauga_comanda" class="action-card">
                <svg class="svg-icon-animated"><use href="#add-comanda-icon"></use></svg>
                <div class="action-title">Comandă Nouă</div>
            </a>
        </div>
    </section>

    <?php if (isManager()) : ?>
        <section>
            <h2>Analiză Vânzări: Top Clienți</h2>
            <div class="dashboard-grid-2col">
                <div class="table-wrapper">
                    <table class="data-table" id="tabel-top-clienti">
                        <thead>
                        <tr>
                            <th class="sort-header" data-col="nume">Nume Client</th>
                            <th class="sort-header" data-col="comenzi">Nr. Comenzi</th>
                            <th class="sort-header" data-col="valoare">Valoare (EUR)</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div>
                    <div id="bar-chart-clienti" class="chart-container"></div>
                </div>
            </div>
        </section>

        <section>
            <h2>Gestiune HR: Evidență Ore Suplimentare</h2>
            <div class="table-wrapper">
                <table class="data-table vertical-table" id="tabel-ore-vertical">
                    <tbody></tbody>
                </table>
            </div>
        </section>
    <?php endif; ?>

    <?php if (isAdmin()) : ?>
        <section>
            <h2>Utilizatori Conectați <span style="font-size: 0.9rem; font-weight: normal; color: #64748b;">(Click pe rând pentru detalii)</span></h2>
            <?php
            $stmt = $pdo->prepare("
                SELECT u.username, u.role, u.logged_at, 
                       a.nume, a.prenume, a.email, a.departament, a.functie
                FROM utilizatori u
                LEFT JOIN angajati a ON u.id = a.utilizator_id
                WHERE u.logged_in = 1 AND u.username <> :currentUser
                ORDER BY u.logged_at DESC
            ");

            $stmt->execute([':currentUser' => $_SESSION['username']]);

            $utilizatoriLogati = $stmt->fetchAll();
            ?>
            <table class="data-table">
                <thead>
                <tr>
                    <th>Utilizator</th>
                    <th>Rol</th>
                    <th>Conectat de la</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($utilizatoriLogati)): ?>
                    <tr><td colspan="3">Niciun utilizator conectat momentan.</td></tr>
                <?php else: ?>
                    <?php foreach ($utilizatoriLogati as $u): ?>
                        <tr class="user-row"
                            data-username="<?= htmlspecialchars($u['username']) ?>"
                            data-nume="<?= ($u['nume'] ?? '') . ' ' . ($u['prenume'] ?? '') ?>"
                            data-role="<?= htmlspecialchars($u['role']) ?>"
                            data-email="<?= htmlspecialchars($u['email'] ?? 'Nespecificat') ?>"
                            data-dep="<?= htmlspecialchars($u['departament'] ?? 'Nespecificat') ?>"
                            data-functie="<?= htmlspecialchars($u['functie'] ?? 'Nespecificat') ?>"
                            data-logged="<?= htmlspecialchars($u['logged_at']) ?>">

                            <td style="color: #2563eb; font-weight: 500;"><?= htmlspecialchars($u['username']) ?></td>
                            <td><?= htmlspecialchars($u['role']) ?></td>
                            <td><?= htmlspecialchars($u['logged_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </section>
    <?php endif; ?>

</main>

<div id="userModal" class="modal-overlay">
    <div class="modal-content">
        <span class="modal-close" title="Închide">&times;</span>
        <h3 style="margin-top:0; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; color: #1e293b;">Detalii Angajat</h3>
        <div class="user-details">
            <p><strong>Username:</strong> <span id="m-username"></span></p>
            <p><strong>Nume Complet:</strong> <span id="m-nume"></span></p>
            <p><strong>Rol:</strong> <span id="m-role"></span></p>
            <p><strong>Email:</strong> <span id="m-email"></span></p>
            <p><strong>Departament:</strong> <span id="m-dep"></span></p>
            <p><strong>Funcție:</strong> <span id="m-functie"></span></p>
            <p><strong>Sesiune activă din:</strong> <span id="m-logged"></span></p>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // La click pe un rând din tabel
        $('.user-row').on('click', function() {
            var $row = $(this);

            // Populăm modalul cu datele ascunse în atributele rândului
            $('#m-username').text($row.data('username'));

            var numeIntreg = $row.data('nume').trim();
            $('#m-nume').append(numeIntreg !== '' ? numeIntreg : 'Profil incomplet');

            $('#m-role').text($row.data('role'));
            $('#m-email').text($row.data('email'));
            $('#m-dep').text($row.data('dep'));
            $('#m-functie').text($row.data('functie'));
            $('#m-logged').text($row.data('logged'));

            // Afișăm modalul cu un efect de Fade In
            $('#userModal').css('display', 'flex').hide().fadeIn(250);
        });

        $('.modal-close').on('click', function() {
            $('#userModal').fadeOut(250);
        });

        $('#userModal').on('click', function(e) {
            if (e.target === this) {
                $(this).fadeOut(250);
            }
        });
    });
</script>
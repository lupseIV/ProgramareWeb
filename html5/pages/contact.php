<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <title>ERP - Contact</title>
    <link rel="stylesheet" href="../styles/main.css">
    <link rel="stylesheet" href="../styles/responsive.css">
    <link rel="stylesheet" href="../styles/adauga_contract.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

</head>
<body>

<main>
    <section id="contact-info">
        <h2>Informații de Contact</h2>

        <ul class="card-list">
            <li>
                <b>Asistență Tehnică</b>
                Suport dedicat 24/7. <br>
                <a href="mailto:support@erp-core.ro" style="color: var(--accent);">support@erp-core.ro</a>
            </li>
            <li>
                <b>Departament Vânzări</b>
                Consultanță și oferte. <br>
                <a href="mailto:sales@erp-core.ro" style="color: var(--accent);">sales@erp-core.ro</a>
            </li>
            <li>
                <b>Sediu Central</b>
                Str. Inovației Nr. 10, București.<br>
                <span style="color: var(--text-muted); font-size: 0.9rem;">Luni - Vineri: 09:00 - 18:00</span>
            </li>
        </ul>
    </section>

    <section id="contact-form-section">
        <h2>Trimite-ne un mesaj</h2>

        <form action="#" method="post">
            <fieldset>
                <legend>Formular de Contact</legend>

                <div class="form-row">
                    <label for="nume">Nume Complet:</label>
                    <input type="text" id="nume" name="nume" required placeholder="Ex: Popescu Ion">
                </div>

                <div class="form-row">
                    <label for="email">Adresă Email:</label>
                    <input type="email" id="email" name="email" required placeholder="nume@companie.ro">
                </div>

                <div class="form-row">
                    <label for="subiect">Subiect Mesaj:</label>
                    <select id="subiect" name="subiect" style="width: 60%; height: 2.5rem; border-radius: 10px; border: 1px solid var(--border); padding: 5px;">
                        <option value="suport">Suport Tehnic (Helpdesk)</option>
                        <option value="vanzari">Ofertă Comercială (Vânzări)</option>
                        <option value="parteneriat">Parteneriat Strategic</option>
                        <option value="altele">Altele</option>
                    </select>
                </div>

                <div class="form-row" style="height: auto; align-items: flex-start; margin-top: 10px;">
                    <label for="mesaj">Mesajul tău:</label>
                    <textarea id="mesaj" name="mesaj" rows="5" style="width: 60%; border-radius: 10px; border: 1px solid var(--border); padding: 10px; font-family: inherit; resize: vertical;" required placeholder="Scrie aici detaliile..."></textarea>
                </div>

            </fieldset>

            <div class="form-actions">
                <input type="reset" value="Resetează Formular" title="Șterge câmpurile">
                <input type="submit" value="Trimite Mesajul" style="background-color: var(--accent); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold;">
            </div>
        </form>
    </section>

</main>


</body>
</html>
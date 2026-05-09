<?php
if (!defined('APP_RUNNING')) { header('Location: /?page=home'); exit; }
$pageTitle = "ERP - Adauga Angajat";
$styles  = ["styles/adauga_contract.css"];
$scripts = ["scripts/adauga_angajat.js"];
?>
<main>
  <section id="add-employee">
    <h1>Înregistrare Angajat Nou</h1>
    <form id="form-angajat" action="#" method="post">
      <fieldset>
        <legend>Date Personale</legend>
        <div class="form-row">
          <label for="nume_angajat">Nume Complet:</label>
          <input type="text" id="nume_angajat" name="nume_angajat" placeholder="Ex: Popescu Maria">
        </div>
        <div class="form-row">
          <label for="cnp">CNP:</label>
          <input type="text" id="cnp" name="cnp" placeholder="13 cifre" maxlength="13">
        </div>

        <div class="form-row">
          <label for="gen">Gen (auto din CNP):</label>
          <input type="text" id="gen" name="gen" readonly style="cursor: not-allowed;" placeholder="...">
        </div>
        <div class="form-row">
          <label for="data_nasterii_cnp">Data Nașterii (auto):</label>
          <input type="date" id="data_nasterii_cnp" name="data_nasterii_cnp" readonly style="cursor: not-allowed;">
        </div>
        <div class="form-row">
          <label for="varsta">Vârsta (auto):</label>
          <input type="number" id="varsta" name="varsta" readonly style="cursor: not-allowed;" placeholder="...">
        </div>

        <div class="form-row">
          <label for="email_angajat">Email Personal:</label>
          <input type="email" id="email_angajat" name="email_angajat" placeholder="nume@exemplu.ro">
        </div>
      </fieldset>

      <fieldset>
        <legend>Detalii Profesionale</legend>
        <div class="form-row">
          <label for="departament_ang">Departament:</label>
          <select id="departament_ang" name="departament_ang">
            <option value="">-- Selectează Departament --</option>
            <option value="it">IT & Software</option>
            <option value="hr">Resurse Umane</option>
            <option value="sales">Vânzări</option>
            <option value="management">Management</option>
          </select>
        </div>
        <div class="form-row">
          <label for="salariu">Salariu de bază (RON):</label>
          <input type="number" id="salariu" name="salariu" step="100">
        </div>
        <div class="form-row">
          <label for="data_angajare">Data Angajării:</label>
          <input type="date" id="data_angajare" name="data_angajare">
        </div>
      </fieldset>

      <fieldset>
        <legend>Atașamente</legend>
        <p>
          <label for="cv_atasat">Atașează CV (PDF):</label>
          <input type="file" id="cv_atasat" name="cv_atasat">
        </p>
        <p>
          <label for="acord_gdpr">Atașează GDPR (PDF):</label>
          <input type="file" id="acord_gdpr" name="acord_gdpr">
        </p>
      </fieldset>

      <div class="form-actions">
        <input type="reset" value="Anulează" onclick="window.location.href='?page=dashboard'; return false;" title="Întoarce-te la Dashboard">
        <input type="submit" value="Salvează Angajatul">
      </div>
    </form>
  </section>
</main>

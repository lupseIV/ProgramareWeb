<?php
if (!defined('APP_RUNNING')) { header('Location: /?page=home'); exit; }
$pageTitle = "ERP - Adauga Angajat";
$styles  = ["styles/adauga_contract.css"];
$scripts = ["scripts/data.js", "scripts/adauga_contract.js"];
?>
<main>
  <section id="add-contract"><h1>Adauga un contract</h1>
    <form id="add-contract-form" action="#" method="post">
      <fieldset>
        <legend>Angajat care adauga contract</legend>
        <div class="form-row">
          <label for="nume_angajat">Nume Angajat:</label>
          <input id="nume_angajat" maxlength="40" name="nume_angajat" readonly size="30"
                 title="Numele este extras automat din profil" type="text" value="Popescu Ion">
        </div>
        <div class="form-row"><label for="id_operator">ID Operator:</label>
          <input id="id_operator" name="id_operator" readonly size="10"
                 title="Id este extras automat din profil"
                 type="text" value="OP-9921">
        </div>
        <div class="form-row"><label for="parola_validare">Parolă Validare:</label>
          <input id="parola_validare" maxlength="20" name="parola_validare" size="15"
                 type="password">
        </div>
      </fieldset>
      <fieldset>
        <legend>Date Contractuale Client</legend>

        <div class="form-row">
          <label for="nume_client">Denumire Client Partener:</label>
          <input id="nume_client" maxlength="100" name="nume_client" size="40"
                 title="Introduceți numele oficial al entității"
                 type="text">
        </div>
        <div class="form-row">
          <label for="cui_client">Cod Identificare Fiscală (CUI):</label>
          <input id="cui_client" maxlength="12" name="cui_client" size="20" type="text">
        </div>
        <div class="form-row">
          <label for="adresa_sediu">Adresă Sediu Social:</label>
          <input id="adresa_sediu" maxlength="200" name="adresa_sediu" size="60" type="text">
        </div>
      </fieldset>
      <fieldset>
        <legend>Detalii Financiare și Gestiune</legend>
        <div class="form-row">
          <label for="nr_intern">Nr. Contract:</label>
          <input id="nr_intern" name="nr_intern" readonly size="15" type="text"
                 value="CTR-2024-001">
        </div>
        <div class="form-row">
          <label for="valoare">Valoare:</label>
          <input id="valoare" max="1000000" name="valoare" step="100" type="number" value="5000">
        </div>
        <div class="form-row">
          <label for="moneda">Monedă:</label>
          <select id="moneda" name="moneda">
            <option selected value="eur">Euro (EUR)</option>
            <option value="ron">Lei (RON)</option>
            <option value="usd">Dolari (USD)</option>
          </select>
        </div>
        <div class="form-row">
          <label for="durata">Durată (Zile):</label>
          <input id="durata" max="365" name="durata" step="5" type="number" value="30">
        </div>
        <div class="form-row">
          <label for="metoda_plata">Metodă Plată:</label>
          <select id="metoda_plata" name="metoda_plata">
            <option value="OP">Ordin Plată</option>
            <option value="CARD">Card Bancar</option>
            <option value="TREZ">Trezorerie</option>
          </select>
        </div>
      </fieldset>
      <fieldset>
        <legend>Tip contract</legend>
        <p>
          <label for="servicii">Categorii Servicii Selectate:</label> <br>
          <select id="servicii" multiple name="servicii" size="4">
            <option selected value="audit">Audit Gestiune</option>
            <option value="consultanta">Consultanță Resurse</option>
            <option value="it_support">Suport IT Dedicat</option>
            <option disabled value="leasing">Externalizare Logistică (Indisponibil)</option>
          </select>
        </p>
        <p>
          Nivel de Prioritate:
          <input id="prio_std" name="prioritate" type="radio" value="std">
          <label for="prio_std">Standard</label>
          <input checked id="prio_prm" name="prioritate" type="radio" value="prm">
          <label for="prio_prm">Premium (Urgență)</label>
        </p>
      </fieldset>
      <fieldset>
        <legend>5. Fișiere și Observații</legend>
        <p>
          <input checked id="notificare" name="notificare" type="checkbox">
          <label for="notificare">Notifică departament financiar</label>

          <input id="arhiva" name="arhiva" type="checkbox">
          <label for="arhiva">Arhivare fizică necesară</label>
        </p>
        <p>
          <label for="fisier_atasat">Atașează Copie Contract (PDF):</label>
          <input id="fisier_atasat" name="fisier_atasat" type="file">
        </p>
        <p>
          <label for="observatii">Observații Specifice:</label><br>
          <textarea cols="80" id="observatii" name="observatii"
                    rows="4">Introduceți mențiuni...</textarea>
        </p>

      </fieldset>
      <div class="form-actions">
        <input type="reset" value="Anulează" onclick="window.location.href='?page=dashboard'; return false;" title="Întoarce-te la Dashboard">
        <input type="submit" value="Înregistrează Contractul">
      </div>
    </form>
  </section>
</main>


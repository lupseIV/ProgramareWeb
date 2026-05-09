<?php
if (!defined('APP_RUNNING')) { header('Location: /?page=home'); exit; }
$pageTitle = "ERP - Adauga Angajat";
$styles  = ["styles/adauga_contract.css"];
$scripts = ["scripts/data.js", "scripts/adauga_comanda.js"];
?>
<main>
  <section id="add-order">
    <h1>Plasare Comandă Nouă</h1>
    <form id="form-comanda" action="#" method="post">
      <fieldset>
        <legend>Detalii Client</legend>
        <div class="form-row">
          <label for="tara_client">Țara Clientului:</label>
          <select id="tara_client" name="tara_client">
            <option value="">-- Alege Țara --</option>
            <option value="Romania">România</option>
            <option value="Germania">Germania</option>
            <option value="Italia">Italia</option>
          </select>
        </div>
        <div class="form-row">
          <label for="client_comanda">Selectați Clientul:</label>
          <select id="client_comanda" name="client_comanda">
            <option value="">-- Alegeți întâi țara --</option>
          </select>
        </div>

        <div class="form-row" style="align-items: flex-start;">
          <label for="adresa_livrare_select" style="margin-top: 10px;">Adresa de Livrare:</label>
          <div style="width: 60%; display: flex; flex-direction: column; gap: 10px;">

            <select id="adresa_livrare_select" name="adresa_livrare_select" style="width: 100%;">
              <option value="">-- Alege o adresă precedentă --</option>
              <option value="noua" style="font-weight: bold; color: #3b82f6;">+ Introdu o adresă nouă...</option>
            </select>

            <input type="text" id="adresa_livrare_noua" name="adresa_livrare_noua"
                   placeholder="Ex: Strada, Nr, Oraș, Cod Poștal"
                   style="display: none; width: 100%;">
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>Specificații Comandă</legend>
        <div class="form-row">
          <label for="produs_serviciu">Produs / Serviciu:</label>
          <input type="text" id="produs_serviciu" name="produs_serviciu" placeholder="Ex: Licență ERP Core">
        </div>
        <div class="form-row">
          <label for="cantitate">Cantitate:</label>
          <input type="number" id="cantitate" name="cantitate" min="1" value="1">
        </div>
        <div class="form-row">
          <label for="data_livrare">Data Livrării Dorite:</label>
          <input type="date" id="data_livrare" name="data_livrare">
        </div>
      </fieldset>

      <fieldset>
        <legend>Metodă de Plată</legend>
        <p>
          <input type="radio" id="plata_card" name="metoda_plata" value="card">
          <label for="plata_card">Card Bancar (Online)</label>

          <input type="radio" id="plata_op" name="metoda_plata" value="op">
          <label for="plata_op">Ordin de Plată (Transfer)</label>

          <input type="radio" id="plata_ramburs" name="metoda_plata" value="ramburs">
          <label for="plata_ramburs">Ramburs la livrare</label>
        </p>
        <p>
          <label for="obs_comanda">Observații privind livrarea:</label><br>
          <textarea id="obs_comanda" name="obs_comanda" rows="3" style="width: 80%;"></textarea>
        </p>
      </fieldset>

      <div class="form-actions">
        <input type="reset" value="Anulează" onclick="window.location.href='?page=dashboard'; return false;" title="Întoarce-te la Dashboard">
        <input type="submit" value="Trimite Comanda">
      </div>
    </form>
  </section>
</main>

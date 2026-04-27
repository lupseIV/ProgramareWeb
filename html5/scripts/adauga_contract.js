$(document).ready( function() {
    const $addContractForm = $("#add-contract-form");

    $('<div>', {
        id: 'mesaj-alerta',
        style: 'display: none; color: red; margin-bottom: 15px;'
    }).insertAfter('#add-contract h1');

    if ($addContractForm.length) {
        $addContractForm.submit(function(event) {
            let esteValid = true;
            let mesajeEroare = [];

            $addContractForm.find('.eroare-validare').removeClass('eroare-validare')

            function marcheazaEroareFromId(jqueryElem) {
                if (jqueryElem.length) {
                    jqueryElem.addClass("eroare-validare");
                    esteValid = false;
                }
            }


            const $parola = $('#parola_validare');
            if (!$parola.val() || $parola.val().length < 6) {
                marcheazaEroareFromId($parola);
                mesajeEroare.push("- Parola trebuie să conțină minim 6 caractere.");
            }

            const $numeClient = $("#nume_client");
            if (!$numeClient.val() || $numeClient.val().trim() === "") {
                marcheazaEroareFromId($numeClient);
                mesajeEroare.push("- Introduceți denumirea clientului.");
            }

            const $cui = $("#cui_client");
            if (!$cui.val() || $cui.val().length < 6 || $cui.val().length > 10) {
                marcheazaEroareFromId($cui);
                mesajeEroare.push("- CUI-ul trebuie să aibă între 6 și 10 caractere.");
            }

            const $valoare = $("#valoare");
            if (!$valoare.val() || Number($valoare.val()) <= 0) {
                marcheazaEroareFromId($valoare);
                mesajeEroare.push("- Valoarea contractului trebuie să fie mai mare ca 0.");
            }

            const $durata = $("#durata");
            if (!$durata.val() || Number($durata.val()) <= 0 || Number($durata.val()) > 365) {
                marcheazaEroareFromId($durata);
                mesajeEroare.push("- Durata trebuie să fie cuprinsă între 1 și 365 zile.");
            }

            const $servicii = $("#servicii");
            if ($servicii.val() && $servicii.val().length === 0) {
                marcheazaEroareFromId($servicii);
                mesajeEroare.push("- Trebuie să selectați cel puțin un serviciu.");
            }

            const $fisier = $("#fisier_atasat");
            if (!$fisier.val()) {
                marcheazaEroareFromId($fisier);
                mesajeEroare.push("- Atașați copia PDF a contractului.");
            } else if (!$fisier.val().toLowerCase().endsWith(".pdf")) {
                marcheazaEroareFromId($fisier);
                mesajeEroare.push("- Fișierul atașat trebuie să aibă extensia .pdf");
            }

            const $alerta = $('#mesaj-alerta');
            if (!esteValid) {
                event.preventDefault();
                $alerta
                    .css('color', 'red')
                    .html(
                    "<strong>Atenție! Formularul conține erori:</strong><br>"
                    + mesajeEroare.join("<br>"))
                    .slideDown(300);
            } else {
                event.preventDefault();

                $addContractForm
                    .find('input, select, textarea')
                    .not('[readonly]')
                    .not('[type="submit"], [type="reset"], [type="button"]')
                    .val('');

                $alerta.removeClass('eroare-text').css('color', 'green')
                    .text("Toate datele sunt corecte! Contractul ar fi fost salvat cu succes.")
                    .slideDown(300);
            }
            $('html, body').animate({
                scrollTop: $('#add-contract').offset().top - 50
            }, 600)
        });

        $addContractForm.on('input change', '.eroare-validare', function() {
            $(this).removeClass('eroare-validare');
        });
    }
});
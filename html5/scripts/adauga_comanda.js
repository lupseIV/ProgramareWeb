$(document).ready(function() {
    const $formComanda = $("#form-comanda");

    const $taraSelect = $("#tara_client");
    const $clientSelect = $("#client_comanda");
    const $adresaSelect = $("#adresa_livrare_select");
    const $adresaNouaInput = $("#adresa_livrare_noua");

    function curataAdrese() {
        if(!$adresaSelect.length) return;
        $adresaSelect.find('option').not(':first, :last').remove();
        $adresaSelect.val("");
        $adresaNouaInput.css("display","none");
        $adresaNouaInput.val("");
    }

    if ($adresaSelect.length > 0) {
        $adresaSelect.change(function() {
            if ($(this).val() === "noua") {
                $adresaNouaInput.css("display", "block");
                $adresaNouaInput.focus();
            } else {
                $adresaNouaInput.css("display","none");
                $adresaNouaInput.val("");
                $adresaNouaInput.removeClass("eroare-validare");
            }
        });
    }

    if ($taraSelect.length > 0 && $clientSelect.length > 0) {
        $taraSelect.change(function() {
            const taraAleasa = $(this).val();
            $clientSelect.html ('<option value="">-- Alege Client --</option>');
            curataAdrese();

            if (taraAleasa && typeof dateComanda !== 'undefined' && dateComanda.tari_clienti[taraAleasa]) {
                const clienti = dateComanda.tari_clienti[taraAleasa];
                clienti.forEach(client => {
                    $clientSelect.append($('<option>', {
                        value: client.id,
                        text: client.nume
                    }));
                });
            } else if (!taraAleasa) {
                $clientSelect.html('<option value="">-- Alegeți întâi țara --</option>');
            }
        });
    }

    if ($clientSelect.length > 0 && $adresaSelect.length > 0) {
        $clientSelect.change(function() {
            const clientAles = $(this).val();
            curataAdrese();

            if (clientAles && typeof dateComanda !== 'undefined') {
                let adrese = [];

                if (dateComanda.adrese_clienti && dateComanda.adrese_clienti[clientAles]) {
                    adrese = dateComanda.adrese_clienti[clientAles];
                }
                else if (dateComanda.adrese_precedente) {
                    adrese = dateComanda.adrese_precedente;
                }

                adrese.forEach(adresa => {
                    $('option[value="noua"]').before($('<option>',{
                        value: adresa,
                        text: adresa
                    }))
                })
            }
        });
    }
    $('<div>', {
        id: 'mesaj-alerta',
        style: 'display: none; color: red; margin-bottom: 15px;'
    }).insertAfter('#add-order h1');

    if ($formComanda.length) {
        $formComanda.submit(function(event) {
            let esteValid = true;
            let mesajeEroare = [];

            $formComanda.find(".eroare-validare").removeClass(".eroare-validare");

            function marcheazaEroare(jqueryElem) {
                if (jqueryElem.length) {
                    jqueryElem.addClass("eroare-validare");
                    esteValid = false;
                }
            }

            if (!$taraSelect.val()) {
                marcheazaEroare($taraSelect);
                mesajeEroare.push("- Vă rugăm să selectați țara.");
            }

            if (!$clientSelect.val()) {
                marcheazaEroare($clientSelect);
                mesajeEroare.push("- Vă rugăm să selectați clientul.");
            }

            if (!$adresaSelect.val()) {
                marcheazaEroare($adresaSelect);
                mesajeEroare.push("- Selectați o adresă de livrare.");
            } else if ($adresaSelect.val() === "noua") {
                if (!$adresaNouaInput.val() || $adresaNouaInput.val().trim().length < 5) {
                    marcheazaEroare($adresaNouaInput);
                    mesajeEroare.push("- Introduceți o adresă validă (minim 5 caractere).");
                }
            }

            const $produs = $("#produs_serviciu");
            if (!$produs.val() || $produs.val().trim() === "") {
                marcheazaEroare($produs);
                mesajeEroare.push("- Specificați produsul sau serviciul comandat.");
            }

            const $cantitate = $("#cantitate");
            if (!$cantitate.val() || Number($cantitate.val()) < 1) {
                marcheazaEroare($cantitate);
                mesajeEroare.push("- Cantitatea trebuie să fie cel puțin 1.");
            }

            const $dataLivrare = $("#data_livrare");
            if (!$dataLivrare.val()) {
                marcheazaEroare($dataLivrare);
                mesajeEroare.push("- Selectați data livrării dorite.");
            } else {
                const dataSelectata = new Date($dataLivrare.val());
                const dataAzi = new Date();
                dataAzi.setHours(0,0,0,0);

                if (dataSelectata < dataAzi) {
                    marcheazaEroare($dataLivrare);
                    mesajeEroare.push("- Data livrării nu poate fi în trecut.");
                }
            }

            const $optiuniPlata = $('input[name="metoda_plata"]');
            if(!$optiuniPlata.is(':checked')){
                mesajeEroare.push("- Vă rugăm să alegeți o metodă de plată.");
                esteValid = false;
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

                $formComanda
                    .find('input, select, textarea')
                    .not('[readonly]')
                    .not('[type="submit"], [type="reset"], [type="button"]')
                    .val('');

                $alerta.removeClass('eroare-text').css('color', 'green')
                    .text("Toate datele sunt corecte! Comanda a fost salvata cu succes.")
                    .slideDown(300);
            }
            $('html, body').animate({
                scrollTop: $('#add-order').offset().top - 50
            }, 600)
        });
        $formComanda.on('input change', '.eroare-validare', function() {
            $(this).removeClass('eroare-validare');
        });
    }

});
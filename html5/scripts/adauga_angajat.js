$(document).ready(function() {
    const $formAngajat = $("#form-angajat");

    const $cnpInput = $("#cnp");
    const $genInput = $("#gen");
    const $dataNasteriiInput = $("#data_nasterii_cnp");
    const $varstaInput = $("#varsta");

    $('<div>', {
        id: 'mesaj-alerta',
        style: 'display: none; color: red; margin-bottom: 15px;'
    }).insertAfter('#add-employee h1');

    $formAngajat.on('input change', '.eroare-validare', function() {
        $(this).removeClass('eroare-validare');
    })

    if ($cnpInput.length) {
        $cnpInput.change(function() {
            const cnp = $(this).val();

            if (/^[0-9]{13}$/.test(cnp)) {
                const s = parseInt(cnp.charAt(0));
                const aa = parseInt(cnp.substring(1, 3));
                const ll = parseInt(cnp.substring(3, 5));
                const zz = parseInt(cnp.substring(5, 7));

                let an = 0;

                if (s === 1 || s === 2) an = 1900 + aa;
                else if (s === 3 || s === 4) an = 1800 + aa;
                else if (s === 5 || s === 6) an = 2000 + aa;
                else if (s === 7 || s === 8 || s === 9) {
                    an = (aa > 25) ? 1900 + aa : 2000 + aa;
                }

                if (an > 0 && ll >= 1 && ll <= 12 && zz >= 1 && zz <= 31) {

                    $genInput.val( (s % 2 !== 0) ? "Masculin" : "Feminin");

                    const lunaStr = ll < 10 ? '0' + ll : ll;
                    const ziuaStr = zz < 10 ? '0' + zz : zz;
                    $dataNasteriiInput.val( `${an}-${lunaStr}-${ziuaStr}`);

                    const azi = new Date();
                    const dataN = new Date(an, ll - 1, zz);
                    let varsta = azi.getFullYear() - dataN.getFullYear();

                    const diferentaLuni = azi.getMonth() - dataN.getMonth();
                    if (diferentaLuni < 0 || (diferentaLuni === 0 && azi.getDate() < dataN.getDate())) {
                        varsta--;
                    }
                    $varstaInput.val( varsta);
                } else {
                    reseteazaExtragereCNP();
                }
            } else {
                reseteazaExtragereCNP();
            }
        });
    }

    function reseteazaExtragereCNP() {
        $genInput.val('');
        $dataNasteriiInput.val('');
        $varstaInput.val('');
    }

    if ($formAngajat.length) {
        $formAngajat.submit(function(event) {
            let esteValid = true;
            let mesajeEroare = [];

            $formAngajat.find('.eroare-validare').removeClass('eroare-validare');

            function marcheazaEroare(jqueryElem) {
                if (jqueryElem.length) {
                    jqueryElem.addClass("eroare-validare");
                    esteValid = false;
                }
            }

            const $nume = $("#nume_angajat");
            if (!$nume.val() || $nume.val().trim().length < 3) {
                marcheazaEroare($nume);
                mesajeEroare.push("- Numele trebuie să conțină minim 3 caractere.");
            }

            const $cnp = $("#cnp");
            const cnpRegex = /^[0-9]{13}$/;
            if (!$cnp.val() || !cnpRegex.test($cnp.val())) {
                marcheazaEroare($cnp);
                mesajeEroare.push("- CNP-ul este invalid. Trebuie să conțină exact 13 cifre.");
            }

            if ($varstaInput && $varstaInput.val()) {
                if (parseInt($varstaInput.val()) < 18) {
                    marcheazaEroare($cnp);
                    marcheazaEroare($varstaInput);
                    mesajeEroare.push("- Persoana este minoră (" + $varstaInput.val() + " ani). Angajarea necesită condiții speciale / este interzisă.");
                }
            }

            const $emailAngajat = $("#email_angajat");
            if (!$emailAngajat.val() || $emailAngajat.val().indexOf('@') === -1) {
                marcheazaEroare($emailAngajat);
                mesajeEroare.push("- Introduceți o adresă de email validă.");
            }

            const $departamentAng = $("#departament_ang");
            if (!$departamentAng.val()) {
                marcheazaEroare($departamentAng);
                mesajeEroare.push("- Selectați un departament.");
            }

            const $dataAngajare = $("#data_angajare")
            if (!$dataAngajare.val()){
                marcheazaEroare($dataAngajare)
                mesajeEroare.push("- Selectați data de angajare.")
            } else {
                const dataSelectata = new Date($dataAngajare.val());
                const dataAzi = new Date();
                dataAzi.setHours(0,0,0,0);

                if (dataSelectata < dataAzi) {
                    marcheazaEroare($dataAngajare);
                    mesajeEroare.push("- Data angajării nu poate fi în trecut.");
                }
            }

            const $salariu = $("#salariu");
            if (!$salariu.val() || Number($salariu.val()) < 2000) {
                marcheazaEroare($salariu);
                mesajeEroare.push("- Salariul de bază trebuie să fie minim 2000 RON.");
            }

            const $acordGdpr = $("#acord_gdpr");
            if (!$acordGdpr.val()) {
                marcheazaEroare($acordGdpr);
                mesajeEroare.push("- Este necesar acordul pentru prelucrarea datelor (GDPR).");
            } else if (!$acordGdpr.val().toLowerCase().endsWith(".pdf")){
                marcheazaEroare($acordGdpr)
                mesajeEroare.push("- Acordul GDPR trebuie să fie in format .pdf.")
            }

            const $cvAtasat = $("#cv_atasat")
            if($cvAtasat.val() && !$cvAtasat.val().toLowerCase().endsWith(".pdf")){
                marcheazaEroare($cvAtasat)
                mesajeEroare.push("- CV-ul trebuie să fie in format .pdf.")
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

                $formAngajat
                    .find('input, select, textarea')
                    .not('[readonly]')
                    .not('[type="submit"], [type="reset"], [type="button"]')
                    .val('');
                reseteazaExtragereCNP();
                $alerta.removeClass('eroare-text').css('color', 'green')
                    .text("Toate datele sunt corecte! Angajat salvat cu succes.")
                    .slideDown(300);
            }
            $('html, body').animate({
                scrollTop: $('#add-employee').offset().top - 50
            }, 600)
        });
    }
});
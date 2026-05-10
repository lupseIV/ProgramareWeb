$(document).ready(function () {

    const $form   = $('#form-comanda');
    const $alerta = $('<div id="mesaj-alerta" style="display:none;margin-bottom:15px;padding:.75rem 1rem;border-radius:6px;"></div>')
                        .insertAfter('#add-order h1');

    $form.on('input change', '.eroare-validare', function () {
        $(this).removeClass('eroare-validare');
    });

    $form.on('submit', function (e) {
        let ok = true;
        const erori = [];

        $form.find('.eroare-validare').removeClass('eroare-validare');

        function err($el, msg) {
            $el.addClass('eroare-validare');
            erori.push('- ' + msg);
            ok = false;
        }

        // Client
        const $client = $('#client');
        if ($client.val().trim() === '')
            err($client, 'Denumirea clientului este obligatorie.');

        // Produs / Serviciu
        const $produs = $('#produs_serviciu');
        if ($produs.val().trim() === '')
            err($produs, 'Specificați produsul sau serviciul comandat.');

        // Cantitate ≥ 1
        const $cant = $('#cantitate');
        if (Number($cant.val()) < 1)
            err($cant, 'Cantitatea trebuie să fie cel puțin 1.');

        // Valoare ≥ 0
        const $val = $('#valoare');
        if ($val.val() !== '' && Number($val.val()) < 0)
            err($val, 'Valoarea nu poate fi negativă.');

        // Data livrare — nu în trecut
        const $dl = $('#data_livrare');
        if ($dl.val()) {
            const selectata = new Date($dl.val());
            const azi = new Date();
            azi.setHours(0, 0, 0, 0);
            if (selectata < azi)
                err($dl, 'Data livrării nu poate fi în trecut.');
        }

        // Metodă plată selectată
        if (!$('input[name="metoda_plata"]:checked').length) {
            erori.push('- Alegeți o metodă de plată.');
            ok = false;
        }

        if (!ok) {
            e.preventDefault();
            $alerta
                .css({ background: '#fee2e2', color: '#b91c1c', border: '1px solid #fca5a5' })
                .html('<strong>Atenție! Formularul conține erori:</strong><br>' + erori.join('<br>'))
                .slideDown(300);
            $('html, body').animate({ scrollTop: $('#add-order').offset().top - 50 }, 400);
        } else {
            $alerta.slideUp(100);
            // No preventDefault — PHP receives the POST and saves to DB
        }
    });
});

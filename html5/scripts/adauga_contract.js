$(document).ready(function () {

    const $form   = $('#add-contract-form');
    const $alerta = $('<div id="mesaj-alerta" style="display:none;margin-bottom:15px;padding:.75rem 1rem;border-radius:6px;"></div>')
                        .insertAfter('#add-contract h1');

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

        // Parolă validare ≥ 6 chars (only if filled — it's optional)
        const $parola = $('#parola_validare');
        if ($parola.val() && $parola.val().length < 6)
            err($parola, 'Parola de validare trebuie să conțină minim 6 caractere.');

        // Client obligatoriu
        const $client = $('#client');
        if ($client.val().trim() === '')
            err($client, 'Denumirea clientului este obligatorie.');

        // CUI — between 6 and 10 chars if filled
        const $cui = $('#cui_client');
        if ($cui.val() && ($cui.val().length < 4 || $cui.val().length > 12))
            err($cui, 'CUI-ul trebuie să aibă între 4 și 12 caractere.');

        // Valoare > 0
        const $valoare = $('#valoare');
        if (!$valoare.val() || Number($valoare.val()) <= 0)
            err($valoare, 'Valoarea contractului trebuie să fie mai mare ca 0.');

        // Durată 1–365
        const $durata = $('#durata');
        const d = Number($durata.val());
        if (!$durata.val() || d < 1 || d > 365)
            err($durata, 'Durata trebuie să fie cuprinsă între 1 și 365 zile.');

        // Fișier — PDF dacă este furnizat
        const $fisier = $('#fisier_atasat');
        if ($fisier.val() && !$fisier.val().toLowerCase().endsWith('.pdf'))
            err($fisier, 'Fișierul atașat trebuie să fie în format .pdf.');

        if (!ok) {
            e.preventDefault();
            $alerta
                .css({ background: '#fee2e2', color: '#b91c1c', border: '1px solid #fca5a5' })
                .html('<strong>Atenție! Formularul conține erori:</strong><br>' + erori.join('<br>'))
                .slideDown(300);
            $('html, body').animate({ scrollTop: $('#add-contract').offset().top - 50 }, 400);
        } else {
            $alerta.slideUp(100);
            // No preventDefault — PHP receives the POST and saves to DB
        }
    });
});

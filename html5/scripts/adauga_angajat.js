$(document).ready(function () {

    // ── CNP live extraction ───────────────────────────────────────────────
    // Fills readonly display fields client-side as the user types,
    // and also updates the hidden input so the value is sent on POST.
    const $cnp = $('#cnp');

    function extractCNP(cnp) {
        if (!/^[0-9]{13}$/.test(cnp)) { resetCNP(); return; }

        const s  = parseInt(cnp[0]);
        const yy = parseInt(cnp.substring(1, 3));
        const mm = parseInt(cnp.substring(3, 5));
        const dd = parseInt(cnp.substring(5, 7));

        let an;
        if      ([1, 2].includes(s)) an = 1900 + yy;
        else if ([3, 4].includes(s)) an = 1800 + yy;
        else if ([5, 6].includes(s)) an = 2000 + yy;
        else                         an = (yy > 25) ? 1900 + yy : 2000 + yy;

        if (an === 0 || mm < 1 || mm > 12 || dd < 1 || dd > 31) { resetCNP(); return; }

        const genCode  = (s % 2 !== 0) ? 'M' : 'F';
        const genLabel = genCode === 'M' ? 'Masculin' : 'Feminin';
        const dataN    = new Date(an, mm - 1, dd);
        const azi      = new Date();

        let varsta = azi.getFullYear() - dataN.getFullYear();
        if (azi.getMonth() < dataN.getMonth() ||
            (azi.getMonth() === dataN.getMonth() && azi.getDate() < dataN.getDate())) {
            varsta--;
        }

        const pad  = n => String(n).padStart(2, '0');
        const data = an + '-' + pad(mm) + '-' + pad(dd);

        $('#gen_display').val(genLabel);
        $('#gen_hidden').val(genCode);
        $('#data_nasterii_display').val(data);
        $('#varsta_display').val(varsta);
    }

    function resetCNP() {
        $('#gen_display').val('');
        $('#gen_hidden').val('');
        $('#data_nasterii_display').val('');
        $('#varsta_display').val('');
    }

    $cnp.on('input', function () { extractCNP($(this).val()); });

    // ── Validation ────────────────────────────────────────────────────────
    const $form   = $('#form-angajat');
    const $alerta = $('<div id="mesaj-alerta" style="display:none;margin-bottom:15px;padding:.75rem 1rem;border-radius:6px;"></div>')
                        .insertAfter('#add-employee h1');

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

        const $nume = $('#nume');
        if ($nume.val().trim().length < 3)
            err($nume, 'Numele trebuie să conțină minim 3 caractere.');

        const $prenume = $('#prenume');
        if ($prenume.val().trim().length < 2)
            err($prenume, 'Prenumele trebuie să conțină minim 2 caractere.');

        if ($cnp.val() && !/^[0-9]{13}$/.test($cnp.val()))
            err($cnp, 'CNP-ul trebuie să conțină exact 13 cifre.');

        const varsta = parseInt($('#varsta_display').val());
        if (!isNaN(varsta) && varsta < 18)
            err($cnp, 'Persoana este minoră (' + varsta + ' ani). Angajarea nu este permisă.');

        const $email = $('#email');
        if ($email.val() && $email.val().indexOf('@') === -1)
            err($email, 'Introduceți o adresă de email validă.');

        const $dep = $('#departament');
        if (!$dep.val())
            err($dep, 'Selectați un departament.');

        const $sal = $('#salariu');
        if (Number($sal.val()) < 2000)
            err($sal, 'Salariul trebuie să fie minim 2000 RON.');

        const $gdpr = $('#acord_gdpr');
        if ($gdpr.val() && !$gdpr.val().toLowerCase().endsWith('.pdf'))
            err($gdpr, 'Acordul GDPR trebuie să fie fișier .pdf.');

        const $cv = $('#cv_atasat');
        if ($cv.val() && !$cv.val().toLowerCase().endsWith('.pdf'))
            err($cv, 'CV-ul trebuie să fie fișier .pdf.');

        if (!ok) {
            e.preventDefault();
            $alerta
                .css({ background: '#fee2e2', color: '#b91c1c', border: '1px solid #fca5a5' })
                .html('<strong>Atenție! Formularul conține erori:</strong><br>' + erori.join('<br>'))
                .slideDown(300);
            $('html, body').animate({ scrollTop: $('#add-employee').offset().top - 50 }, 400);
        } else {
            $alerta.slideUp(100);
            // No preventDefault — PHP receives the POST and saves to DB
        }
    });
});

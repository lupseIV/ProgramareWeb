document.addEventListener("DOMContentLoaded", function() {
    const formAngajat = document.getElementById("form-angajat");

    // ==========================================
    // PARSARE AUTOMATĂ A CNP-ULUI
    // ==========================================
    const cnpInput = document.getElementById("cnp");
    const genInput = document.getElementById("gen");
    const dataNasteriiInput = document.getElementById("data_nasterii_cnp");
    const varstaInput = document.getElementById("varsta");

    if (cnpInput) {
        cnpInput.addEventListener("input", function() {
            const cnp = this.value;

            // Verificăm dacă sunt exact 13 cifre
            if (/^[0-9]{13}$/.test(cnp)) {
                // Extragem părțile componente
                const s = parseInt(cnp.charAt(0)); // Sex și secol
                const aa = parseInt(cnp.substring(1, 3)); // An
                const ll = parseInt(cnp.substring(3, 5)); // Lună
                const zz = parseInt(cnp.substring(5, 7)); // Ziua

                let an = 0;

                // Determinăm anul complet pe baza primei cifre (S)
                if (s === 1 || s === 2) an = 1900 + aa;
                else if (s === 3 || s === 4) an = 1800 + aa;
                else if (s === 5 || s === 6) an = 2000 + aa;
                else if (s === 7 || s === 8 || s === 9) {
                    // Cetățeni străini / rezidenți - aproximăm secolul după cifrele anului
                    an = (aa > 25) ? 1900 + aa : 2000 + aa;
                }

                // Validăm logica datei calendaristice (ex: luna <= 12, ziua <= 31)
                if (an > 0 && ll >= 1 && ll <= 12 && zz >= 1 && zz <= 31) {

                    // 1. Completăm Genul (Impar = Masculin, Par = Feminin)
                    // Excepție: 9 este de obicei pentru cetățeni străini unde sexul e determinat altfel, dar aplicam regula generală.
                    genInput.value = (s % 2 !== 0) ? "Masculin" : "Feminin";

                    // 2. Completăm Data Nașterii (Format HTML Date: YYYY-MM-DD)
                    const lunaStr = ll < 10 ? '0' + ll : ll;
                    const ziuaStr = zz < 10 ? '0' + zz : zz;
                    dataNasteriiInput.value = `${an}-${lunaStr}-${ziuaStr}`;

                    // 3. Calculăm și completăm Vârsta exactă (ținând cont de luna curentă)
                    const azi = new Date();
                    const dataN = new Date(an, ll - 1, zz);
                    let varsta = azi.getFullYear() - dataN.getFullYear();

                    const diferentaLuni = azi.getMonth() - dataN.getMonth();
                    if (diferentaLuni < 0 || (diferentaLuni === 0 && azi.getDate() < dataN.getDate())) {
                        varsta--; // Încă nu și-a serbat ziua anul acesta
                    }
                    varstaInput.value = varsta;

                    // Curățăm erorile vizuale de pe CNP dacă a fost valid
                    cnpInput.classList.remove("eroare-validare");

                } else {
                    reseteazaExtragereCNP();
                }
            } else {
                reseteazaExtragereCNP();
            }
        });
    }

    // Funcție pentru golirea câmpurilor dacă CNP-ul este șters sau invalid
    function reseteazaExtragereCNP() {
        if(genInput) genInput.value = "";
        if(dataNasteriiInput) dataNasteriiInput.value = "";
        if(varstaInput) varstaInput.value = "";
    }

    // ==========================================
    // VALIDAREA FORMULARULUI (SUBMIT)
    // ==========================================
    if (formAngajat) {
        formAngajat.addEventListener("submit", function(event) {
            let esteValid = true;
            let mesajeEroare = [];

            // Curățăm erorile anterioare
            const elementeCuEroare = formAngajat.querySelectorAll(".eroare-validare");
            elementeCuEroare.forEach(function(el) {
                el.classList.remove("eroare-validare");
            });

            function marcheazaEroare(idElement) {
                const element = document.getElementById(idElement);
                if (element) {
                    element.classList.add("eroare-validare");
                    esteValid = false;
                }
            }

            // Validare Nume
            const nume = document.getElementById("nume_angajat");
            if (!nume.value || nume.value.trim().length < 3) {
                marcheazaEroare("nume_angajat");
                mesajeEroare.push("- Numele trebuie să conțină minim 3 caractere.");
            }

            // Validare CNP (exact 13 cifre)
            const cnp = document.getElementById("cnp");
            const cnpRegex = /^[0-9]{13}$/;
            if (!cnp.value || !cnpRegex.test(cnp.value)) {
                marcheazaEroare("cnp");
                mesajeEroare.push("- CNP-ul este invalid. Trebuie să conțină exact 13 cifre.");
            }

            // Validare Minor (Extra Validare bazată pe extragerea vârstei)
            if (varstaInput && varstaInput.value) {
                if (parseInt(varstaInput.value) < 18) {
                    marcheazaEroare("cnp");
                    marcheazaEroare("varsta");
                    mesajeEroare.push("- Persoana este minoră (" + varstaInput.value + " ani). Angajarea necesită condiții speciale / este interzisă.");
                }
            }

            // Validare Email
            const email = document.getElementById("email_angajat");
            if (!email.value || email.value.indexOf('@') === -1) {
                marcheazaEroare("email_angajat");
                mesajeEroare.push("- Introduceți o adresă de email validă.");
            }

            // Validare Departament
            const departament = document.getElementById("departament_ang");
            if (!departament.value) {
                marcheazaEroare("departament_ang");
                mesajeEroare.push("- Selectați un departament.");
            }

            // Validare data angajare
            const dataAngajare = document.getElementById("data_angajare")
            if (!dataAngajare.value){
                marcheazaEroare("data_angajare")
                mesajeEroare.push("- Selectați data de angajare.")
            } else {
                const dataSelectata = new Date(dataAngajare.value);
                const dataAzi = new Date();
                dataAzi.setHours(0,0,0,0);

                if (dataSelectata < dataAzi) {
                    marcheazaEroare("data_angajare");
                    mesajeEroare.push("- Data angajării nu poate fi în trecut.");
                }
            }

            // Validare Salariu
            const salariu = document.getElementById("salariu");
            if (!salariu.value || Number(salariu.value) < 2000) {
                marcheazaEroare("salariu");
                mesajeEroare.push("- Salariul de bază trebuie să fie minim 2000 RON.");
            }

            // Validare Acord GDPR
            const acordGdpr = document.getElementById("acord_gdpr");
            if (!acordGdpr.value) {
                marcheazaEroare("acord_gdpr");
                mesajeEroare.push("- Este necesar acordul pentru prelucrarea datelor (GDPR).");
            } else if (!acordGdpr.value.toLowerCase().endsWith(".pdf")){
                marcheazaEroare("acord_gdpr")
                mesajeEroare.push("- Acordul GDPR trebuie să fie in format .pdf.")
            }

            // Validare CV
            const cv = document.getElementById("cv_atasat")
            if(cv.value && !cv.value.toLowerCase().endsWith(".pdf")){
                marcheazaEroare("cv_atasat")
                mesajeEroare.push("- CV-ul trebuie să fie in format .pdf.")
            }

            // Finalizare
            if (!esteValid) {
                event.preventDefault();
                alert("Erori la înregistrare:\n\n" + mesajeEroare.join("\n"));
            } else {
                event.preventDefault(); // Pentru testare frontend
                alert("Angajatul a fost înregistrat cu succes!");
            }
        });
    }
});
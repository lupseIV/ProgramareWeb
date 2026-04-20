document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form");

    if (form) {
        form.addEventListener("submit", function(event) {
            let esteValid = true;
            let mesajeEroare = [];

            const elementeCuEroare = form.querySelectorAll(".eroare-validare");
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


            const parola = document.getElementById("parola_validare");
            if (!parola.value || parola.value.length < 6) {
                marcheazaEroare("parola_validare");
                mesajeEroare.push("- Parola trebuie să conțină minim 6 caractere.");
            }

            const numeClient = document.getElementById("nume_client");
            if (!numeClient.value || numeClient.value.trim() === "") {
                marcheazaEroare("nume_client");
                mesajeEroare.push("- Introduceți denumirea clientului.");
            }

            const cui = document.getElementById("cui_client");
            if (!cui.value || cui.value.length < 6 || cui.value.length > 10) {
                marcheazaEroare("cui_client");
                mesajeEroare.push("- CUI-ul trebuie să aibă între 6 și 10 caractere.");
            }

            const valoare = document.getElementById("valoare");
            if (!valoare.value || Number(valoare.value) <= 0) {
                marcheazaEroare("valoare");
                mesajeEroare.push("- Valoarea contractului trebuie să fie mai mare ca 0.");
            }

            const durata = document.getElementById("durata");
            if (!durata.value || Number(durata.value) <= 0 || Number(durata.value) > 365) {
                marcheazaEroare("durata");
                mesajeEroare.push("- Durata trebuie să fie cuprinsă între 1 și 365 zile.");
            }

            const servicii = document.getElementById("servicii");
            if (servicii && servicii.selectedOptions.length === 0) {
                marcheazaEroare("servicii");
                mesajeEroare.push("- Trebuie să selectați cel puțin un serviciu.");
            }

            const fisier = document.getElementById("fisier_atasat");
            if (!fisier.value) {
                marcheazaEroare("fisier_atasat");
                mesajeEroare.push("- Atașați copia PDF a contractului.");
            } else if (!fisier.value.toLowerCase().endsWith(".pdf")) {
                marcheazaEroare("fisier_atasat");
                mesajeEroare.push("- Fișierul atașat trebuie să aibă extensia .pdf");
            }

            if (!esteValid) {
                event.preventDefault();
                alert("Atenție! Formularul conține erori:\n\n" + mesajeEroare.join("\n"));
            } else {
                event.preventDefault();
                alert("Toate datele sunt corecte! Contractul ar fi fost salvat cu succes.");
            }
        });
    }
});
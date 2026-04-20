document.addEventListener("DOMContentLoaded", function() {
    const formComanda = document.getElementById("form-comanda");

    // Elemente pentru populare dinamica
    const taraSelect = document.getElementById("tara_client");
    const clientSelect = document.getElementById("client_comanda");
    const adresaSelect = document.getElementById("adresa_livrare_select");
    const adresaNouaInput = document.getElementById("adresa_livrare_noua");

    // Funcție ajutătoare pentru a reseta opțiunile de adrese (păstrând opțiunea "+ Introdu adresă nouă")
    function curataAdrese() {
        if(!adresaSelect) return;
        const optiuni = adresaSelect.querySelectorAll('option');
        optiuni.forEach(opt => {
            // Nu stergem prima optiune goala si nici optiunea de "adresa noua"
            if (opt.value !== "" && opt.value !== "noua") {
                opt.remove();
            }
        });
        adresaSelect.value = "";
        adresaNouaInput.style.display = "none";
        adresaNouaInput.value = "";
    }

    // 1. Afișare input de adresă nouă la schimbare (Change Event pe Adresă)
    if (adresaSelect) {
        adresaSelect.addEventListener("change", function() {
            if (this.value === "noua") {
                // Afisam input-ul
                adresaNouaInput.style.display = "block";
                adresaNouaInput.focus();
            } else {
                // Ascundem input-ul si stergem ce a scris
                adresaNouaInput.style.display = "none";
                adresaNouaInput.value = "";
                adresaNouaInput.classList.remove("eroare-validare");
            }
        });
    }

    // 2. Dependență Țară -> Clienți
    if (taraSelect && clientSelect) {
        taraSelect.addEventListener("change", function() {
            const taraAleasa = this.value;
            clientSelect.innerHTML = '<option value="">-- Alege Client --</option>';
            curataAdrese(); // Resetăm și adresele pentru că s-a resetat clientul

            if (taraAleasa && typeof dateComanda !== 'undefined' && dateComanda.tari_clienti[taraAleasa]) {
                const clienti = dateComanda.tari_clienti[taraAleasa];
                clienti.forEach(client => {
                    const option = document.createElement("option");
                    option.value = client.id;
                    option.textContent = client.nume;
                    clientSelect.appendChild(option);
                });
            } else if (!taraAleasa) {
                clientSelect.innerHTML = '<option value="">-- Alegeți întâi țara --</option>';
            }
        });
    }

    // 3. Dependență Client -> Adrese
    if (clientSelect && adresaSelect) {
        clientSelect.addEventListener("change", function() {
            const clientAles = this.value;
            curataAdrese(); // Goliți adresele vechi înainte să le puneți pe cele noi

            if (clientAles && typeof dateComanda !== 'undefined') {
                let adrese = [];

                // Căutăm adresele specifice clientului ales în data.js
                if (dateComanda.adrese_clienti && dateComanda.adrese_clienti[clientAles]) {
                    adrese = dateComanda.adrese_clienti[clientAles];
                }
                // Fallback: dacă clientul nu are adrese, arătăm o listă globală
                else if (dateComanda.adrese_precedente) {
                    adrese = dateComanda.adrese_precedente;
                }

                // Inserăm noile adrese fix înainte de opțiunea "Adresă nouă..."
                const optiuneNoua = adresaSelect.querySelector('option[value="noua"]');
                adrese.forEach(adresa => {
                    const option = document.createElement("option");
                    option.value = adresa;
                    option.textContent = adresa;
                    adresaSelect.insertBefore(option, optiuneNoua);
                });
            }
        });
    }

    // --- VALIDARE FORMULAR LA SUBMIT ---
    if (formComanda) {
        formComanda.addEventListener("submit", function(event) {
            let esteValid = true;
            let mesajeEroare = [];

            // Curățăm erorile anterioare vizuale
            const elementeCuEroare = formComanda.querySelectorAll(".eroare-validare");
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

            // Validare Țară
            if (!taraSelect.value) {
                marcheazaEroare("tara_client");
                mesajeEroare.push("- Vă rugăm să selectați țara.");
            }

            // Validare Client
            if (!clientSelect.value) {
                marcheazaEroare("client_comanda");
                mesajeEroare.push("- Vă rugăm să selectați clientul.");
            }

            // Validare Adresă (Complexă: fie din select, fie din inputul vizibil)
            if (!adresaSelect.value) {
                marcheazaEroare("adresa_livrare_select");
                mesajeEroare.push("- Selectați o adresă de livrare.");
            } else if (adresaSelect.value === "noua") {
                // Dacă a ales adresă nouă, validăm inputul de text
                if (!adresaNouaInput.value || adresaNouaInput.value.trim().length < 5) {
                    marcheazaEroare("adresa_livrare_noua");
                    mesajeEroare.push("- Introduceți o adresă validă (minim 5 caractere).");
                }
            }

            // Validare Produs
            const produs = document.getElementById("produs_serviciu");
            if (!produs.value || produs.value.trim() === "") {
                marcheazaEroare("produs_serviciu");
                mesajeEroare.push("- Specificați produsul sau serviciul comandat.");
            }

            // Validare Cantitate
            const cantitate = document.getElementById("cantitate");
            if (!cantitate.value || Number(cantitate.value) < 1) {
                marcheazaEroare("cantitate");
                mesajeEroare.push("- Cantitatea trebuie să fie cel puțin 1.");
            }

            // Validare Dată Livrare (să nu fie în trecut)
            const dataLivrare = document.getElementById("data_livrare");
            if (!dataLivrare.value) {
                marcheazaEroare("data_livrare");
                mesajeEroare.push("- Selectați data livrării dorite.");
            } else {
                const dataSelectata = new Date(dataLivrare.value);
                const dataAzi = new Date();
                dataAzi.setHours(0,0,0,0);

                if (dataSelectata < dataAzi) {
                    marcheazaEroare("data_livrare");
                    mesajeEroare.push("- Data livrării nu poate fi în trecut.");
                }
            }

            // Validare Metodă de Plată (Radio Buttons)
            const optiuniPlata = document.getElementsByName("metoda_plata");
            let metodaSelectata = false;
            for (let i = 0; i < optiuniPlata.length; i++) {
                if (optiuniPlata[i].checked) {
                    metodaSelectata = true;
                    break;
                }
            }
            if (!metodaSelectata) {
                mesajeEroare.push("- Vă rugăm să alegeți o metodă de plată.");
                esteValid = false;
            }

            // Finalizare Trimitere Formular
            if (!esteValid) {
                event.preventDefault();
                alert("Erori la plasarea comenzii:\n\n" + mesajeEroare.join("\n"));
            } else {
                event.preventDefault(); // Doar pentru laborator/testare, blocheaza reload-ul
                alert("Comanda a fost procesată și validată cu succes!");
            }
        });
    }
});
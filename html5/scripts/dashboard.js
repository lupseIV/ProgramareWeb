document.addEventListener("DOMContentLoaded", () => {

    let clienti = (typeof dateComanda !== 'undefined' && dateComanda.top_clienti)
        ? [...dateComanda.top_clienti] : [];

    let sortColClienti = '';
    let sortAscClienti = true;

    const tbodyClienti = document.querySelector('#tabel-top-clienti tbody');
    const thsClienti = document.querySelectorAll('#tabel-top-clienti th');
    const chartContainer = document.getElementById('bar-chart-clienti');

    function renderClienti() {
        if (!tbodyClienti) return;
        tbodyClienti.innerHTML = '';

        clienti.forEach(c => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>${c.nume}</strong></td>
                <td>${c.comenzi}</td>
                <td style="color: var(--accent); font-weight: bold;">${c.valoare.toLocaleString()} €</td>
            `;
            tbodyClienti.appendChild(tr);
        });

        renderChart();
    }

    function renderChart() {
        if (!chartContainer) return;
        chartContainer.innerHTML = '';
        if (clienti.length === 0) return;

        const maxVal = Math.max(...clienti.map(c => c.valoare));

        clienti.forEach(c => {
            const heightPct = (c.valoare / maxVal) * 100;

            const barWrapper = document.createElement('div');
            barWrapper.className = 'bar-wrapper';

            const bar = document.createElement('div');
            bar.className = 'bar';
            bar.style.height = `${Math.max(heightPct, 5)}%`;
            bar.innerHTML = `<span>${c.valoare >= 1000 ? (c.valoare/1000)+'k' : c.valoare}</span>`;

            const label = document.createElement('div');
            label.className = 'bar-label';
            label.textContent = c.nume.split(' ')[0];

            barWrapper.appendChild(bar);
            barWrapper.appendChild(label);
            chartContainer.appendChild(barWrapper);
        });
    }

    thsClienti.forEach(th => {
        th.addEventListener('click', () => {
            const col = th.dataset.col;

            if (sortColClienti === col) {
                sortAscClienti = !sortAscClienti;
            } else {
                sortColClienti = col;
                sortAscClienti = true;
            }

            // Actualizare indicatori vizuali (săgeți CSS)
            thsClienti.forEach(h => h.classList.remove('sort-asc', 'sort-desc'));
            th.classList.add(sortAscClienti ? 'sort-asc' : 'sort-desc');

            // Logica de sortare a array-ului
            clienti.sort((a, b) => {
                let valA = a[col];
                let valB = b[col];

                if (typeof valA === 'string') {
                    return sortAscClienti ? valA.localeCompare(valB) : valB.localeCompare(valA);
                }
                return sortAscClienti ? (valA - valB) : (valB - valA);
            });

            renderClienti();
        });
    });


    let ore = (typeof dateComanda !== 'undefined' && dateComanda.ore_suplimentare)
        ? [...dateComanda.ore_suplimentare] : [];
    let rataBonus = (typeof dateComanda !== 'undefined' && dateComanda.rata_bonus_ora)
        ? dateComanda.rata_bonus_ora : 50;

    let sortColOre = '';
    let sortAscOre = true;

    const tbodyOre = document.querySelector('#tabel-ore-vertical tbody');

    function renderOreVertical() {
        if (!tbodyOre) return;
        tbodyOre.innerHTML = '';

        const dataInbogatita = ore.map(o => ({
            nume: o.nume,
            ore: o.ore,
            bonus: o.ore * rataBonus
        }));

        const trNume = document.createElement('tr');
        trNume.innerHTML = `<th data-col="nume" class="sort-header-vert ${sortColOre === 'nume' ? (sortAscOre ? 'sort-asc' : 'sort-desc') : ''}">Nume Angajat</th>`;
        dataInbogatita.forEach((o, index) => {
            trNume.innerHTML += `<td class="${sortColOre === 'nume' ? 'sorted-col' : ''}">${o.nume}</td>`;
        });

        const trOre = document.createElement('tr');
        trOre.innerHTML = `<th data-col="ore" class="sort-header-vert ${sortColOre === 'ore' ? (sortAscOre ? 'sort-asc' : 'sort-desc') : ''}">Ore Suplimentare</th>`;
        dataInbogatita.forEach((o, index) => {
            trOre.innerHTML += `<td class="${sortColOre === 'ore' ? 'sorted-col' : ''}">${o.ore}h</td>`;
        });

        const trBonus = document.createElement('tr');
        trBonus.innerHTML = `<th data-col="bonus" class="sort-header-vert ${sortColOre === 'bonus' ? (sortAscOre ? 'sort-asc' : 'sort-desc') : ''}">Bonus Generat (RON)</th>`;
        dataInbogatita.forEach((o, index) => {
            trBonus.innerHTML += `<td class="${sortColOre === 'bonus' ? 'sorted-col' : ''}">+ ${o.bonus} RON</td>`;
        });

        tbodyOre.appendChild(trNume);
        tbodyOre.appendChild(trOre);
        tbodyOre.appendChild(trBonus);

        document.querySelectorAll('.sort-header-vert').forEach(th => {
            th.addEventListener('click', (e) => {
                const col = e.target.dataset.col;

                if (sortColOre === col) {
                    sortAscOre = !sortAscOre;
                } else {
                    sortColOre = col;
                    sortAscOre = true;
                }

                ore.sort((a, b) => {
                    let valA = a[col];
                    let valB = b[col];

                    if (col === 'bonus') {
                        valA = a.ore * rataBonus;
                        valB = b.ore * rataBonus;
                    }

                    if (typeof valA === 'string') {
                        return sortAscOre ? valA.localeCompare(valB) : valB.localeCompare(valA);
                    }
                    return sortAscOre ? (valA - valB) : (valB - valA);
                });

                renderOreVertical();
            });
        });
    }

    renderClienti();
    renderOreVertical();
});
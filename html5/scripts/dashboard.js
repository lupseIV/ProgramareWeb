$(document).ready(() => {

    let clienti = (typeof dateComanda !== 'undefined' && dateComanda.top_clienti)
        ? [...dateComanda.top_clienti] : [];

    let sortColClienti = '';
    let sortAscClienti = true;

    const $tbodyClienti = $('#tabel-top-clienti tbody');
    const $thsClienti = $('#tabel-top-clienti th');
    const $chartContainer = $('#bar-chart-clienti');

    function renderClienti() {
        if (!$tbodyClienti.length) return;
        $tbodyClienti.empty()

        clienti.forEach(c => {
            $tbodyClienti.append(
                $('<tr>')
                    .html(`
                        <td><strong>${c.nume}</strong></td>
                        <td>${c.comenzi}</td>
                        <td style="color: var(--accent); font-weight: bold;">${c.valoare.toLocaleString()} €</td>
                        `)
            );
        });
        renderChart();
    }

    function renderChart() {
        if (!$chartContainer.length) return;
        $chartContainer.empty();
        if (clienti.length === 0) return;

        const maxVal = Math.max(...clienti.map(c => c.valoare));

        clienti.forEach(c => {
            const heightPct = (c.valoare / maxVal) * 100;
            $chartContainer.append(
                $('<div>', {
                    class: 'bar-wrapper'
                })
                    .append($('<div>',{
                        class: 'bar-label',
                        text: c.nume.split(' ')[0]
                }))
                    .prepend($('<div>', {
                        class: 'bar',
                    }).css('height', `${Math.max(heightPct, 5)}%`)
                        .html(`<span>${c.valoare >= 1000 ? (c.valoare/1000)+'k' : c.valoare}</span>`))
            );
        });
    }

    $thsClienti.click(function() {
        const col = $(this).attr('data-col');
        if (sortColClienti === col) {
            sortAscClienti = !sortAscClienti;
        } else {
            sortColClienti = col;
            sortAscClienti = true;
        }

        $thsClienti.removeClass('sort-asc sort-desc');
        $(this).addClass(sortAscClienti ? 'sort-asc' : 'sort-desc');

        clienti.sort((a, b) => {
            let valA = a[col];
            let valB = b[col];

            if (typeof valA === 'string') {
                return sortAscClienti ? valA.localeCompare(valB) : valB.localeCompare(valA);
            }
            return sortAscClienti ? (valA - valB) : (valB - valA);
        });

        renderClienti();
    })


    let ore = (typeof dateComanda !== 'undefined' && dateComanda.ore_suplimentare)
        ? [...dateComanda.ore_suplimentare] : [];
    let rataBonus = (typeof dateComanda !== 'undefined' && dateComanda.rata_bonus_ora)
        ? dateComanda.rata_bonus_ora : 50;

    let sortColOre = '';
    let sortAscOre = true;

    const $tbodyOre = $('#tabel-ore-vertical tbody');

    function createHorizontalCol(colName, colClass, colText, columnData, tdClass){
        const $tr = $('<tr>').append(
            $('<th>', {
                'data-col': colName,
                class: colClass,
                text: colText
            })
        );
        const $tds = columnData.map(val => {
            return $('<td>', {
                class: tdClass,
                html: val
            });
        });
        return $tr.append($tds);
    }
    function renderOreVertical() {
        if (!$tbodyOre.length) return;
        $tbodyOre.empty();

        const dataInbogatita = ore.map(o => ({
            nume: o.nume,
            ore: o.ore,
            bonus: o.ore * rataBonus
        }));

        $tbodyOre
            .append(createHorizontalCol(
                "nume",
                `sort-header-vert ${sortColOre === 'nume' ? (sortAscOre ? 'sort-asc' : 'sort-desc') : ''}`,
                "Nume Angajat",
                dataInbogatita.map(o => o.nume),
                `${sortColOre === 'nume' ? 'sorted-col' : ''}`))
            .append(createHorizontalCol(
                "ore",
                `sort-header-vert ${sortColOre === 'ore' ? (sortAscOre ? 'sort-asc' : 'sort-desc') : ''}`,
                "Ore Suplimentare",
                dataInbogatita.map(o => `${o.ore}h`),
                `${sortColOre === 'ore' ? 'sorted-col' : ''}`
            ))
            .append(createHorizontalCol(
                "bonus",
                `sort-header-vert ${sortColOre === 'bonus' ? (sortAscOre ? 'sort-asc' : 'sort-desc') : ''}`,
                "Bonus Generat (RON)",
                dataInbogatita.map(o => `+ ${o.bonus} RON`),
                `${sortColOre === 'bonus' ? 'sorted-col' : ''}`
            ));

        $('.sort-header-vert').click(function() {
                const col = $(this).attr('data-col');
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
    }

    renderClienti();
    renderOreVertical();
});
const dateComanda = {
    tari_clienti: {
        "Romania": [
            { id: "ro_1", nume: "SC Alpha Solutions SRL" },
            { id: "ro_2", nume: "SC Beta Trade SA" },
            { id: "ro_3", nume: "Persoană Fizică (RO)" }
        ],
        "Germania": [
            { id: "de_1", nume: "Tech GmbH" },
            { id: "de_2", nume: "Muller Logistics" }
        ],
        "Italia": [
            { id: "it_1", nume: "Roma Tech SpA" },
            { id: "it_2", nume: "Genoa Imports" }
        ]
    },
    adrese_clienti: {
        "ro_1": ["Str. Inovației Nr. 10, București", "Bd. Unirii Nr. 45, Cluj-Napoca"],
        "ro_2": ["Str. Industriei 2, Timișoara"],
        "ro_3": ["Aleea Trandafirilor 5, Iași", "Str. Libertății 10, Brașov"],
        "de_1": ["Berliner Straße 12, Munchen", "Alexanderplatz 1, Berlin"],
        "de_2": ["Hamburger Weg 5, Hamburg"],
        "it_1": ["Via Roma 100, Milano"],
        "it_2": ["Piazza San Marco 1, Venezia"]
    },
    adrese_precedente: [
        "Str. Inovației Nr. 10, București",
        "Bd. Unirii Nr. 45, Cluj-Napoca",
        "Str. Principala 1, Timișoara",
        "Berliner Straße 12, Munchen",
        "Via Roma 100, Milano"
    ],

    top_clienti: [
        { nume: "Alpha Solutions", comenzi: 15, valoare: 45000 },
        { nume: "Beta Trade SA", comenzi: 8, valoare: 22000 },
        { nume: "Tech GmbH", comenzi: 12, valoare: 38000 },
        { nume: "Roma Tech SpA", comenzi: 20, valoare: 60000 },
        { nume: "Muller Logistics", comenzi: 5, valoare: 15000 }
    ],
    ore_suplimentare: [
        { nume: "Popescu Ion", ore: 12 },
        { nume: "Ionescu Maria", ore: 8 },
        { nume: "Georgescu Dan", ore: 15 },
        { nume: "Avram Elena", ore: 5 },
        { nume: "Dumitru Alex", ore: 20 }
    ],
    rata_bonus_ora: 50
};
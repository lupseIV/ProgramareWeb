<?php
if (!defined('APP_RUNNING')) { header('Location: /?page=home'); exit; }
$pageTitle = "Enterprise Resource Planning";
$styles  = ["styles/responsive.css", "styles/carousel.css"];
$scripts = ["scripts/home.js"];

$toateSlide = [
    ['cheie' => 'angajat',   'link' => '?page=adauga_angajat',  'text' => 'Modul HR: Gestionează angajații eficient și sigur.',       'imagine' => 'img/carousel1.jpg'],
    ['cheie' => 'contract',  'link' => '?page=adauga_contract', 'text' => 'Management Contracte: Semnare și aprobare rapidă.',         'imagine' => 'img/carousel2.jpg'],
    ['cheie' => 'comanda',   'link' => '?page=adauga_comanda',  'text' => 'Sistem Comenzi: Trasabilitate completă a livrărilor.',      'imagine' => 'img/carousel3.jfif'],
    ['cheie' => 'dashboard', 'link' => '?page=dashboard',       'text' => 'Dashboard Analitic: Decizii bazate pe date în timp real.',  'imagine' => 'img/carousel4.png'],
];

$slidePerRol = [
    'EMPLOYEE' => ['comanda', 'dashboard'],
    'MANAGER'  => ['angajat', 'contract', 'comanda', 'dashboard'],
    'ADMIN'    => ['angajat', 'contract', 'comanda', 'dashboard'],
];
if (!isLogged()) {
    $slideFiltered = $toateSlide;
} else {
    $rol = $_SESSION['role'];
    $cheiPermise = $slidePerRol[$rol] ?? $slidePerRol['EMPLOYEE'];
    $slideFiltered = array_values(array_filter($toateSlide, function ($s) use ($cheiPermise) {
    return in_array($s['cheie'], $cheiPermise);}));
}
?>

<script>
    const carouselData = <?= json_encode($slideFiltered) ?>;
</script>

<main>
    <div class="carousel-section">
        <div class="carousel-container" id="erpCarousel">
            <button class="carousel-btn prev" id="carouselPrev" aria-label="Înapoi">&#10094;</button>
            <button class="carousel-btn next" id="carouselNext" aria-label="Înainte">&#10095;</button>

            <div class="carousel-indicators" id="carouselIndicators"></div>
        </div>
    </div>

    <section id="main-presentation">
        <h2>ERP Core</h2>
        <div class="presentation-container">
            <img alt="Logo Firma" class="floated" height="200" src="https://picsum.photos/400/300" width="300">
            <p>Suntem partenerul tău <b>strategic</b> în optimizarea capitalului uman și operațional, oferind <span>soluții</span>
                integrate de gestiune a resurselor care <strong>transformă complexitatea administrativă în eficiență
                    pură.</strong> Într-o piață dinamică, ne asumăm rolul de a simplifica fluxurile de lucru prin
                tehnologie de ultimă generație și expertiză personalizată, permițându-ți să te concentrezi pe ceea ce
                contează cu adevărat: creșterea afacerii tale. Credem că succesul oricărei organizații pleacă de la o
                administrare inteligentă și transparentă, motiv pentru care livrăm nu doar instrumente de monitorizare,
                ci o viziune clară asupra performanței și sustenabilității resurselor tale.</p>
        </div>
    </section>

    <section id="gdpr">
        <h2>Securitate & GDPR</h2>
        <div class="table-responsive"><table >
            <thead>
            <tr>
                <th class="text-center" colspan="3">Protocol de Protecție Date</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="security-meta" rowspan="3">
                    <strong>POLITICI DE SECURITATE</strong>
                </td>
                <td colspan="2"><b>Nivel de Confidențialitate: Înalt</b></td>
            </tr>
            <tr>
                <td>
                    <strong>AVIZ GDPR:</strong> Prelucrarea datelor conform Regulamentului (UE) 2016/679.
                </td>
                <td class="seal-cell" rowspan="2">
                    <img alt="Sigiliu Securitate" class="seal-img" height="60" src="https://picsum.photos/120/80"
                         width="100">
                </td>
            </tr>
            <tr>
                <td>Accesul neautorizat este monitorizat și raportat automat departamentului IT.</td>
            </tr>
            <tr>
                <td class="security-note" colspan="3">
                    Sistem securizat conform normelor ISO/IEC 27001
                </td>
            </tr>
            </tbody>
        </table></div>

    </section>

    <section id="performanta-si-servicii">
        <h2>Performanță & Servicii</h2>
        <table>
            <thead>
            <tr>
                <th class="service-banner" colspan="3">Management integrat al serviciilor</th>
            </tr>
            <tr>
                <th>Categorie Resurse</th>
                <th>Servicii Specifice</th>
                <th>Beneficiul Clientului</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><b>Resurse Umane (HR)</b></td>
                <td>Recrutare, administrare personal, salarizare și training.</td>
                <td>Reducerea costurilor și creșterea retenției angajaților.</td>
            </tr>
            <tr>
                <td><b>Resurse Materiale</b></td>
                <td>Gestiunea stocurilor și optimizarea lanțului de aprovizionare.</td>
                <td>Eliminarea pierderilor și control total asupra mărfii.</td>
            </tr>
            <tr>
                <td class="benefits-wrapper" colspan="3">
                    <h3 class="accent-title">De ce să ne alegi?</h3>
                    <table class="bg-white">
                        <thead>
                        <tr>
                            <th>Indicator Performanță</th>
                            <th>Obiectivul Nostru</th>
                            <th>Impactul în Afacere</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><b>Eficiență</b></td>
                            <td>Optimizare > 25%</td>
                            <td>Reducerea timpilor morți.</td>
                        </tr>
                        <tr>
                            <td><b>Tehnologie</b></td>
                            <td>Implementare Cloud / ERP</td>
                            <td>Acces date în timp real.</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td><b>Resurse Tehnice</b></td>
                <td>Mentenanța echipamentelor și monitorizarea activelor.</td>
                <td>Maximizarea duratei de viață a activelor.</td>
            </tr>
            </tbody>
        </table>
    </section>

    <section id="info">
        <h2>Informații Operaționale</h2>
        <h3>Mod de lucru (Standard Workflow)</h3>
        <ol class="info-list">
            <li>
                <b>Discuție inițială și Analiză:</b>
                <ul class="card-list">
                    <li>Prezentare generală a portofoliului.</li>
                    <li>Audit tehnic al infrastructurii.</li>
                    <li>Indicatori de performanță (KPI).</li>
                    <li>Program help-desk (SLA).</li>
                    <li>Contract și NDA.</li>
                </ul>
            </li>
            <li>
                <b>Implementare și Integrare:</b>
                <ul class="card-list">
                    <li>Modelarea aplicației pe cerințe.</li>
                    <li>Migrare baze de date.</li>
                    <li>Testare staging (UAT).</li>
                    <li>Workshop-uri angajați.</li>
                    <li>Suport 24/7 (2 săptămâni).</li>
                </ul>
            </li>
            <li>
                <b>Verificare și Mentenanță:</b>
                <table>
                    <thead>
                    <tr>
                        <th>Uptime</th>
                        <th>Securitate</th>
                        <th>Feedback</th>
                        <th>Mentenanță</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>99.9% Garantat</td>
                        <td>Audit trimestrial</td>
                        <td>Lunar utilizatori</td>
                        <td>Patch-uri critice</td>
                    </tr>
                    </tbody>
                </table>
            </li>
        </ol>
    </section>

    <section id="proceduri">
        <h2>Manual și Proceduri Interne</h2>
        <ul >
            <li>
                <span class="caret">1. Onboarding Angajați Noi</span>
                <ul class="nested">
                    <li>1.1. Configurare Conturi de rețea</li>
                    <li>1.2. Traseul documentelor HR</li>
                    <li>
                        <span class="caret">1.3. Echipamente</span>
                        <ul class="nested">
                            <li>1.3.1 Livrabil: Laptop Dell Latitude</li>
                            <li>1.3.2 Livrabil: Accesori periferice</li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li >
                <span class="caret">2. Protocol Semnare Contracte</span>
                <ul class="nested">
                    <li>2.1. Auditare Date Client</li>
                    <li>2.2. Aprobare Nivel 1 (Management)</li>
                    <li>2.3. Trimitere catre Arhiva Generala</li>
                </ul>
            </li>
        </ul>
    </section>

</main>

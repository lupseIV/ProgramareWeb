document.addEventListener("DOMContentLoaded", () => {

    const dateCarousel = [
        {
            link: "adauga_angajat.html",
            text: "Modul HR: Gestionează angajații eficient și sigur.",
            imagine: "img/carousel1.jpg"
        },
        {
            link: "adauga_contract.html",
            text: "Management Contracte: Semnare și aprobare rapidă.",
            imagine: "img/carousel2.jpg"
        },
        {
            link: "adauga_comanda.html",
            text: "Sistem Comenzi: Trasabilitate completă a livrărilor.",
            imagine: "img/carousel3.jfif"
        },
        {
            link: "dashboard.html",
            text: "Dashboard Analitic: Decizii bazate pe date în timp real.",
            imagine: "img/carousel4.png"
        }
    ];

    const carouselContainer = document.getElementById('erpCarousel');
    const indicatorsContainer = document.getElementById('carouselIndicators');

    let currentIndex = 0;
    let slideInterval;

    dateCarousel.forEach((slideDate, index) => {
        const slideLink = document.createElement('a');
        slideLink.href = slideDate.link;
        slideLink.className = `carousel-slide ${index === 0 ? 'active' : ''}`;
        slideLink.style.backgroundImage = `url('${slideDate.imagine}')`;

        const slideText = document.createElement('div');
        slideText.className = 'carousel-text';
        slideText.textContent = slideDate.text;

        slideLink.appendChild(slideText);
        carouselContainer.insertBefore(slideLink, document.getElementById('carouselPrev'));

        const indicator = document.createElement('div');
        indicator.className = `indicator ${index === 0 ? 'active' : ''}`;
        indicator.addEventListener('click', () => {
            goToSlide(index);
            resetInterval();
        });
        indicatorsContainer.appendChild(indicator);
    });

    const slides = document.querySelectorAll('.carousel-slide');
    const indicators = document.querySelectorAll('.indicator');

    function goToSlide(index) {
        slides[currentIndex].classList.remove('active');
        indicators[currentIndex].classList.remove('active');

        currentIndex = index;

        if (currentIndex >= slides.length) currentIndex = 0;
        if (currentIndex < 0) currentIndex = slides.length - 1;

        slides[currentIndex].classList.add('active');
        indicators[currentIndex].classList.add('active');
    }

    function nextSlide() { goToSlide(currentIndex + 1); }
    function prevSlide() { goToSlide(currentIndex - 1); }

    document.getElementById('carouselNext').addEventListener('click', () => {
        nextSlide();
        resetInterval();
    });

    document.getElementById('carouselPrev').addEventListener('click', () => {
        prevSlide();
        resetInterval();
    });

    function startInterval() {
        slideInterval = setInterval(nextSlide, 4000);
    }

    function resetInterval() {
        clearInterval(slideInterval);
        startInterval();
    }

    carouselContainer.addEventListener('mouseenter', () => clearInterval(slideInterval));
    carouselContainer.addEventListener('mouseleave', startInterval);

    startInterval();

    const careti = document.querySelectorAll('.caret');
    careti.forEach(caret => {
        caret.addEventListener('click', function() {
            const subLista = this.parentElement.querySelector('.nested');
            if(subLista) {
                subLista.classList.toggle('active');
                this.classList.toggle('caret-down');
            }
        });
    });
});
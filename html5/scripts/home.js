$(document).ready(() => {

    const dateCarousel = (typeof carouselData !== 'undefined') ? carouselData : [];

    const $erpCarousel = $('#erpCarousel');
    const $carouselIndicators = $('#carouselIndicators');
    const $carouselPrev = $('#carouselPrev')
    const $carouselNext = $('#carouselNext')

    let currentIndex = 0;
    let slideInterval;

    dateCarousel.forEach((slideDate, index) => {
        $carouselPrev.before($('<a>', {
            href: slideDate.link,
            class: `carousel-slide ${index === 0 ? 'active' : ''}`,
        })
            .css("backgroundImage", `url('${slideDate.imagine}')`)
            .append($('<div>', {
                class: 'carousel-text',
                text: slideDate.text
            })))

        $carouselIndicators.append($('<div>',{
            class: `indicator ${index === 0 ? 'active' : ''}`,
            click: function () {
                goToSlide(index);
                resetInterval();
            }
        }))
    })

    const $carouselSlides = $('.carousel-slide');
    const $carouselIndicator = $('.indicator');

    function goToSlide(index) {
        $carouselSlides.eq(currentIndex).removeClass('active');
        $carouselIndicator.eq(currentIndex).removeClass('active');

        currentIndex = index;

        if (currentIndex >= $carouselSlides.length) currentIndex = 0;
        if (currentIndex < 0) currentIndex = $carouselSlides.length - 1;

        $carouselSlides.eq(currentIndex).addClass('active');
        $carouselIndicator.eq(currentIndex).addClass('active');
    }

    function nextSlide() { goToSlide(currentIndex + 1); }
    function prevSlide() { goToSlide(currentIndex - 1); }

    $carouselNext.click(() => {
        nextSlide();
        resetInterval();
    });

    $carouselPrev.click(() => {
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

    $erpCarousel.mouseenter(() => clearInterval(slideInterval));
    $erpCarousel.mouseleave(startInterval);

    startInterval();

    $('.caret').click(function() {
        const subLista = $(this).siblings('.nested');

        if (subLista.length) {
            subLista.slideToggle(300);
            $(this).toggleClass('caret-down');
        }
    });
});
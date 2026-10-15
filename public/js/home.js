document.addEventListener('DOMContentLoaded', function () {
    var carousel = document.querySelector('[data-course-carousel]');
    if (!carousel) return;

    var track = carousel.querySelector('[data-course-track]');
    var cards = Array.prototype.slice.call(track.querySelectorAll('.course-card'));
    var prevBtn = carousel.querySelector('[data-carousel-prev]');
    var nextBtn = carousel.querySelector('[data-carousel-next]');
    if (!cards.length) return;

    var index = 0;
    var autoTimer = null;
    var gap = 26;

    function cardsPerView() {
        var w = window.innerWidth;
        if (w <= 600) return 1;
        if (w <= 1024) return 2;
        return 3;
    }

    function maxIndex() {
        return Math.max(0, cards.length - cardsPerView());
    }

    function update() {
        var perView = cardsPerView();
        var cardWidth = cards[0].getBoundingClientRect().width;
        var offset = index * (cardWidth + gap);
        track.style.transform = 'translateX(-' + offset + 'px)';

        var disableArrows = cards.length <= perView;
        if (prevBtn) prevBtn.style.display = disableArrows ? 'none' : 'flex';
        if (nextBtn) nextBtn.style.display = disableArrows ? 'none' : 'flex';
    }

    function goNext() {
        index = index >= maxIndex() ? 0 : index + 1;
        update();
    }

    function goPrev() {
        index = index <= 0 ? maxIndex() : index - 1;
        update();
    }

    function startAuto() {
        stopAuto();
        if (cards.length > cardsPerView()) {
            autoTimer = setInterval(goNext, 4000);
        }
    }

    function stopAuto() {
        if (autoTimer) { clearInterval(autoTimer); autoTimer = null; }
    }

    if (nextBtn) nextBtn.addEventListener('click', function () { goNext(); startAuto(); });
    if (prevBtn) prevBtn.addEventListener('click', function () { goPrev(); startAuto(); });

    carousel.addEventListener('mouseenter', stopAuto);
    carousel.addEventListener('mouseleave', startAuto);

    var resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            index = Math.min(index, maxIndex());
            update();
        }, 150);
    });

    update();
    startAuto();
});

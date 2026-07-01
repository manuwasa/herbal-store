// Public site interactivity — plain vanilla JS, no framework, no CDN.
document.addEventListener('DOMContentLoaded', function () {
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // --- Hero entrance -----------------------------------------------------
    // Hero content is always above the fold, so activate immediately on load
    // (via requestAnimationFrame, so the browser paints the hidden state first
    // and the transition actually plays) rather than waiting on a scroll observer.
    document.querySelectorAll('.hero-reveal').forEach(function (el) {
        requestAnimationFrame(function () {
            el.classList.add('is-visible');
        });
    });

    // --- Scroll-reveal (sections + cards below the fold) -------------------
    var revealTargets = document.querySelectorAll('.reveal');
    var topPickSlider = document.getElementById('top-pick-slider');

    function markVisible(el) { el.classList.add('is-visible'); }

    if (!reduceMotion && 'IntersectionObserver' in window) {
        var revealObserver = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    markVisible(entry.target);
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

        revealTargets.forEach(function (el) {
            // Cards further along the horizontally-scrolling Top Pick slider can
            // sit outside the page's horizontal viewport even once their row has
            // scrolled into view vertically, so per-card observation can miss
            // them entirely if the slider is never scrolled. Reveal those as a
            // group via the slider container's own visibility instead.
            if (topPickSlider && topPickSlider.contains(el)) return;
            revealObserver.observe(el);
        });

        if (topPickSlider) {
            new IntersectionObserver(function (entries, obs) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        topPickSlider.querySelectorAll('.reveal').forEach(markVisible);
                        obs.disconnect();
                    }
                });
            }, { threshold: 0.15 }).observe(topPickSlider);
        }
    } else {
        revealTargets.forEach(markVisible);
    }

    // --- Navbar shrink-on-scroll ---------------------------------------------
    var header = document.getElementById('site-header');
    var headerInner = document.getElementById('site-header-inner');
    if (header && headerInner) {
        var scrollThreshold = 24;
        var updateHeaderState = function () {
            var scrolled = window.scrollY > scrollThreshold;
            headerInner.classList.toggle('py-2', scrolled);
            headerInner.classList.toggle('py-4', !scrolled);
            header.classList.toggle('shadow-sm', scrolled);
            header.classList.toggle('shadow-stone-900/5', scrolled);
        };
        window.addEventListener('scroll', updateHeaderState, { passive: true });
        updateHeaderState();
    }

    // --- Top Pick slider -----------------------------------------------------
    var slider = document.getElementById('top-pick-slider');
    var prevBtn = document.getElementById('top-pick-prev');
    var nextBtn = document.getElementById('top-pick-next');

    if (!slider) return;

    // Threshold wider than 0 because the slider's own side padding means
    // scrollLeft starts a little above 0 even when fully scrolled left.
    var edgeThreshold = 30;

    function isAtStart() {
        return slider.scrollLeft <= edgeThreshold;
    }

    function isAtEnd() {
        var maxScroll = slider.scrollWidth - slider.clientWidth;
        return slider.scrollLeft >= maxScroll - edgeThreshold;
    }

    function scrollByCard(direction) {
        var card = slider.querySelector(':scope > *');
        var gap = 20;
        var amount = card ? card.getBoundingClientRect().width + gap : 300;
        slider.scrollBy({ left: direction * amount, behavior: 'smooth' });
    }

    function updateButtons() {
        if (!prevBtn || !nextBtn) return;
        prevBtn.disabled = isAtStart();
        nextBtn.disabled = isAtEnd();
    }

    if (prevBtn) prevBtn.addEventListener('click', function () { scrollByCard(-1); });
    if (nextBtn) nextBtn.addEventListener('click', function () { scrollByCard(1); });

    slider.addEventListener('scroll', updateButtons);
    window.addEventListener('resize', updateButtons);
    updateButtons();

    // Auto-advance: makes the slider feel alive without requiring a click.
    // Pauses on hover, touch, click, and keyboard focus, and never runs at all
    // under reduced motion (auto-scrolling carousels must stay pausable/off —
    // WCAG 2.2.2 Pause, Stop, Hide).
    var autoAdvanceMs = 5000;
    var autoAdvanceTimer = null;
    var userInteracting = false;
    var interactionTimeout = null;

    function autoAdvance() {
        if (userInteracting) return;
        if (isAtEnd()) {
            slider.scrollTo({ left: 0, behavior: 'smooth' });
        } else {
            scrollByCard(1);
        }
    }

    function stopAutoAdvance() {
        if (autoAdvanceTimer) {
            window.clearInterval(autoAdvanceTimer);
            autoAdvanceTimer = null;
        }
    }

    function startAutoAdvance() {
        if (reduceMotion) return;
        stopAutoAdvance();
        autoAdvanceTimer = window.setInterval(autoAdvance, autoAdvanceMs);
    }

    function markInteracting(active) {
        userInteracting = active;
        if (active) {
            window.clearTimeout(interactionTimeout);
        }
    }

    function markTemporaryInteraction() {
        userInteracting = true;
        window.clearTimeout(interactionTimeout);
        interactionTimeout = window.setTimeout(function () { userInteracting = false; }, 4000);
    }

    slider.addEventListener('mouseenter', function () { markInteracting(true); });
    slider.addEventListener('mouseleave', function () { markInteracting(false); });
    slider.addEventListener('touchstart', markTemporaryInteraction, { passive: true });
    slider.addEventListener('pointerdown', markTemporaryInteraction);
    slider.addEventListener('focusin', function () { markInteracting(true); });
    slider.addEventListener('focusout', function () { markInteracting(false); });
    if (prevBtn) prevBtn.addEventListener('click', markTemporaryInteraction);
    if (nextBtn) nextBtn.addEventListener('click', markTemporaryInteraction);

    if (!reduceMotion && 'IntersectionObserver' in window) {
        new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    startAutoAdvance();
                } else {
                    stopAutoAdvance();
                }
            });
        }, { threshold: 0.4 }).observe(slider);
    } else if (!reduceMotion) {
        startAutoAdvance();
    }
});

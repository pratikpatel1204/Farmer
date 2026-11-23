/* =========================================================
   KisanMandi Home: auto-scroll slider + profile filter
   Kisi library ki zarurat nahi. Markup:
     <div class="auto-slider" data-autoplay="3000">
        <div class="slider-track"> <div class="slider-item">...</div> ... </div>
     </div>
     Arrow buttons: data-slider-prev="#id" / data-slider-next="#id"
   ========================================================= */
(function () {
    'use strict';

    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function initSlider(slider) {
        var track = slider.querySelector('.slider-track');
        if (!track) return;

        var delay = parseInt(slider.getAttribute('data-autoplay'), 10) || 3000;
        var timer = null;
        var paused = false;

        function step() {
            var item = track.querySelector('.slider-item:not(.d-none)');
            if (!item) return 0;
            var gap = parseFloat(getComputedStyle(track).columnGap) || 16;
            return item.getBoundingClientRect().width + gap;
        }

        function atEnd() {
            return track.scrollLeft + track.clientWidth >= track.scrollWidth - 4;
        }

        function next() {
            if (atEnd()) {
                track.scrollTo({ left: 0 });
            } else {
                track.scrollBy({ left: step() });
            }
        }

        function prev() {
            if (track.scrollLeft <= 4) {
                track.scrollTo({ left: track.scrollWidth });
            } else {
                track.scrollBy({ left: -step() });
            }
        }

        function play() {
            stop();
            if (reduceMotion || paused) return;
            timer = setInterval(function () {
                if (!document.hidden) next();
            }, delay);
        }

        function stop() {
            if (timer) clearInterval(timer);
            timer = null;
        }

        // Mouse upar ho, ya touch / focus ho to ruk jaye
        slider.addEventListener('mouseenter', function () { paused = true; stop(); });
        slider.addEventListener('mouseleave', function () { paused = false; play(); });
        slider.addEventListener('focusin', function () { paused = true; stop(); });
        slider.addEventListener('focusout', function () { paused = false; play(); });
        track.addEventListener('touchstart', function () { stop(); }, { passive: true });
        track.addEventListener('touchend', function () { setTimeout(play, 2500); }, { passive: true });

        if (slider.id) {
            document.querySelectorAll('[data-slider-next="#' + slider.id + '"]').forEach(function (btn) {
                btn.addEventListener('click', function () { next(); play(); });
            });
            document.querySelectorAll('[data-slider-prev="#' + slider.id + '"]').forEach(function (btn) {
                btn.addEventListener('click', function () { prev(); play(); });
            });
        }

        slider.kmSlider = { next: next, prev: prev, play: play, stop: stop, track: track };
        play();
    }

    function initProfileFilter() {
        var filter = document.querySelector('[data-profile-filter]');
        if (!filter) return;
        var slider = document.querySelector(filter.getAttribute('data-profile-filter'));

        filter.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-role]');
            if (!btn || !slider) return;

            filter.querySelectorAll('[data-role]').forEach(function (b) {
                b.classList.toggle('active', b === btn);
                b.setAttribute('aria-pressed', b === btn ? 'true' : 'false');
            });

            var role = btn.getAttribute('data-role');
            slider.querySelectorAll('.slider-item').forEach(function (item) {
                var show = role === 'all' || item.getAttribute('data-role') === role;
                item.classList.toggle('d-none', !show);
            });
            if (slider.kmSlider) slider.kmSlider.track.scrollTo({ left: 0 });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.auto-slider').forEach(initSlider);
        initProfileFilter();
    });
})();

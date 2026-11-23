/* =========================================================
   KisanMandi Inner Pages: chhote helpers (koi library nahi)
     [data-auto-submit]      : select badalte hi form submit (Sort by)
     [data-gallery-thumb]    : product photo thumbnail click -> badi photo
     [data-password-toggle]  : password dikhao / chhupao
     [data-copy-link]        : page ka link copy
   ========================================================= */
(function () {
    'use strict';

    function onReady(fn) {
        if (document.readyState !== 'loading') fn();
        else document.addEventListener('DOMContentLoaded', fn);
    }

    onReady(function () {
        // Sort by: select badalte hi submit
        document.querySelectorAll('[data-auto-submit]').forEach(function (select) {
            select.addEventListener('change', function () {
                if (select.form) select.form.submit();
            });
        });

        // Product gallery
        document.querySelectorAll('[data-gallery-thumb]').forEach(function (thumb) {
            thumb.addEventListener('click', function () {
                var main = document.querySelector(thumb.getAttribute('data-gallery-thumb'));
                if (!main) return;
                main.src = thumb.getAttribute('data-src');
                thumb.parentNode.querySelectorAll('[data-gallery-thumb]').forEach(function (t) {
                    t.classList.toggle('active', t === thumb);
                    t.setAttribute('aria-pressed', t === thumb ? 'true' : 'false');
                });
            });
        });

        // Password show / hide
        document.querySelectorAll('[data-password-toggle]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.querySelector(btn.getAttribute('data-password-toggle'));
                if (!input) return;
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                var icon = btn.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye', !show);
                    icon.classList.toggle('fa-eye-slash', show);
                }
            });
        });

        // Copy link
        document.querySelectorAll('[data-copy-link]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var url = btn.getAttribute('data-copy-link') || window.location.href;
                var done = function () {
                    var old = btn.innerHTML;
                    btn.innerHTML = '<i class="fa-solid fa-check"></i>';
                    btn.setAttribute('title', 'Link copied');
                    setTimeout(function () { btn.innerHTML = old; btn.setAttribute('title', 'Copy link'); }, 1800);
                };
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(url).then(done, function () { window.prompt('Copy this link:', url); });
                } else {
                    window.prompt('Copy this link:', url);
                }
            });
        });
    });
})();

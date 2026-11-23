/* KisanMandi - Survey
   1) Home hero: jitne question card ki height me pure aa sakein utne dikhao (2-3), baaki chhupao
   2) Survey page: kitne answer hue (progress), khali question par submit roko, "Change my answers" button */
(function () {
    'use strict';

    /* ---------- 1) Hero: height ke hisab se question ---------- */
    function fitHeroSurvey() {
        document.querySelectorAll('[data-survey-fit]').forEach(function (list) {
            var items = Array.prototype.slice.call(list.querySelectorAll('[data-survey-item]'));
            items.forEach(function (item) { item.hidden = false; });

            // Mobile par CSS 2 tak rakhta hai, height fix nahi hoti
            if (window.innerWidth < 768) return;

            for (var i = items.length - 1; i > 0; i--) {
                if (list.scrollHeight <= list.clientHeight + 1) break;
                items[i].hidden = true;
            }
        });
    }

    var resizeTimer;
    if (document.querySelector('[data-survey-fit]')) {
        fitHeroSurvey();
        window.addEventListener('load', fitHeroSurvey);
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(fitHeroSurvey, 120);
        });
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(fitHeroSurvey);
        }
    }

    /* ---------- 2) Survey page ---------- */
    var form = document.querySelector('[data-survey-form]');
    if (!form) return;

    var questions = Array.prototype.slice.call(form.querySelectorAll('[data-survey-question]'));
    var progressText = form.querySelector('[data-survey-progress-text]');
    var progressFill = form.querySelector('[data-survey-progress-fill]');

    function isAnswered(q) {
        return !!q.querySelector('.survey-option-input:checked');
    }

    function updateProgress() {
        var done = questions.filter(isAnswered).length;
        if (progressText) progressText.textContent = done + ' of ' + questions.length + ' answered';
        if (progressFill) progressFill.style.width = (questions.length ? done * 100 / questions.length : 0) + '%';
    }

    form.addEventListener('change', function (e) {
        if (!e.target.classList.contains('survey-option-input')) return;
        var q = e.target.closest('[data-survey-question]');
        // :has() na chalne wale purane browser ke liye class
        q.querySelectorAll('.survey-option').forEach(function (opt) {
            opt.classList.toggle('is-checked', opt.contains(e.target));
        });
        q.classList.remove('is-missing');
        var err = q.querySelector('[data-survey-error]');
        if (err) err.hidden = true;
        updateProgress();
    });

    form.addEventListener('submit', function (e) {
        var missing = questions.filter(function (q) { return !isAnswered(q); });
        if (!missing.length) {
            var btn = form.querySelector('[data-survey-submit]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>Saving...';
            }
            return;
        }
        e.preventDefault();
        missing.forEach(function (q) {
            q.classList.add('is-missing');
            var err = q.querySelector('[data-survey-error]');
            if (err) {
                err.innerHTML = '<i class="fa-solid fa-circle-exclamation me-1"></i>Please select an answer.';
                err.hidden = false;
            }
        });
        missing[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    // "Change my answers" (24 ghante ke andar)
    var editBtn = document.querySelector('[data-survey-edit]');
    if (editBtn) {
        editBtn.addEventListener('click', function () {
            form.hidden = false;
            editBtn.hidden = true;
            editBtn.setAttribute('aria-expanded', 'true');
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }

    // Home se kisi question par click karke aaye (#question-12) to us par halka highlight
    if (location.hash && /^#question-\d+$/.test(location.hash)) {
        var target = document.querySelector(location.hash);
        if (target) target.classList.add('is-focus');
    }

    updateProgress();
})();

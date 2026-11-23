// ---------- Cookie helpers ----------
function clearTransCookie() {
    var h = location.hostname;
    var parts = h.split('.');
    var domains = ['', h, '.' + h];
    if (parts.length > 2) domains.push('.' + parts.slice(-2).join('.'));

    domains.forEach(function (d) {
        document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/' +
            (d ? '; domain=' + d : '');
    });
}

function setTransCookie(lang) {
    clearTransCookie();
    if (lang === 'en') return;               // English = koi cookie nahi
    document.cookie = 'googtrans=/en/' + lang + '; path=/';
    if (location.hostname !== 'localhost') {
        document.cookie = 'googtrans=/en/' + lang + '; path=/; domain=' + location.hostname;
    }
}

function getCurrentLang() {
    var m = document.cookie.match(/googtrans=\/en\/(\w+)/);
    return m ? m[1] : 'en';
}

// ---------- Default Hindi (sirf pehli visit par) ----------
try {
    if (!localStorage.getItem('lang_chosen') && !document.cookie.includes('googtrans=')) {
        setTransCookie('hi');
        localStorage.setItem('lang_chosen', '1');
    }
} catch (e) {}

// ---------- Google engine (hidden) ----------
function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'en',
        includedLanguages: 'hi,gu,mr',
        autoDisplay: false
    }, 'google_translate_element');
}

// ---------- Apne dropdown se call hoga ----------
function changeLanguage(lang) {
    try { localStorage.setItem('lang_chosen', '1'); } catch (e) {}
    setTransCookie(lang);
    location.reload();
}

document.addEventListener('DOMContentLoaded', function () {
    var sel = document.getElementById('my-lang-select');
    if (sel) sel.value = getCurrentLang();
});

// ---------- Google script load ----------
var s = document.createElement('script');
s.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
document.head.appendChild(s);
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">

    <title>@yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('front/image/favicon.png') }}" sizes="32x32">
    <script>
        (function() {
            var html = document.documentElement;
            var m = document.cookie.match(/googtrans=\/en\/(\w+)/);
            var firstVisit = false;
            try {
                firstVisit = !localStorage.getItem('lang_chosen') && !m;
            } catch (e) {}

            if (firstVisit || (m && m[1] !== 'en')) {
                // Loader me kis bhasha ka message dikhe
                var lang = m ? m[1] : 'hi';
                html.setAttribute('data-loading-lang', lang);
                html.classList.add('translating');

                function reveal() {
                    html.classList.remove('translating');
                }

                new MutationObserver(function() {
                    if (/translated-(ltr|rtl)/.test(html.className)) {
                        setTimeout(reveal, 150);
                    }
                }).observe(html, {
                    attributes: true,
                    attributeFilter: ['class']
                });

                // Safety: Google slow ya block ho to bhi page dikha do
                setTimeout(reveal, 2000);
            }
        })();
    </script>
    {{-- Translate script (defer = DOM ready hone ke baad chalegi) --}}
    <script src="{{ asset('front/js/translate.js') }}" defer></script>

    {{-- Sab files offline (public/front) se --}}
    <link rel="stylesheet" href="{{ asset('front/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('front/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/toastr.min.css') }}">
    @stack('styles')
</head>

<body>
    @include('front.layout.header')
    <main class="content-wrapper">
        @yield('content')
    </main>
    @include('front.layout.footer')
    <script src="{{ asset('front/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('front/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('front/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('front/js/toastr.min.js') }}"></script>
    @stack('scripts')
</body>

</html>
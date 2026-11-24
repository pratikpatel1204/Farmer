@php
    $headerCategories = \App\Models\Category::orderBy('id')->get(['id', 'name', 'image']);
    $headerDotColors = ['green', 'red', 'yellow', 'lime', 'brown', 'dark-green'];
    $headerLanguages = [
        'en' => ['English', 'English', 'EN'],
        'hi' => ['हिंदी', 'Hindi', 'हि'],
        'gu' => ['ગુજરાતી', 'Gujarati', 'ગુ'],
        'mr' => ['मराठी', 'Marathi', 'म'],
    ];
@endphp
<header class="site-header sticky-top">
    <div class="field-stripe" aria-hidden="true"></div>
    <div class="top-bar">
        <div class="container-xl d-flex align-items-center flex-wrap flex-md-nowrap gap-2 gap-md-3 gap-xl-4 py-2">
            <button class="btn header-icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#mainMenu" aria-controls="mainMenu" aria-label="Open menu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <a href="{{ url('/') }}" class="brand-logo d-flex align-items-center gap-2 me-auto me-md-0"
                aria-label="KisanMandi home">
                <img src="{{ asset('front/image/logo.png') }}" alt="KisanMandi" class="brand-logo-img"
                    style="height: 50px; width: auto; max-width: 200px; object-fit: contain;">
            </a>
            <form class="header-search" role="search" action="{{ url('search') }}" method="GET">
                <div class="input-group">
                    <input class="form-control search-input" type="search" name="q" value="{{ request('q') }}"
                        placeholder="Search Items, Services, Profiles....." aria-label="Search">
                    <button class="btn search-btn" type="submit" aria-label="Search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>

            {{-- ===== Language dropdown (Google Translate) : desktop + mobile dono par =====
                 notranslate + translate="no" : bhasha ke naam khud translate na hon --}}
            <div class="lang-switch dropdown notranslate" translate="no">
                <button class="btn lang-btn" type="button" data-bs-toggle="dropdown" data-bs-display="static"
                    aria-expanded="false" aria-label="Change language">
                    <i class="fa-solid fa-language lang-btn-icon" aria-hidden="true"></i>
                    <span class="lang-btn-short" data-lang-short>EN</span>
                    <span class="lang-btn-name" data-lang-name>English</span>
                    <i class="fa-solid fa-chevron-down lang-btn-caret" aria-hidden="true"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end lang-menu">
                    <li class="lang-menu-head"><i class="fa-solid fa-earth-asia"></i>Choose your language</li>
                    @foreach ($headerLanguages as $code => [$native, $english, $short])
                        <li>
                            <button type="button" class="dropdown-item lang-option" data-lang="{{ $code }}"
                                data-short="{{ $short }}" data-name="{{ $native }}"
                                onclick="changeLanguage('{{ $code }}')" translate="no">
                                <span class="lang-badge">{{ $short }}</span>
                                <span class="lang-names">
                                    <strong>{{ $native }}</strong>
                                    <small>{{ $english }}</small>
                                </span>
                                <i class="fa-solid fa-circle-check lang-check" aria-hidden="true"></i>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
            {{-- Google ka asli widget (chhupa hua, sirf engine ke liye) --}}
            <div id="google_translate_element" style="position:absolute; left:-9999px;"></div>
            <script>
                // Abhi kaunsi bhasha hai: button ka label + menu me tick (translate.js wala hi cookie)
                (function () {
                    var m = document.cookie.match(/googtrans=\/en\/(\w+)/);
                    var current = m ? m[1] : 'en';
                    try {
                        // Pehli visit par translate.js Hindi lagata hai
                        if (!m && !localStorage.getItem('lang_chosen')) current = 'hi';
                    } catch (e) {}

                    var options = document.querySelectorAll('.lang-option');
                    options.forEach(function (opt) {
                        var on = opt.getAttribute('data-lang') === current;
                        opt.classList.toggle('active', on);
                        opt.setAttribute('aria-current', on ? 'true' : 'false');
                        if (on) {
                            document.querySelectorAll('[data-lang-short]').forEach(function (el) { el.textContent = opt.getAttribute('data-short'); });
                            document.querySelectorAll('[data-lang-name]').forEach(function (el) { el.textContent = opt.getAttribute('data-name'); });
                        }
                    });
                })();
            </script>

            <div class="header-account d-none d-md-flex align-items-center gap-2">
                @guest('web')
                    <a href="{{ url('login') }}" class="btn btn-login">
                        <i class="fa-solid fa-user me-1"></i>Login
                    </a>
                    <a href="{{ url('register') }}" class="btn btn-register">
                        <i class="fa-solid fa-user-plus me-1"></i>Register
                    </a>
                @else
                    <a href="{{ route('member.dashboard') }}" class="btn btn-login" target="_blank">
                        <i class="fa-solid fa-circle-user me-1"></i>My Account
                    </a>
                    <a href="{{ route('logout') }}" class="btn btn-register">
                        <i class="fa-solid fa-right-from-bracket me-1"></i>Logout
                    </a>
                @endguest
            </div>
            @guest('web')
                <a href="{{ url('login') }}" class="btn header-icon-btn d-md-none" aria-label="Login">
                    <i class="fa-solid fa-circle-user"></i>
                </a>
            @else
                <a href="{{ route('member.dashboard') }}" class="btn header-icon-btn d-md-none" target="_blank"
                    aria-label="My Account">
                    <i class="fa-solid fa-user"></i>
                </a>
            @endguest
        </div>
    </div>
    <nav class="main-nav navbar navbar-expand-lg p-0" aria-label="Main menu">
        <div class="container-xl">
            <div class="offcanvas offcanvas-start" tabindex="-1" id="mainMenu" aria-labelledby="mainMenuLabel">
                <div class="offcanvas-header main-nav-drawer-head">
                    <h5 class="offcanvas-title brand-name" id="mainMenuLabel">
                        <span class="brand-name-first">Kisan</span>
                        <span class="brand-name-second">Mandi</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="Close menu">
                    </button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav align-items-lg-center gap-1">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                                <i class="fa-solid fa-house nav-icon"></i>Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('about*') ? 'active' : '' }}"
                                href="{{ url('about') }}">
                                <i class="fa-solid fa-circle-info nav-icon"></i>About
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-cta nav-cta-want {{ request()->is('i-want*') ? 'active' : '' }}"
                                href="{{ url('i-want') }}">
                                <i class="fa-solid fa-cart-shopping nav-cta-icon"></i>People Want
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-cta nav-cta-have {{ request()->is('i-have*') ? 'active' : '' }}"
                                href="{{ url('i-have') }}">
                                <i class="fa-solid fa-seedling nav-cta-icon"></i>People Have
                            </a>
                        </li>
                        <li class="nav-item dropdown category-dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->is('category*') ? 'active' : '' }}"
                                href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-table-cells-large nav-icon"></i>Category
                            </a>
                            <ul class="dropdown-menu category-menu">
                                @forelse ($headerCategories as $cat)
                                    <li>
                                        <a class="dropdown-item {{ request()->is('category/' . $cat->slug) ? 'active' : '' }}"
                                            href="{{ url('category/' . $cat->slug) }}">
                                            @if ($cat->image)
                                                <img src="{{ asset($cat->image) }}" alt="" loading="lazy"
                                                    style="width: 26px; height: 26px; border-radius: 50%; object-fit: cover; flex-shrink: 0;">
                                            @else
                                                <span
                                                    class="category-dot dot-{{ $headerDotColors[$loop->index % count($headerDotColors)] }}"></span>
                                            @endif
                                            {{ $cat->name }}
                                        </a>
                                    </li>
                                @empty
                                    <li><span class="dropdown-item disabled">No category yet</span></li>
                                @endforelse
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('contact-us*') ? 'active' : '' }}"
                                href="{{ url('contact-us') }}">
                                <i class="fa-solid fa-phone nav-icon"></i>Contact Us
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('blog*') ? 'active' : '' }}"
                                href="{{ url('blog') }}">
                                <i class="fa-solid fa-newspaper nav-icon"></i>Blog
                            </a>
                        </li>
                    </ul>
                    <div class="drawer-account d-flex d-md-none gap-2 mt-3">
                        @guest('web')
                            <a href="{{ url('login') }}" class="btn btn-login flex-fill">Login</a>
                            <a href="{{ url('register') }}" class="btn btn-register flex-fill">Register</a>
                        @else
                            <a href="{{ route('member.dashboard') }}" class="btn btn-login flex-fill">My Account</a>
                            <a href="{{ route('logout') }}" class="btn btn-register flex-fill">Logout</a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
<?php
    $headerCategories = \App\Models\Category::orderBy('id')->get(['id', 'name', 'image']);
    $headerDotColors = ['green', 'red', 'yellow', 'lime', 'brown', 'dark-green'];
    $headerLanguages = [
        'en' => ['English', 'English', 'EN'],
        'hi' => ['हिंदी', 'Hindi', 'हि'],
        'gu' => ['ગુજરાતી', 'Gujarati', 'ગુ'],
        'mr' => ['मराठी', 'Marathi', 'म'],
    ];
?>
<header class="site-header sticky-top">
    <div class="field-stripe" aria-hidden="true"></div>
    <div class="top-bar">
        <div class="container-xl d-flex align-items-center flex-wrap flex-md-nowrap gap-2 gap-md-3 gap-xl-4 py-2">
            <button class="btn header-icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#mainMenu" aria-controls="mainMenu" aria-label="Open menu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <a href="<?php echo e(url('/')); ?>" class="brand-logo d-flex align-items-center gap-2 me-auto me-md-0"
                aria-label="KisanMandi home">
                <img src="<?php echo e(asset('front/image/logo.png')); ?>" alt="KisanMandi" class="brand-logo-img"
                    style="height: 50px; width: auto; max-width: 200px; object-fit: contain;">
            </a>
            <form class="header-search" role="search" action="<?php echo e(url('search')); ?>" method="GET">
                <div class="input-group">
                    <input class="form-control search-input" type="search" name="q" value="<?php echo e(request('q')); ?>"
                        placeholder="Search Items, Services, Profiles....." aria-label="Search">
                    <button class="btn search-btn" type="submit" aria-label="Search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>

            
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
                    <?php $__currentLoopData = $headerLanguages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => [$native, $english, $short]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <button type="button" class="dropdown-item lang-option" data-lang="<?php echo e($code); ?>"
                                data-short="<?php echo e($short); ?>" data-name="<?php echo e($native); ?>"
                                onclick="changeLanguage('<?php echo e($code); ?>')" translate="no">
                                <span class="lang-badge"><?php echo e($short); ?></span>
                                <span class="lang-names">
                                    <strong><?php echo e($native); ?></strong>
                                    <small><?php echo e($english); ?></small>
                                </span>
                                <i class="fa-solid fa-circle-check lang-check" aria-hidden="true"></i>
                            </button>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            
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
                <?php if(auth()->guard('web')->guest()): ?>
                    <a href="<?php echo e(url('login')); ?>" class="btn btn-login">
                        <i class="fa-solid fa-user me-1"></i>Login
                    </a>
                    <a href="<?php echo e(url('register')); ?>" class="btn btn-register">
                        <i class="fa-solid fa-user-plus me-1"></i>Register
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('member.dashboard')); ?>" class="btn btn-login" target="_blank">
                        <i class="fa-solid fa-circle-user me-1"></i>My Account
                    </a>
                    <a href="<?php echo e(route('logout')); ?>" class="btn btn-register">
                        <i class="fa-solid fa-right-from-bracket me-1"></i>Logout
                    </a>
                <?php endif; ?>
            </div>
            <?php if(auth()->guard('web')->guest()): ?>
                <a href="<?php echo e(url('login')); ?>" class="btn header-icon-btn d-md-none" aria-label="Login">
                    <i class="fa-solid fa-circle-user"></i>
                </a>
            <?php else: ?>
                <a href="<?php echo e(route('member.dashboard')); ?>" class="btn header-icon-btn d-md-none" target="_blank"
                    aria-label="My Account">
                    <i class="fa-solid fa-user"></i>
                </a>
            <?php endif; ?>
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
                            <a class="nav-link <?php echo e(request()->is('/') ? 'active' : ''); ?>" href="<?php echo e(url('/')); ?>">
                                <i class="fa-solid fa-house nav-icon"></i>Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->is('about*') ? 'active' : ''); ?>"
                                href="<?php echo e(url('about')); ?>">
                                <i class="fa-solid fa-circle-info nav-icon"></i>About
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-cta nav-cta-want <?php echo e(request()->is('i-want*') ? 'active' : ''); ?>"
                                href="<?php echo e(url('i-want')); ?>">
                                <i class="fa-solid fa-cart-shopping nav-cta-icon"></i>I Want
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-cta nav-cta-have <?php echo e(request()->is('i-have*') ? 'active' : ''); ?>"
                                href="<?php echo e(url('i-have')); ?>">
                                <i class="fa-solid fa-seedling nav-cta-icon"></i>I Have
                            </a>
                        </li>
                        <li class="nav-item dropdown category-dropdown">
                            <a class="nav-link dropdown-toggle <?php echo e(request()->is('category*') ? 'active' : ''); ?>"
                                href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-table-cells-large nav-icon"></i>Category
                            </a>
                            <ul class="dropdown-menu category-menu">
                                <?php $__empty_1 = true; $__currentLoopData = $headerCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <li>
                                        <a class="dropdown-item <?php echo e(request()->is('category/' . $cat->slug) ? 'active' : ''); ?>"
                                            href="<?php echo e(url('category/' . $cat->slug)); ?>">
                                            <?php if($cat->image): ?>
                                                <img src="<?php echo e(asset($cat->image)); ?>" alt="" loading="lazy"
                                                    style="width: 26px; height: 26px; border-radius: 50%; object-fit: cover; flex-shrink: 0;">
                                            <?php else: ?>
                                                <span
                                                    class="category-dot dot-<?php echo e($headerDotColors[$loop->index % count($headerDotColors)]); ?>"></span>
                                            <?php endif; ?>
                                            <?php echo e($cat->name); ?>

                                        </a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <li><span class="dropdown-item disabled">No category yet</span></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->is('contact-us*') ? 'active' : ''); ?>"
                                href="<?php echo e(url('contact-us')); ?>">
                                <i class="fa-solid fa-phone nav-icon"></i>Contact Us
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->is('blog*') ? 'active' : ''); ?>"
                                href="<?php echo e(url('blog')); ?>">
                                <i class="fa-solid fa-newspaper nav-icon"></i>Blog
                            </a>
                        </li>
                    </ul>
                    <div class="drawer-account d-flex d-md-none gap-2 mt-3">
                        <?php if(auth()->guard('web')->guest()): ?>
                            <a href="<?php echo e(url('login')); ?>" class="btn btn-login flex-fill">Login</a>
                            <a href="<?php echo e(url('register')); ?>" class="btn btn-register flex-fill">Register</a>
                        <?php else: ?>
                            <a href="<?php echo e(route('member.dashboard')); ?>" class="btn btn-login flex-fill">My Account</a>
                            <a href="<?php echo e(route('logout')); ?>" class="btn btn-register flex-fill">Logout</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/front/layout/header.blade.php ENDPATH**/ ?>
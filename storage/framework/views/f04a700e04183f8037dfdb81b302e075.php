<header class="zentic-topbar">
    <div class="topbar-left">
        <a href="<?php echo e(route('member.dashboard')); ?>" class="brand-link d-lg-none d-block">
            <div class="brand-icon">
                <img src="<?php echo e(asset('member/img/logo.png')); ?>" alt="<?php echo e(config('app.name')); ?>" style="height: 30px;">
            </div>
        </a>
        <button class="sidebar-toggle-btn" id="sidebarToggle">
            <i class="icon-menu"></i>
        </button>
    </div>
    <div class="topbar-right">
        <div id="google_translate_element" style="position:absolute; left:-9999px;"></div>
        <div class="lang-switcher notranslate" translate="no">
            <button class="lang-btn" type="button" title="Change language">
                <i class="icon-globe lang-flag-btn" id="langBtnIcon"></i>
                <span class="lang-label" id="langBtnLabel">EN</span>
                <i class="icon-chevron-down lang-arrow"></i>
            </button>
            <div class="lang-dropdown">
                <div class="lang-dropdown-header">
                    <i class="icon-globe"></i><span>Select Language</span>
                </div>
                <div class="lang-search-wrap">
                    <i class="icon-search"></i>
                    <input type="text" class="lang-search" placeholder="Search language…" autocomplete="off">
                </div>
                <div class="lang-list">
                    <div class="lang-item" data-lang="en" data-name="English" data-abbr="EN">
                        <i class="icon-globe lang-flag"></i>
                        <div class="lang-info">
                            <span class="lang-name">English</span>
                            <span class="lang-region">English</span>
                        </div>
                        <i class="icon-circle-check lang-check"></i>
                    </div>
                    <div class="lang-item" data-lang="hi" data-name="हिन्दी" data-abbr="HI">
                        <i class="icon-globe lang-flag"></i>
                        <div class="lang-info">
                            <span class="lang-name">हिन्दी</span>
                            <span class="lang-region">India</span>
                        </div>
                        <i class="icon-circle-check lang-check"></i>
                    </div>
                    <div class="lang-item" data-lang="gu" data-name="ગુજરાતી" data-abbr="GU">
                        <i class="icon-globe lang-flag"></i>
                        <div class="lang-info">
                            <span class="lang-name">ગુજરાતી</span>
                            <span class="lang-region">India</span>
                        </div>
                        <i class="icon-circle-check lang-check"></i>
                    </div>
                    <div class="lang-item" data-lang="mr" data-name="मराठी" data-abbr="MR">
                        <i class="icon-globe lang-flag"></i>
                        <div class="lang-info">
                            <span class="lang-name">मराठी</span>
                            <span class="lang-region">India</span>
                        </div>
                        <i class="icon-circle-check lang-check"></i>
                    </div>
                </div>
                <div class="lang-no-results"><i class="icon-circle-alert"></i><span>No language found</span></div>
            </div>
        </div>
        <div class="topbar-divider"></div>

        
        <?php echo $__env->make('member.layout.notification-bell', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="topbar-divider"></div>
        <div class="topbar-user position-relative">
            <?php if(auth()->user()->profile_image): ?>
                <img class="avatar-gen-img topbar-user-avatar" src="<?php echo e(asset(auth()->user()->profile_image)); ?>"
                    data-size="36" alt="<?php echo e(auth()->user()->full_name); ?>" />
            <?php else: ?>
                <img class="avatar-gen-img topbar-user-avatar" src="<?php echo e(asset('member/img/avatar/avatar.jpg')); ?>"
                    data-size="36" alt="<?php echo e(auth()->user()->full_name); ?>" />
            <?php endif; ?>
            <div class="user-dropdown-menu">
                <div class="user-dropdown-header">
                    <?php if(auth()->user()->profile_image): ?>
                        <img class="avatar-gen-img udm-avatar" src="<?php echo e(asset(auth()->user()->profile_image)); ?>"
                            data-size="40" alt="<?php echo e(auth()->user()->full_name); ?>">
                    <?php else: ?>
                        <img class="avatar-gen-img udm-avatar" src="<?php echo e(asset('member/img/avatar/avatar.jpg')); ?>"
                            data-size="40" alt="<?php echo e(auth()->user()->full_name); ?>">
                    <?php endif; ?>
                    <div class="udm-info">
                        <p class="user-dropdown-name"><?php echo e(auth()->user()->full_name); ?></p>
                        <p class="user-dropdown-email"><?php echo e(auth()->user()->unique_id); ?></p>
                    </div>
                </div>
                <div class="udm-body">
                    <a href="<?php echo e(route('member.profile')); ?>" class="user-dropdown-item">
                        <i class="icon-circle-user"></i><span>My Profile</span>
                    </a>
                    <a href="<?php echo e(route('member.notification')); ?>" class="user-dropdown-item">
                        <i class="icon-bell"></i><span>Notifications</span>
                    </a>
                    <hr class="user-dropdown-divider">
                    <a href="<?php echo e(route('logout')); ?>" class="user-dropdown-item danger">
                        <i class="icon-log-out"></i>
                        <span>Sign Out</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/member/layout/header.blade.php ENDPATH**/ ?>
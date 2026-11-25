<aside class="zentic-sidebar" id="zenticSidebar">
    <div class="sidebar-brand">
        <a href="<?php echo e(url('/')); ?>" class="brand-link">
            <div class="brand-icon">
                <img src="<?php echo e(asset('member/img/logo.png')); ?>" alt="<?php echo e(config('app.name')); ?>">
            </div>
        </a>
        <button class="sidebar-close-btn d-lg-none" id="sidebarClose"><i class="icon-x"></i></button>
    </div>
    <nav class="sidebar-nav">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Member Dashboard', 'Member Profile'])): ?>
            <div class="nav-section">
                <span class="nav-section-label">Main Menu<i class="icon-chevron-down nav-section-arrow"></i></span>
                <ul class="nav-list">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Member Dashboard')): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('member.dashboard')); ?>" class="nav-link">
                                <i class="icon-gauge nav-icon"></i>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Member Profile')): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('member.profile')); ?>" class="nav-link">
                                <i class="icon-circle-user nav-icon"></i>
                                <span class="nav-text">Profile</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Member Product Master', 'Member Product Inquiry'])): ?>
            <div class="nav-section">
                <span class="nav-section-label">
                    Product Management<i class="icon-chevron-down nav-section-arrow"></i>
                </span>
                <ul class="nav-list">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Member Product Master')): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('member.products')); ?>" class="nav-link">
                                <i class="icon-box nav-icon"></i>
                                <span class="nav-text">Product List</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Member Product Inquiry')): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('member.products.inquiries.list')); ?>" class="nav-link">
                                <i class="icon-message-square nav-icon"></i>
                                <span class="nav-text">Product Inquiry</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Member Tree View', 'My Downline'])): ?>
            <div class="nav-section">
                <span class="nav-section-label">
                    Network Management<i class="icon-chevron-down nav-section-arrow"></i>
                </span>
                <ul class="nav-list">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Member Tree View')): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('member.treeview')); ?>" class="nav-link">
                                <i class="icon-network nav-icon"></i>
                                <span class="nav-text">Tree View</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('My Downline')): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('member.my.downline')); ?>" class="nav-link">
                                <i class="icon-users-round nav-icon"></i>
                                <span class="nav-text">My Downline</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
    </nav>
    <div class="sidebar-user" id="sidebarUserTrigger">
        <?php if(auth()->user()->profile_image): ?>
            <img class="avatar-gen-img sidebar-user-avatar" src="<?php echo e(asset(auth()->user()->profile_image)); ?>"
                data-size="40" alt="<?php echo e(auth()->user()->full_name); ?>">
        <?php else: ?>
            <img class="avatar-gen-img" src="<?php echo e(asset('member/img/avatar/avatar.jpg')); ?>" data-size="40"
                alt="<?php echo e(auth()->user()->full_name); ?>">
        <?php endif; ?>
        <div class="sidebar-user-info">
            <span class="sidebar-user-name"><?php echo e(auth()->user()->full_name); ?></span>
            <span class="sidebar-user-role"><?php echo e(auth()->user()->unique_id); ?></span>
        </div>
        <button class="sidebar-user-settings" id="sidebarUserBtn"><i class="icon-ellipsis-vertical"></i></button>
        <!-- Sidebar profile popup -->
        <div class="sidebar-profile-popup" id="sidebarProfilePopup">
            <div class="spp-header">
                <div class="spp-avatar">
                    <?php if(auth()->user()->profile_image): ?>
                        <img class="avatar-gen-img" src="<?php echo e(asset(auth()->user()->profile_image)); ?>" data-size="40"
                            alt="<?php echo e(auth()->user()->full_name); ?>" />
                    <?php else: ?>
                        <img class="avatar-gen-img" src="<?php echo e(asset('member/img/avatar/avatar.jpg')); ?>" data-size="40"
                            alt="<?php echo e(auth()->user()->full_name); ?>" />
                    <?php endif; ?>
                </div>
                <div class="spp-info">
                    <span class="spp-name"><?php echo e(auth()->user()->full_name); ?></span>
                    <span class="spp-subtitle"><?php echo e(auth()->user()->unique_id); ?></span>
                </div>
                <button class="spp-close" id="sidebarProfileClose"><i class="icon-x"></i></button>
            </div>
            <div class="spp-body">
                <a href="<?php echo e(route('member.profile')); ?>" class="spp-item">
                    <i class="icon-circle-user"></i>My Profile
                </a>
                <a href="<?php echo e(route('member.notification')); ?>" class="spp-item">
                    <i class="icon-bell"></i><span>Notifications</span>
                </a>
                <hr class="spp-divider">
                <a href="<?php echo e(route('logout')); ?>" class="spp-item spp-danger">
                    <i class="icon-log-out"></i> Sign Out
                </a>
            </div>
        </div>
    </div>
</aside>
<?php /**PATH D:\xampp\htdocs\Farmer\resources\views/member/layout/Sidebar.blade.php ENDPATH**/ ?>
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <!-- Logo -->
    <div class="sidebar-logo">
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="logo logo-normal text-center">
            <img src="<?php echo e(asset('admin/img/logo.png')); ?>" alt="Logo" style="height:50px; width:auto;">
        </a>
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="logo-small">
            <img src="<?php echo e(asset('admin/img/logo-small.png')); ?>" alt="Logo">
        </a>
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="dark-logo">
            <img src="<?php echo e(asset('admin/img/logo-white.png')); ?>" alt="Logo">
        </a>
    </div>
    <!-- /Logo -->
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Admin Dashboard')): ?>
                    <li>
                        <ul class="m-0">
                            <li>
                                <a href="<?php echo e(route('admin.dashboard')); ?>" class="px-2">
                                    <i class="ti ti-smart-home"></i><span> Dashboard</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Member Details')): ?>
                    <li>
                        <ul class="m-0">
                            <li>
                                <a href="<?php echo e(route('admin.member.details')); ?>" class="px-2">
                                    <i class="ti ti-users"></i><span> Member Details</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Product Management')): ?>
                    <li>
                        <ul class="m-0">
                            <li>
                                <a href="<?php echo e(route('admin.product.list')); ?>" class="px-2">
                                    <i class="ti ti-package"></i>
                                    <span>Product Management</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Admin Tree View', 'Admin Downline'])): ?>
                    <li>
                        <ul class="m-0">
                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="ti ti-network"></i><span>Network Management</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Admin Tree View')): ?>
                                        <li>
                                            <a href="<?php echo e(route('admin.member.tree')); ?>">Member Tree View</a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Admin Downline')): ?>
                                        <li>
                                            <a href="<?php echo e(route('admin.member.downline')); ?>">Member Downline</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any([
                    'Categories Master',
                    'Banner Master',
                    'Popup Master',
                    'Notification Master',
                    'State Master',
                    'City Master',
                    'Privacy Policy',
                    'Terms
                    Conditions',
                    ])): ?>
                    <li>
                        <ul class="m-0">
                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="ti ti-settings"></i><span>Master Setting</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Categories Master')): ?>
                                        <li><a href="<?php echo e(route('admin.category.index')); ?>">Categories Master</a></li>
                                        <li><a href="<?php echo e(route('admin.sub-category.index')); ?>">Sub Category Master</a></li>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Banner Master')): ?>
                                        <li><a href="<?php echo e(route('admin.banner.index')); ?>">Banner Master</a></li>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Popup Master')): ?>
                                        <li><a href="<?php echo e(route('admin.popup.index')); ?>">Popup Master</a></li>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Notification Master')): ?>
                                        <li><a href="<?php echo e(route('admin.notification-master.index')); ?>">Notification Master</a></li>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Level Master')): ?>
                                        <li><a href="<?php echo e(route('admin.level.index')); ?>">Level Master</a></li>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('State Master')): ?>
                                        <li><a href="<?php echo e(route('admin.state.list')); ?>">State Master</a></li>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('City Master')): ?>
                                        <li><a href="<?php echo e(route('admin.city.list')); ?>">City Master</a></li>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Privacy Policy')): ?>
                                        <li><a href="<?php echo e(route('admin.privacy.policy')); ?>">Privacy & Policy</a></li>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Terms Conditions')): ?>
                                        <li><a href="<?php echo e(route('admin.terms.conditions')); ?>">Terms & Conditions</a></li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Blog Management')): ?>
                    <li>
                        <ul class="m-0">
                            <li>
                                <a href="<?php echo e(route('admin.blog.index')); ?>"
                                    class="px-2 <?php echo e(request()->routeIs('admin.blog.*') ? 'active' : ''); ?>">
                                    <i class="ti ti-news"></i>
                                    <span>Blog Management</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['View Contact Inquiries', 'View Product Inquiries'])): ?>
                    <li>
                        <ul class="m-0">
                            <li class="submenu">
                                <a href="javascript:void(0);"
                                    class="<?php echo e(request()->routeIs('admin.contact.inquiries.*', 'admin.product.inquiries.*') ? 'subdrop active' : ''); ?>">
                                    <i class="ti ti-message-question"></i><span>Inquiries</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('View Contact Inquiries')): ?>
                                        <li>
                                            <a href="<?php echo e(route('admin.contact.inquiries.list')); ?>"
                                                class="<?php echo e(request()->routeIs('admin.contact.inquiries.*') ? 'active' : ''); ?>">
                                                Contact Us Inquiry
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('View Product Inquiries')): ?>
                                        <li>
                                            <a href="<?php echo e(route('admin.product.inquiries.list')); ?>"
                                                class="<?php echo e(request()->routeIs('admin.product.inquiries.*') ? 'active' : ''); ?>">
                                                Product Inquiry
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('View Surveys')): ?>
                    <li>
                        <ul class="m-0">
                            <li class="submenu">
                                <a href="javascript:void(0);"
                                    class="<?php echo e(request()->routeIs('admin.survey.*') ? 'subdrop active' : ''); ?>">
                                    <i class="ti ti-clipboard-list"></i><span>Survey</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li>
                                        <a href="<?php echo e(route('admin.survey.index')); ?>"
                                            class="<?php echo e(request()->routeIs('admin.survey.index', 'admin.survey.edit', 'admin.survey.questions.*', 'admin.survey.results') ? 'active' : ''); ?>">
                                            Survey List
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo e(route('admin.survey.create')); ?>"
                                            class="<?php echo e(request()->routeIs('admin.survey.create') ? 'active' : ''); ?>">
                                            Add Survey
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['View Roles', 'View Permissions'])): ?>
                    <li>
                        <ul class="m-0">
                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="ti ti-shield"></i><span>Roles & Permissions</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('View Roles')): ?>
                                        <li><a href="<?php echo e(route('admin.roles.list')); ?>">Role List</a></li>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('View Permissions')): ?>
                                        <li><a href="<?php echo e(route('admin.permissions.list')); ?>">Permission List</a></li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->
<?php /**PATH D:\xampp\htdocs\Farmer\resources\views/admin/layout/Sidebar.blade.php ENDPATH**/ ?>
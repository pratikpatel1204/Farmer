<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <!-- Logo -->
    <div class="sidebar-logo">
        <a href="{{ route('admin.dashboard') }}" class="logo logo-normal text-center">
            <img src="{{ asset('admin/img/logo.png') }}" alt="Logo" style="height:50px; width:auto;">
        </a>
        <a href="{{ route('admin.dashboard') }}" class="logo-small">
            <img src="{{ asset('admin/img/logo-small.png') }}" alt="Logo">
        </a>
        <a href="{{ route('admin.dashboard') }}" class="dark-logo">
            <img src="{{ asset('admin/img/logo-white.png') }}" alt="Logo">
        </a>
    </div>
    <!-- /Logo -->
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                @can('Admin Dashboard')
                    <li>
                        <ul class="m-0">
                            <li>
                                <a href="{{ route('admin.dashboard') }}" class="px-2">
                                    <i class="ti ti-smart-home"></i><span> Dashboard</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan
                @can('Member Details')
                    <li>
                        <ul class="m-0">
                            <li>
                                <a href="{{ route('admin.member.details') }}" class="px-2">
                                    <i class="ti ti-users"></i><span> Member Details</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan
                @can('Product Management')
                    <li>
                        <ul class="m-0">
                            <li>
                                <a href="{{ route('admin.product.list') }}" class="px-2">
                                    <i class="ti ti-package"></i>
                                    <span>Product Management</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan
                @canany(['Admin Tree View', 'Admin Downline'])
                    <li>
                        <ul class="m-0">
                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="ti ti-network"></i><span>Network Management</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    @can('Admin Tree View')
                                        <li>
                                            <a href="{{ route('admin.member.tree') }}">Member Tree View</a>
                                        </li>
                                    @endcan
                                    @can('Admin Downline')
                                        <li>
                                            <a href="{{ route('admin.member.downline') }}">Member Downline</a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endcanany
                @canany([
                    'Categories Master',
                    'Banner Master',
                    'State Master',
                    'City Master',
                    'Privacy Policy',
                    'Terms
                    Conditions',
                    ])
                    <li>
                        <ul class="m-0">
                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="ti ti-settings"></i><span>Master Setting</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    @can('Categories Master')
                                        <li><a href="{{ route('admin.category.index') }}">Categories Master</a></li>
                                    @endcan
                                    @can('Banner Master')
                                        <li><a href="{{ route('admin.banner.index') }}">Banner Master</a></li>
                                    @endcan
                                    @can('Popup Master')
                                        <li><a href="{{ route('admin.popup.index') }}">Popup Master</a></li>
                                    @endcan
                                    @can('Level Master')
                                        <li><a href="{{ route('admin.level.index') }}">Level Master</a></li>
                                    @endcan
                                    @can('State Master')
                                        <li><a href="{{ route('admin.state.list') }}">State Master</a></li>
                                    @endcan
                                    @can('City Master')
                                        <li><a href="{{ route('admin.city.list') }}">City Master</a></li>
                                    @endcan
                                    @can('Privacy Policy')
                                        <li><a href="{{ route('admin.privacy.policy') }}">Privacy & Policy</a></li>
                                    @endcan
                                    @can('Terms Conditions')
                                        <li><a href="{{ route('admin.terms.conditions') }}">Terms & Conditions</a></li>
                                    @endcan
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endcanany
                @can('Blog Management')
                    <li>
                        <ul class="m-0">
                            <li>
                                <a href="{{ route('admin.blog.index') }}"
                                    class="px-2 {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}">
                                    <i class="ti ti-news"></i>
                                    <span>Blog Management</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan
                @canany(['View Contact Inquiries', 'View Product Inquiries'])
                    <li>
                        <ul class="m-0">
                            <li class="submenu">
                                <a href="javascript:void(0);"
                                    class="{{ request()->routeIs('admin.contact.inquiries.*', 'admin.product.inquiries.*') ? 'subdrop active' : '' }}">
                                    <i class="ti ti-message-question"></i><span>Inquiries</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    @can('View Contact Inquiries')
                                        <li>
                                            <a href="{{ route('admin.contact.inquiries.list') }}"
                                                class="{{ request()->routeIs('admin.contact.inquiries.*') ? 'active' : '' }}">
                                                Contact Us Inquiry
                                            </a>
                                        </li>
                                    @endcan
                                    @can('View Product Inquiries')
                                        <li>
                                            <a href="{{ route('admin.product.inquiries.list') }}"
                                                class="{{ request()->routeIs('admin.product.inquiries.*') ? 'active' : '' }}">
                                                Product Inquiry
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endcanany
                @can('View Surveys')
                    <li>
                        <ul class="m-0">
                            <li class="submenu">
                                <a href="javascript:void(0);"
                                    class="{{ request()->routeIs('admin.survey.*') ? 'subdrop active' : '' }}">
                                    <i class="ti ti-clipboard-list"></i><span>Survey</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li>
                                        <a href="{{ route('admin.survey.index') }}"
                                            class="{{ request()->routeIs('admin.survey.index', 'admin.survey.edit', 'admin.survey.questions.*', 'admin.survey.results') ? 'active' : '' }}">
                                            Survey List
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.survey.create') }}"
                                            class="{{ request()->routeIs('admin.survey.create') ? 'active' : '' }}">
                                            Add Survey
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endcan

                @canany(['View Roles', 'View Permissions'])
                    <li>
                        <ul class="m-0">
                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="ti ti-shield"></i><span>Roles & Permissions</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    @can('View Roles')
                                        <li><a href="{{ route('admin.roles.list') }}">Role List</a></li>
                                    @endcan
                                    @can('View Permissions')
                                        <li><a href="{{ route('admin.permissions.list') }}">Permission List</a></li>
                                    @endcan
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endcanany
            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->

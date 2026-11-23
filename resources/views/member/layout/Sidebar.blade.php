<aside class="zentic-sidebar" id="zenticSidebar">
    <div class="sidebar-brand">
        <a href="{{ url('/') }}" class="brand-link">
            <div class="brand-icon">
                <img src="{{ asset('member/img/logo.png') }}" alt="{{ config('app.name') }}">
            </div>
        </a>
        <button class="sidebar-close-btn d-lg-none" id="sidebarClose"><i class="icon-x"></i></button>
    </div>
    <nav class="sidebar-nav">
        @canany(['Member Dashboard', 'Member Profile'])
            <div class="nav-section">
                <span class="nav-section-label">Main Menu<i class="icon-chevron-down nav-section-arrow"></i></span>
                <ul class="nav-list">
                    @can('Member Dashboard')
                        <li class="nav-item">
                            <a href="{{ route('member.dashboard') }}" class="nav-link">
                                <i class="icon-gauge nav-icon"></i>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </li>
                    @endcan
                    @can('Member Profile')
                        <li class="nav-item">
                            <a href="{{ route('member.profile') }}" class="nav-link">
                                <i class="icon-circle-user nav-icon"></i>
                                <span class="nav-text">Profile</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </div>
        @endcanany
        @canany(['Member Product Master', 'Member Product Inquiry'])
            <div class="nav-section">
                <span class="nav-section-label">
                    Product Management<i class="icon-chevron-down nav-section-arrow"></i>
                </span>
                <ul class="nav-list">
                    @can('Member Product Master')
                        <li class="nav-item">
                            <a href="{{ route('member.products') }}" class="nav-link">
                                <i class="icon-box nav-icon"></i>
                                <span class="nav-text">Product List</span>
                            </a>
                        </li>
                    @endcan
                    @can('Member Product Inquiry')
                        <li class="nav-item">
                            <a href="{{route('member.products.inquiries.list')}}" class="nav-link">
                                <i class="icon-message-square nav-icon"></i>
                                <span class="nav-text">Product Inquiry</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </div>
        @endcanany
        @canany(['Member Tree View', 'My Downline'])
            <div class="nav-section">
                <span class="nav-section-label">
                    Network Management<i class="icon-chevron-down nav-section-arrow"></i>
                </span>
                <ul class="nav-list">
                    @can('Member Tree View')
                        <li class="nav-item">
                            <a href="{{ route('member.treeview') }}" class="nav-link">
                                <i class="icon-network nav-icon"></i>
                                <span class="nav-text">Tree View</span>
                            </a>
                        </li>
                    @endcan
                    @can('My Downline')
                        <li class="nav-item">
                            <a href="{{route('member.my.downline')}}" class="nav-link">
                                <i class="icon-users-round nav-icon"></i>
                                <span class="nav-text">My Downline</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </div>
        @endcanany
    </nav>
    <div class="sidebar-user" id="sidebarUserTrigger">
        @if (auth()->user()->profile_image)
            <img class="avatar-gen-img sidebar-user-avatar" src="{{ asset(auth()->user()->profile_image) }}"
                data-size="40" alt="{{ auth()->user()->full_name }}">
        @else
            <img class="avatar-gen-img" src="{{ asset('member/img/avatar/avatar.jpg') }}" data-size="40"
                alt="{{ auth()->user()->full_name }}">
        @endif
        <div class="sidebar-user-info">
            <span class="sidebar-user-name">{{ auth()->user()->full_name }}</span>
            <span class="sidebar-user-role">{{ auth()->user()->unique_id }}</span>
        </div>
        <button class="sidebar-user-settings" id="sidebarUserBtn"><i class="icon-ellipsis-vertical"></i></button>
        <!-- Sidebar profile popup -->
        <div class="sidebar-profile-popup" id="sidebarProfilePopup">
            <div class="spp-header">
                <div class="spp-avatar">
                    @if (auth()->user()->profile_image)
                        <img class="avatar-gen-img" src="{{ asset(auth()->user()->profile_image) }}" data-size="40"
                            alt="{{ auth()->user()->full_name }}" />
                    @else
                        <img class="avatar-gen-img" src="{{ asset('member/img/avatar/avatar.jpg') }}" data-size="40"
                            alt="{{ auth()->user()->full_name }}" />
                    @endif
                </div>
                <div class="spp-info">
                    <span class="spp-name">{{ auth()->user()->full_name }}</span>
                    <span class="spp-subtitle">{{ auth()->user()->unique_id }}</span>
                </div>
                <button class="spp-close" id="sidebarProfileClose"><i class="icon-x"></i></button>
            </div>
            <div class="spp-body">
                <a href="{{ route('member.profile') }}" class="spp-item">
                    <i class="icon-circle-user"></i>My Profile
                </a>
                <a href="{{ route('member.notification') }}" class="spp-item">
                    <i class="icon-bell"></i><span>Notifications</span>
                </a>
                <hr class="spp-divider">
                <a href="{{ route('logout') }}" class="spp-item spp-danger">
                    <i class="icon-log-out"></i> Sign Out
                </a>
            </div>
        </div>
    </div>
</aside>

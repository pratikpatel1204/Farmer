@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || Admin Dashboard')
@php
    $sections = [
        [
            'title' => 'Products',
            'sub' => number_format($productStats['have']) . ' I Have · ' . number_format($productStats['want']) . ' I Want · ' . number_format($productStats['this_month']) . ' added this month',
            'cards' => [
                ['label' => 'Total Products', 'value' => $productStats['total'], 'note' => 'All listings', 'icon' => 'ti-package', 'bg' => 'bg-primary'],
                ['label' => 'Approved', 'value' => $productStats['approved'], 'note' => 'Live on the website', 'icon' => 'ti-circle-check', 'bg' => 'bg-success'],
                ['label' => 'Pending', 'value' => $productStats['pending'], 'note' => 'Waiting for review', 'icon' => 'ti-clock-hour-4', 'bg' => 'bg-warning'],
                ['label' => 'Rejected', 'value' => $productStats['rejected'], 'note' => 'Sent back to members', 'icon' => 'ti-circle-x', 'bg' => 'bg-danger'],
            ],
        ],
        [
            'title' => 'Product Inquiries',
            'sub' => number_format($inquiryStats['today']) . ' today · ' . number_format($inquiryStats['this_month']) . ' this month',
            'cards' => [
                ['label' => 'Total Inquiries', 'value' => $inquiryStats['total'], 'note' => 'All time', 'icon' => 'ti-message-question', 'bg' => 'bg-primary'],
                ['label' => 'Pending', 'value' => $inquiryStats['pending'], 'note' => 'Not contacted yet', 'icon' => 'ti-bell-ringing', 'bg' => 'bg-warning'],
                ['label' => 'Contacted', 'value' => $inquiryStats['contacted'], 'note' => 'Seller has replied', 'icon' => 'ti-phone-check', 'bg' => 'bg-info'],
                ['label' => 'Closed', 'value' => $inquiryStats['closed'], 'note' => 'Deal done or closed', 'icon' => 'ti-circle-check', 'bg' => 'bg-success'],
            ],
        ],
        [
            'title' => 'Members',
            'sub' => number_format($memberStats['active']) . ' active members (admins not counted)',
            'cards' => [
                ['label' => 'Total Members', 'value' => $memberStats['total'], 'note' => 'Farmers + Non Farmers', 'icon' => 'ti-users', 'bg' => 'bg-primary'],
                ['label' => 'Farmers', 'value' => $memberStats['farmers'], 'note' => 'Role: Farmer', 'icon' => 'ti-plant-2', 'bg' => 'bg-success'],
                ['label' => 'Non Farmers', 'value' => $memberStats['non_farmers'], 'note' => 'Role: Non Farmer', 'icon' => 'ti-building-store', 'bg' => 'bg-info'],
                ['label' => 'Joined This Month', 'value' => $memberStats['this_month'], 'note' => now()->format('F Y'), 'icon' => 'ti-user-plus', 'bg' => 'bg-secondary'],
            ],
        ],
        [
            'title' => 'Business Profiles',
            'sub' => 'Member business profiles and verification',
            'cards' => [
                ['label' => 'Total Profiles', 'value' => $profileStats['total'], 'note' => 'Profiles created', 'icon' => 'ti-id-badge-2', 'bg' => 'bg-primary'],
                ['label' => 'Approved', 'value' => $profileStats['approved'], 'note' => 'Shown on the website', 'icon' => 'ti-shield-check', 'bg' => 'bg-success'],
                ['label' => 'Pending', 'value' => $profileStats['pending'], 'note' => 'Need verification', 'icon' => 'ti-hourglass', 'bg' => 'bg-warning'],
                ['label' => 'No Profile Yet', 'value' => $profileStats['without'], 'note' => 'Members without a profile', 'icon' => 'ti-user-off', 'bg' => 'bg-dark'],
            ],
        ],
        [
            'title' => 'Contact Us Messages',
            'sub' => number_format($contactStats['today']) . ' today · ' . number_format($contactStats['this_week']) . ' this week · ' . number_format($contactStats['this_month']) . ' this month',
            'cards' => [
                ['label' => 'Total Messages', 'value' => $contactStats['total'], 'note' => 'All time', 'icon' => 'ti-mail', 'bg' => 'bg-primary'],
                ['label' => 'Pending', 'value' => $contactStats['pending'], 'note' => 'Not contacted yet', 'icon' => 'ti-mail-opened', 'bg' => 'bg-warning'],
                ['label' => 'Contacted', 'value' => $contactStats['contacted'], 'note' => 'Team has replied', 'icon' => 'ti-phone-check', 'bg' => 'bg-info'],
                ['label' => 'Closed', 'value' => $contactStats['closed'], 'note' => 'Resolved', 'icon' => 'ti-circle-check', 'bg' => 'bg-success'],
            ],
        ],
    ];
@endphp
@section('content')
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Admin Dashboard</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="javascript:void(0);"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">{{ auth()->user()->role }}</li>
                        <li class="breadcrumb-item active" aria-current="page">Admin Dashboard</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- Welcome Wrap -->
        <div class="card border-0">
            <div class="card-body d-flex align-items-center justify-content-between flex-wrap p-1">
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-xl flex-shrink-0">
                        @if (auth()->user()->profile_image)
                            <img src="{{ asset(auth()->user()->profile_image) }}" class="rounded-circle"
                                alt="{{ auth()->user()->name }}">
                        @else
                            <span
                                class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white fw-bold"
                                style="width: 40px; height: 40px; font-size: 22px;">
                                {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
                            </span>
                        @endif
                    </span>
                    <div class="ms-2">
                        <h3 class="mb-0">
                            Welcome Back, {{ auth()->user()->full_name }} ({{ auth()->user()->unique_id }})
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Welcome Wrap -->

        <!-- Widget Info -->
        @foreach ($sections as $section)
            <div class="km-admin-section mt-4">
                <div class="mb-3">
                    <h4 class="mb-1">{{ $section['title'] }}</h4>
                    <p class="fs-12 text-muted mb-0">{{ $section['sub'] }}</p>
                </div>
                <div class="row">
                    @foreach ($section['cards'] as $card)
                        <div class="col-xl-3 col-sm-6 d-flex">
                            <div class="card flex-fill km-admin-stat">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="overflow-hidden">
                                            <p class="fs-12 fw-medium mb-1 text-truncate">{{ $card['label'] }}</p>
                                            <h4 class="mb-1">{{ number_format($card['value']) }}</h4>
                                            <p class="fs-12 text-muted mb-0 text-truncate">{{ $card['note'] }}</p>
                                        </div>
                                        <span class="avatar avatar-lg rounded-circle flex-shrink-0 {{ $card['bg'] }}">
                                            <i class="ti {{ $card['icon'] }} fs-20"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        <!-- /Widget Info -->

        <style>
            .km-admin-stat {
                transition: transform .18s ease, box-shadow .18s ease;
            }

            .km-admin-stat:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 24px -16px rgba(0, 0, 0, .35);
            }
        </style>
    </div>
@endsection
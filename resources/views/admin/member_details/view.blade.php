@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || Member Profile')

@section('content')
    @php
        $statusClass = ['Approved' => 'success', 'Rejected' => 'danger'][$profile->status] ?? 'warning';
        $socials = [
            'website' => ['ti-world', 'Website'],
            'youtube' => ['ti-brand-youtube', 'YouTube'],
            'facebook' => ['ti-brand-facebook', 'Facebook'],
            'instagram' => ['ti-brand-instagram', 'Instagram'],
            'linkedin' => ['ti-brand-linkedin', 'LinkedIn'],
        ];
        $location = collect([$member->area, $cityName, $stateName, $member->pincode])->filter()->implode(', ');
    @endphp

    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Member Profile</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.member.details') }}">Member Details</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $member->full_name }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2 mt-2 mt-md-0">
                <a href="{{ route('admin.member.details') }}" class="btn btn-light">
                    <i class="ti ti-arrow-left"></i> Back
                </a>
                <a href="{{ route('admin.member.details.edit', $member->id) }}" class="btn btn-primary">
                    <i class="ti ti-edit"></i> Edit Member
                </a>
            </div>
        </div>

        <div class="row">
            {{-- ================= LEFT: MEMBER CARD ================= --}}
            <div class="col-xl-4">
                <div class="card profile-card">
                    <div class="profile-cover"></div>
                    <div class="card-body text-center pt-0">
                        <div class="profile-avatar">
                            @if ($member->profile_image)
                                <img src="{{ asset($member->profile_image) }}" alt="{{ $member->full_name }}">
                            @else
                                <span><i class="ti ti-user"></i></span>
                            @endif
                        </div>
                        <h4 class="mb-1">{{ $member->full_name }}</h4>
                        <div class="d-flex justify-content-center flex-wrap gap-1 mb-3">
                            <span class="badge bg-{{ $member->role === 'Farmer' ? 'success' : 'info' }}">
                                {{ $member->role }}
                            </span>
                            <span class="badge bg-dark"><i class="ti ti-fingerprint"></i> {{ $member->unique_id }}</span>
                            <span class="badge bg-{{ (int) $member->status === 1 ? 'success' : 'danger' }}">
                                {{ (int) $member->status === 1 ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="stat-box">
                                    <h5 class="mb-0">{{ $member->products_count ?? 0 }}</h5>
                                    <small class="text-muted">Products</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-box">
                                    <h5 class="mb-0 text-{{ $profileComplete ? 'success' : 'danger' }}">
                                        {{ $profileComplete ? 'Yes' : 'No' }}
                                    </h5>
                                    <small class="text-muted">Profile Complete</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <ul class="list-group list-group-flush info-list">
                        <li class="list-group-item">
                            <span class="info-label"><i class="ti ti-phone"></i> Mobile</span>
                            <span>
                                {{ $member->mobile ?? '-' }}
                                @if ((int) $member->mobile_verified === 1)
                                    <i class="ti ti-circle-check text-success" title="Verified"></i>
                                @else
                                    <i class="ti ti-circle-x text-danger" title="Not verified"></i>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item">
                            <span class="info-label"><i class="ti ti-mail"></i> Email</span>
                            <span class="text-break">{{ $member->email ?? '-' }}</span>
                        </li>
                        <li class="list-group-item">
                            <span class="info-label"><i class="ti ti-user-share"></i> Sponsor</span>
                            <span>
                                @if ($member->sponsor_id)
                                    {{ $member->sponsor_name }} <small
                                        class="text-muted">({{ $member->sponsor_id }})</small>
                                @else
                                    -
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item">
                            <span class="info-label"><i class="ti ti-calendar"></i> Joined</span>
                            <span>{{ optional($member->created_at)->format('d M Y') ?? '-' }}</span>
                        </li>
                        <li class="list-group-item flex-column align-items-start">
                            <span class="info-label mb-1"><i class="ti ti-map-pin"></i> Address</span>
                            <span class="text-muted small">
                                {{ $member->address ?? '-' }}
                                @if ($location)
                                    <br>{{ $location }}
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- ================= RIGHT: PROFILE DETAILS ================= --}}
            <div class="col-xl-8">
                {{-- Verification status --}}
                <div class="card border-{{ $statusClass }}">
                    <div class="card-body d-md-flex align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3 mb-3 mb-md-0">
                            <span class="status-icon bg-{{ $statusClass }}">
                                <i
                                    class="ti {{ $profile->status === 'Approved' ? 'ti-shield-check' : ($profile->status === 'Rejected' ? 'ti-shield-x' : 'ti-hourglass') }}"></i>
                            </span>
                            <div>
                                <h5 class="mb-1">Profile {{ $profile->status ?? 'Pending' }}</h5>
                                @if ($profile->status === 'Rejected' && $profile->reject_reason)
                                    <p class="text-danger mb-0 small"><i class="ti ti-info-circle"></i>
                                        {{ $profile->reject_reason }}</p>
                                @else
                                    <p class="text-muted mb-0 small">Last updated
                                        {{ optional($profile->updated_at)->format('d M Y, h:i A') }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            @if ($profile->status !== 'Approved')
                                <button class="btn btn-success profileActionBtn" data-status="Approved">
                                    <i class="ti ti-check"></i> Approve
                                </button>
                            @endif
                            @if ($profile->status !== 'Rejected')
                                <button class="btn btn-outline-danger profileActionBtn" data-status="Rejected">
                                    <i class="ti ti-x"></i> Reject
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header pb-0 border-bottom-0">
                        <ul class="nav nav-tabs nav-tabs-bottom" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tab-about">
                                    <i class="ti ti-info-circle me-1"></i> About
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab-kyc">
                                    <i class="ti ti-id me-1"></i> Bank & KYC
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab-media">
                                    <i class="ti ti-photo me-1"></i> Photos & Video
                                    <span class="badge bg-light text-dark ms-1">{{ count($images) }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">

                            {{-- ===== ABOUT ===== --}}
                            <div class="tab-pane fade show active" id="tab-about">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <div class="mini-tile">
                                            <span class="tile-icon bg-primary"><i class="ti ti-building"></i></span>
                                            <div>
                                                <small class="text-muted">Established</small>
                                                <h6 class="mb-0">{{ $profile->establishment_year ?? '-' }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mini-tile">
                                            <span class="tile-icon bg-success"><i class="ti ti-users-group"></i></span>
                                            <div>
                                                <small class="text-muted">Employees</small>
                                                <h6 class="mb-0">{{ $profile->number_of_workers ?? '-' }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mini-tile">
                                            <span class="tile-icon bg-warning"><i class="ti ti-ruler-2"></i></span>
                                            <div>
                                                <small class="text-muted">Area (Sq. Yd)</small>
                                                <h6 class="mb-0">
                                                    {{ $profile->area_in_square_yard ? rtrim(rtrim(number_format($profile->area_in_square_yard, 2), '0'), '.') : '-' }}
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="section-title">Description</h6>
                                <div class="profile-description mb-4">
                                    {!! $descriptionHtml ?: '<p class="text-muted mb-0">No description added.</p>' !!}
                                </div>

                                <h6 class="section-title">Social Links</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @php $hasSocial = false; @endphp
                                    @foreach ($socials as $field => [$icon, $label])
                                        @if (!empty($profile->{$field}) && preg_match('#^https?://#i', $profile->{$field}))
                                            @php $hasSocial = true; @endphp
                                            <a href="{{ $profile->{$field} }}" target="_blank"
                                                rel="noopener noreferrer" class="btn btn-outline-light text-dark">
                                                <i class="ti {{ $icon }} me-1"></i> {{ $label }}
                                            </a>
                                        @endif
                                    @endforeach
                                    @unless ($hasSocial)
                                        <span class="text-muted">No links added.</span>
                                    @endunless
                                </div>
                            </div>

                            {{-- ===== BANK & KYC ===== --}}
                            <div class="tab-pane fade" id="tab-kyc">
                                <h6 class="section-title">Bank Account</h6>
                                <div class="row g-3 mb-4">
                                    @foreach (['bank_name' => 'Bank Name', 'account_number' => 'Account Number', 'branch_name' => 'Branch', 'ifsc_code' => 'IFSC Code'] as $f => $label)
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <small class="text-muted">{{ $label }}</small>
                                                <div class="fw-semibold">{{ $profile->{$f} ?: '-' }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <h6 class="section-title">KYC Documents</h6>
                                <div class="row g-3">
                                    @foreach ([['Aadhar Card', 'aadhar_number', 'aadhar_image'], ['PAN Card', 'pan_number', 'personal_pan_image']] as [$label, $numField, $imgField])
                                        <div class="col-md-6">
                                            <div class="kyc-card">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div>
                                                        <small class="text-muted">{{ $label }}</small>
                                                        <div class="fw-semibold">{{ $profile->{$numField} ?: '-' }}
                                                        </div>
                                                    </div>
                                                    @if ($profile->{$imgField})
                                                        <span class="badge bg-success">Uploaded</span>
                                                    @else
                                                        <span class="badge bg-danger">Missing</span>
                                                    @endif
                                                </div>
                                                @if ($profile->{$imgField})
                                                    <a href="{{ asset($profile->{$imgField}) }}" target="_blank">
                                                        <img src="{{ asset($profile->{$imgField}) }}"
                                                            alt="{{ $label }}" class="kyc-img">
                                                    </a>
                                                @else
                                                    <div class="kyc-empty"><i class="ti ti-photo-off"></i></div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- ===== PHOTOS & VIDEO ===== --}}
                            <div class="tab-pane fade" id="tab-media">
                                <h6 class="section-title">Company Photos</h6>
                                @if (count($images))
                                    <div class="row g-2 mb-4">
                                        @foreach ($images as $img)
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <a href="{{ asset($img) }}" target="_blank" class="gallery-item">
                                                    <img src="{{ asset($img) }}" alt="Company photo">
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted mb-4">No photos uploaded.</p>
                                @endif

                                <h6 class="section-title">Company Video</h6>
                                @if ($profile->company_video)
                                    <video src="{{ asset($profile->company_video) }}" controls
                                        class="w-100 rounded" style="max-height: 360px; background:#000;"></video>
                                @else
                                    <p class="text-muted mb-0">No video uploaded.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .profile-card {
            overflow: hidden;
        }

        .profile-cover {
            height: 90px;
            background: linear-gradient(135deg, var(--bs-primary), #ff9b44);
        }

        .profile-avatar {
            width: 96px;
            height: 96px;
            margin: -48px auto 12px;
            border-radius: 50%;
            border: 4px solid #fff;
            background: #f1f3f5;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-avatar span {
            display: flex;
            width: 100%;
            height: 100%;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            color: #adb5bd;
        }

        .stat-box {
            border: 1px solid #eef0f2;
            border-radius: 8px;
            padding: 10px;
            background: #fafbfc;
        }

        .info-list .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
        }

        .info-label {
            color: #6c757d;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-icon,
        .tile-icon {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .tile-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            font-size: 20px;
        }

        .mini-tile {
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid #eef0f2;
            border-radius: 8px;
            padding: 12px;
            height: 100%;
        }

        .section-title {
            font-weight: 600;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #e5e7eb;
        }

        .profile-description {
            color: #495057;
            line-height: 1.6;
        }

        .profile-description h1,
        .profile-description h2,
        .profile-description h3 {
            font-size: 1.1rem;
        }

        .detail-item {
            border: 1px solid #eef0f2;
            border-radius: 8px;
            padding: 10px 14px;
            background: #fafbfc;
        }

        .kyc-card {
            border: 1px solid #eef0f2;
            border-radius: 8px;
            padding: 12px;
            height: 100%;
        }

        .kyc-img {
            width: 100%;
            height: 170px;
            object-fit: contain;
            border-radius: 6px;
            background: #f8f9fa;
            border: 1px solid #eef0f2;
        }

        .kyc-empty {
            height: 170px;
            border-radius: 6px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: #ced4da;
        }

        .gallery-item {
            display: block;
            border-radius: 8px;
            overflow: hidden;
            aspect-ratio: 4 / 3;
            background: #f8f9fa;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .2s;
        }

        .gallery-item:hover img {
            transform: scale(1.05);
        }
    </style>

    <script>
        $(document).on('click', '.profileActionBtn', function() {
            let $btn = $(this);
            let status = $btn.data('status');
            let approve = status === 'Approved';

            Swal.fire(approve ? {
                title: 'Approve this profile?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Approve',
            } : {
                title: 'Reject this profile?',
                input: 'textarea',
                inputLabel: 'Rejection reason',
                inputPlaceholder: 'Reason likhein...',
                showCancelButton: true,
                confirmButtonText: 'Reject',
                confirmButtonColor: '#d33',
                inputValidator: (value) => {
                    if (!value || value.trim() === '') {
                        return 'Rejection reason zaroori hai!';
                    }
                }
            }).then((result) => {
                if (!result.isConfirmed) return;

                $('.profileActionBtn').prop('disabled', true);
                $.ajax({
                    url: "{{ route('admin.member.details.profile.status') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        profile_id: {{ $profile->id }},
                        status: status,
                        reason: approve ? null : result.value
                    },
                    success: function(res) {
                        toastr.success(res.message);
                        setTimeout(() => location.reload(), 800);
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Something went wrong.');
                        $('.profileActionBtn').prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection

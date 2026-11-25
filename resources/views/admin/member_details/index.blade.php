@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || Member Details')
@section('content')
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Member Details</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="ti ti-smart-home"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">Members</li>
                        <li class="breadcrumb-item active">Member Details</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2 mt-2 mt-md-0">
                <button type="button" class="btn btn-white border" id="exportMemberBtn">
                    <i class="ti ti-file-type-xls me-1 text-success"></i> Export Excel
                </button>
                <a href="{{ route('admin.member.details.create') }}" class="btn btn-primary">
                    <i class="ti ti-user-plus"></i> Create Member
                </a>
            </div>
        </div>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-md-flex justify-content-between align-items-center">
                        <ul class="nav nav-tabs card-header-tabs member-tabs mb-2 mb-md-0" role="tablist">
                            <li class="nav-item">
                                <a href="#" class="nav-link active" data-tab="all">
                                    All <span class="badge bg-primary ms-1 tab-count" data-count="all">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-tab="farmer">
                                    Farmer <span class="badge bg-success ms-1 tab-count" data-count="farmer">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-tab="non-farmer">
                                    Non Farmer <span class="badge bg-info ms-1 tab-count" data-count="non-farmer">0</span>
                                </a>
                            </li>
                        </ul>
                        <div class="d-flex align-items-center gap-2">
                            <div style="max-width: 300px; width: 100%;">
                                <input type="text" id="memberSearch" class="form-control"
                                    placeholder="Search name, ID, email, mobile...">
                            </div>
                            <button type="button" class="btn btn-outline-light border text-dark position-relative text-nowrap"
                                data-bs-toggle="collapse" data-bs-target="#memberFilterBox" aria-expanded="true">
                                <i class="ti ti-filter me-1"></i> Filters
                                <span class="badge bg-danger rounded-pill ms-1 d-none" id="activeFilterCount">0</span>
                            </button>
                        </div>
                    </div>

                    {{-- FILTERS --}}
                    <div class="collapse show" id="memberFilterBox">
                        <div class="card-body border-bottom member-filter-box">
                            <form id="memberFilterForm" autocomplete="off">
                                <div class="row g-3">
                                    <div class="col-xl-3 col-lg-4 col-md-6">
                                        <label class="form-label">State</label>
                                        <select name="state_id" id="filterState" class="form-select">
                                            <option value="">All States</option>
                                            @foreach ($states as $state)
                                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-xl-3 col-lg-4 col-md-6">
                                        <label class="form-label">City</label>
                                        <select name="city_id" id="filterCity" class="form-select" disabled>
                                            <option value="">Select state first</option>
                                        </select>
                                    </div>
                                    <div class="col-xl-2 col-lg-4 col-md-6">
                                        <label class="form-label">Pincode</label>
                                        <input type="text" name="pincode" id="filterPincode" class="form-control"
                                            maxlength="6" inputmode="numeric" placeholder="e.g. 302001">
                                    </div>
                                    <div class="col-xl-2 col-lg-4 col-md-6">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="">All</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-xl-2 col-lg-4 col-md-6">
                                        <label class="form-label">Profile Status</label>
                                        <select name="profile_status" class="form-select">
                                            <option value="">All</option>
                                            <option value="Approved">Approved</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Rejected">Rejected</option>
                                            <option value="not_created">Not Created</option>
                                        </select>
                                    </div>
                                    <div class="col-xl-2 col-lg-4 col-md-6">
                                        <label class="form-label">Profile Complete</label>
                                        <select name="profile_complete" class="form-select">
                                            <option value="">All</option>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="col-xl-2 col-lg-4 col-md-6">
                                        <label class="form-label">Mobile Verified</label>
                                        <select name="mobile_verified" class="form-select">
                                            <option value="">All</option>
                                            <option value="1">Verified</option>
                                            <option value="0">Not Verified</option>
                                        </select>
                                    </div>
                                    <div class="col-xl-2 col-lg-4 col-md-6">
                                        <label class="form-label">From Date</label>
                                        <input type="date" name="from_date" id="filterFromDate" class="form-control">
                                    </div>
                                    <div class="col-xl-2 col-lg-4 col-md-6">
                                        <label class="form-label">To Date</label>
                                        <input type="date" name="to_date" id="filterToDate" class="form-control">
                                    </div>
                                    <div class="col-xl-2 col-lg-4 col-md-6 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-danger w-100" id="resetMemberFilters">
                                            <i class="ti ti-refresh me-1"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card-body">
                        <div id="member_table_wrapper">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .member-tabs .nav-link {
            cursor: pointer;
        }

        .member-filter-box {
            background: #f8f9fa;
        }

        .member-filter-box .form-label {
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .member-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .member-avatar-placeholder {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #f1f3f5;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #6c757d;
            flex-shrink: 0;
        }

        #memberTable td {
            white-space: nowrap;
        }
    </style>

    <script>
        let currentPage = 1;
        let currentSearch = '';
        let currentTab = 'all';
        let searchTimer = null;
        let pincodeTimer = null;
        let xhrTable = null;
        let xhrCities = null;

        const citiesUrl = "{{ route('admin.member.details.cities', ':id') }}";
        const exportUrl = "{{ route('admin.member.details.export') }}";

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        // Filter form ke sirf bhare hue fields
        function getFilters() {
            let filters = {};
            $.each($('#memberFilterForm').serializeArray(), function(i, f) {
                if ($.trim(f.value) !== '') {
                    filters[f.name] = $.trim(f.value);
                }
            });
            return filters;
        }

        function updateFilterBadge() {
            let count = Object.keys(getFilters()).length;
            $('#activeFilterCount').text(count).toggleClass('d-none', count === 0);
        }

        function reloadMemberTable(page = 1) {
            currentPage = page;

            if (xhrTable) {
                xhrTable.abort(); // jaldi-jaldi tab/search/filter badle to purani request cancel
            }

            updateFilterBadge();

            xhrTable = $.ajax({
                url: "{{ route('admin.member.details.table') }}",
                type: "GET",
                data: $.extend({
                    tab: currentTab,
                    page: page,
                    search: currentSearch
                }, getFilters()),
                success: function(res) {
                    $('#member_table_wrapper').html(res.html);
                    $.each(res.counts, function(key, val) {
                        $('.tab-count[data-count="' + key + '"]').text(val);
                    });
                    $('#member_table_wrapper [data-bs-toggle="tooltip"]').each(function() {
                        new bootstrap.Tooltip(this);
                    });
                },
                error: function(xhr, status) {
                    if (status !== 'abort') {
                        toastr.error('Unable to load members');
                    }
                }
            });
        }

        function loadCities(stateId) {
            let $city = $('#filterCity');

            if (xhrCities) {
                xhrCities.abort();
            }

            if (!stateId) {
                $city.html('<option value="">Select state first</option>').prop('disabled', true);
                return;
            }

            $city.html('<option value="">Loading...</option>').prop('disabled', true);

            xhrCities = $.get(citiesUrl.replace(':id', stateId), function(cities) {
                let html = '<option value="">All Cities</option>';
                $.each(cities, function(i, c) {
                    html += '<option value="' + c.id + '">' + $('<div>').text(c.name).html() + '</option>';
                });
                $city.html(html).prop('disabled', false);
            }).fail(function(xhr, status) {
                if (status !== 'abort') {
                    toastr.error('Unable to load cities');
                    $city.html('<option value="">All Cities</option>').prop('disabled', false);
                }
            });
        }

        $(document).ready(function() {
            reloadMemberTable(1);
        });

        // TABS
        $(document).on('click', '.member-tabs .nav-link', function(e) {
            e.preventDefault();
            $('.member-tabs .nav-link').removeClass('active');
            $(this).addClass('active');
            currentTab = $(this).data('tab');
            reloadMemberTable(1);
        });

        // SEARCH (debounced)
        $(document).on('keyup', '#memberSearch', function() {
            let val = $(this).val();
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                currentSearch = val;
                reloadMemberTable(1);
            }, 400);
        });

        // STATE -> CITY
        $(document).on('change', '#filterState', function() {
            loadCities($(this).val());
            reloadMemberTable(1);
        });

        // Baaki selects / dates
        $(document).on('change', '#memberFilterForm select:not(#filterState), #memberFilterForm input[type="date"]',
            function() {
                let from = $('#filterFromDate').val();
                let to = $('#filterToDate').val();
                if (from && to && from > to) {
                    toastr.warning('From Date, To Date se pehle honi chahiye');
                    $(this).val('');
                    return;
                }
                reloadMemberTable(1);
            });

        // PINCODE (sirf digits, debounced)
        $(document).on('input', '#filterPincode', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
            clearTimeout(pincodeTimer);
            pincodeTimer = setTimeout(function() {
                reloadMemberTable(1);
            }, 400);
        });

        $(document).on('submit', '#memberFilterForm', function(e) {
            e.preventDefault();
        });

        // RESET
        $(document).on('click', '#resetMemberFilters', function() {
            $('#memberFilterForm')[0].reset();
            loadCities('');
            $('#memberSearch').val('');
            currentSearch = '';
            reloadMemberTable(1);
        });

        // EXPORT (current tab + search + filters)
        $(document).on('click', '#exportMemberBtn', function() {
            let params = $.extend({
                tab: currentTab,
                search: $.trim($('#memberSearch').val())
            }, getFilters());

            if (!params.search) {
                delete params.search;
            }

            toastr.info('Excel file ban rahi hai, download thodi der me shuru hoga...');
            window.location.href = exportUrl + '?' + $.param(params);
        });

        // PAGINATION
        $(document).on('click', '.paginateLink', function(e) {
            e.preventDefault();
            reloadMemberTable($(this).data('page'));
        });

        // USER ACTIVE / INACTIVE
        $(document).on('change', '.user-status-toggle', function() {
            let $cb = $(this);
            let userId = $cb.data('user-id');
            let status = $cb.is(':checked') ? 1 : 0;

            Swal.fire({
                title: 'Are you sure you want to ' + (status ? 'activate' : 'deactivate') + ' this member?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Confirm',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (!result.isConfirmed) {
                    $cb.prop('checked', !status);
                    return;
                }
                $cb.prop('disabled', true);
                $.ajax({
                    url: "{{ route('admin.member.details.status') }}",
                    type: "POST",
                    data: {
                        user_id: userId,
                        status: status
                    },
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.message);
                            $cb.next('label').text(status ? 'Active' : 'Inactive');
                        } else {
                            toastr.error(res.message);
                            $cb.prop('checked', !status);
                        }
                    },
                    error: function() {
                        toastr.error('Something went wrong.');
                        $cb.prop('checked', !status);
                    },
                    complete: function() {
                        $cb.prop('disabled', false);
                    }
                });
            });
        });

        // PROFILE APPROVE / REJECT
        $(document).on('change', '.approval-toggle', function() {
            let $cb = $(this);
            let profileId = $cb.data('profile-id');
            let approve = $cb.is(':checked');

            let swalOptions = approve ? {
                title: 'Approve this profile?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Approve',
                cancelButtonText: 'Cancel',
            } : {
                title: 'Reject this profile?',
                input: 'textarea',
                inputLabel: 'Rejection reason',
                inputPlaceholder: 'Reason likhein...',
                showCancelButton: true,
                confirmButtonText: 'Reject',
                confirmButtonColor: '#d33',
                cancelButtonText: 'Cancel',
                inputValidator: (value) => {
                    if (!value || value.trim() === '') {
                        return 'Rejection reason zaroori hai!';
                    }
                }
            };

            Swal.fire(swalOptions).then((result) => {
                if (!result.isConfirmed) {
                    $cb.prop('checked', !approve);
                    return;
                }
                $cb.prop('disabled', true);
                $.ajax({
                    url: "{{ route('admin.member.details.profile.status') }}",
                    type: "POST",
                    data: {
                        profile_id: profileId,
                        status: approve ? 'Approved' : 'Rejected',
                        reason: approve ? null : result.value
                    },
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.message);
                            reloadMemberTable(currentPage);
                        } else {
                            toastr.error(res.message);
                            $cb.prop('checked', !approve).prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr
                            .responseJSON.message :
                            'Something went wrong.';
                        toastr.error(msg);
                        $cb.prop('checked', !approve).prop('disabled', false);
                    }
                });
            });
        });

        // SOFT DELETE
        $(document).on('click', '.deleteMemberBtn', function() {
            let $btn = $(this);
            let id = $btn.data('id');

            Swal.fire({
                title: "Are you sure?",
                text: "This member will be deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }
                $btn.prop('disabled', true);
                $.ajax({
                    url: "{{ url('admin/member-details') }}/" + id,
                    type: "POST",
                    data: { _method: "DELETE" },
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.message);
                            reloadMemberTable(currentPage);
                        } else {
                            toastr.error(res.message);
                            $btn.prop('disabled', false);
                        }
                    },
                    error: function() {
                        toastr.error('Something went wrong.');
                        $btn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || Downline List')

@section('content')
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Downline List</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="ti ti-smart-home"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">Members</li>
                        <li class="breadcrumb-item active">Downline</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2 mt-2 mt-md-0">
                @if ($root->sponsor_id && $root->role !== 'Admin')
                    <a href="{{ route('admin.member.downline', $root->sponsor_id) }}" class="btn btn-light">
                        <i class="ti ti-arrow-up"></i> Sponsor Downline
                    </a>
                @endif
                <a href="{{ route('admin.member.tree', $root->unique_id) }}" class="btn btn-primary">
                    <i class="ti ti-hierarchy-2"></i> Tree View
                </a>
            </div>
        </div>

        <p class="text-muted mb-3">
            Downline of <strong>{{ $root->full_name }}</strong> ({{ $root->unique_id }})
        </p>

        {{-- COUNTS --}}
        <div class="row">
            <div class="col-md-4 col-sm-6">
                <div class="card">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Total Downline</p>
                            <h3 class="mb-0" id="count_total">0</h3>
                        </div>
                        <span class="count-icon bg-primary"><i class="ti ti-users"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="card">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Non Farmer</p>
                            <h3 class="mb-0" id="count_non_farmer">0</h3>
                        </div>
                        <span class="count-icon bg-info"><i class="ti ti-user"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="card">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Farmer</p>
                            <h3 class="mb-0" id="count_farmer">0</h3>
                        </div>
                        <span class="count-icon bg-success"><i class="ti ti-plant-2"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-md-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-2 mb-md-0">Downline Members</h4>
                        <div class="d-flex flex-wrap gap-2">
                            <select id="levelFilter" class="form-select" style="width: 130px;">
                                <option value="">All Levels</option>
                            </select>
                            <select id="roleFilter" class="form-select" style="width: 150px;">
                                <option value="">All Roles</option>
                                <option value="Non Farmer">Non Farmer</option>
                                <option value="Farmer">Farmer</option>
                            </select>
                            <input type="text" id="downlineSearch" class="form-control" style="width: 250px;"
                                placeholder="Search name, ID, mobile...">
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="downline_table_wrapper">
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
        .count-icon {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }
    </style>

    <script>
        const ROOT_ID = @json($root->unique_id);
        let currentPage = 1;
        let currentSearch = '';
        let currentLevel = '';
        let currentRole = '';
        let searchTimer = null;

        function fillLevels(maxLevel) {
            let $sel = $('#levelFilter');
            if ($sel.find('option').length - 1 === maxLevel) {
                return;
            }
            let selected = $sel.val();
            $sel.find('option:not(:first)').remove();
            for (let i = 1; i <= maxLevel; i++) {
                $sel.append('<option value="' + i + '">Level ' + i + '</option>');
            }
            $sel.val(selected && selected <= maxLevel ? selected : '');
        }

        function reloadDownlineTable(page = 1) {
            currentPage = page;

            $.ajax({
                url: "{{ route('admin.member.downline.table') }}",
                type: "GET",
                data: {
                    unique_id: ROOT_ID,
                    page: page,
                    search: currentSearch,
                    level: currentLevel,
                    role: currentRole
                },
                success: function(res) {
                    $('#downline_table_wrapper').html(res.html);
                    $('#count_total').text(res.counts.total);
                    $('#count_non_farmer').text(res.counts.non_farmer);
                    $('#count_farmer').text(res.counts.farmer);
                    fillLevels(res.max_level);
                },
                error: function() {
                    toastr.error('Unable to load downline');
                }
            });
        }

        $(document).ready(function() {
            reloadDownlineTable(1);
        });

        // SEARCH (debounced)
        $(document).on('keyup', '#downlineSearch', function() {
            let val = $(this).val();
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                currentSearch = val;
                reloadDownlineTable(1);
            }, 400);
        });

        // FILTERS
        $(document).on('change', '#levelFilter', function() {
            currentLevel = $(this).val();
            reloadDownlineTable(1);
        });

        $(document).on('change', '#roleFilter', function() {
            currentRole = $(this).val();
            reloadDownlineTable(1);
        });

        // PAGINATION
        $(document).on('click', '.paginateLink', function(e) {
            e.preventDefault();
            reloadDownlineTable($(this).data('page'));
        });
    </script>
@endsection
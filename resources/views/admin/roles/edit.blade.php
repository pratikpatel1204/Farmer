@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || Edit Roles')
@section('content')

    @php
        $groupedPermissions = $permissions->groupBy(fn($p) => $p->group_name ?: 'Other')->sortKeys();
        if ($groupedPermissions->has('Other')) {
            $otherGroup = $groupedPermissions->pull('Other');
            $groupedPermissions->put('Other', $otherGroup);
        }
    @endphp

    <div class="content">

        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Edit Role</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="javascript:void(0);"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">Roles</li>
                        <li class="breadcrumb-item active">Edit Role & Permissions</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h5 class="card-title mb-0">Modify Role & Permissions</h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-success" id="checkAll">
                        Check All
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" id="uncheckAll">
                        Uncheck All
                    </button>
                </div>
            </div>

            <div class="card-body">

                <form id="updateRoleForm">
                    @csrf
                    <input type="hidden" name="id" value="{{ $role->id }}">

                    <div class="row">

                        <div class="col-lg-6">
                            <label class="form-label">Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $role->name }}" readonly>
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label">Search Permission</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-search"></i></span>
                                <input type="text" id="permissionSearch" class="form-control"
                                    placeholder="Type to find a permission, e.g. &quot;View Leads&quot;">
                                <button type="button" class="btn btn-outline-secondary d-none" id="clearSearch">
                                    <i class="ti ti-x"></i>
                                </button>
                            </div>
                            <small class="text-muted" id="searchHint"></small>
                        </div>

                        <div class="col-lg-12 mt-4">
                            <label class="form-label d-block mb-2">Permissions (grouped by module)</label>

                            <div id="permissionGroups">
                                @foreach ($groupedPermissions as $groupName => $groupPermissions)
                                    @php
                                        $checkedInGroup = $groupPermissions
                                            ->filter(fn($p) => in_array($p->id, $rolePermissions))
                                            ->count();
                                        $totalInGroup = $groupPermissions->count();
                                    @endphp
                                    <div class="permission-group mb-2" data-group-name="{{ strtolower($groupName) }}">
                                        <div class="perm-group-header d-flex align-items-center justify-content-between">
                                            <div class="d-flex gap-4">
                                                <span class="perm-group-title">{{ $groupName }}</span>
                                                <span
                                                    class="badge bg-light text-dark group-count">{{ $checkedInGroup }}/{{ $totalInGroup }}</span>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input group-check-all">
                                                <label class="form-check-label fw-semibold">
                                                    Select all in {{ $groupName }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="perm-group-body">
                                            <div class="row">
                                                @foreach ($groupPermissions as $permission)
                                                    <div class="col-md-3 mb-2 permission-item"
                                                        data-perm-name="{{ strtolower($permission->name) }}">
                                                        <div class="form-check">
                                                            <input class="form-check-input permissionCheck" type="checkbox"
                                                                name="permissions[]" value="{{ $permission->name }}"
                                                                {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                                            <label class="form-check-label">
                                                                {{ $permission->name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <p class="text-muted mt-2 mb-0" id="noResultsMsg" style="display:none;">
                                No permission matches your search.
                            </p>
                        </div>

                    </div>

                    <div class="text-end mt-4">
                        @can('View Roles')
                            <a href="{{ route('admin.roles.list') }}" class="btn btn-secondary">Cancel</a>
                        @endcan

                        <button type="submit" class="btn btn-primary" id="saveBtn">
                            <span class="btn-text">Update Role</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <style>
        .permission-group {
            border: 1px solid #e5eaee;
            border-radius: 8px;
            overflow: hidden;
        }

        .perm-group-header {
            background-color: #eef4f8;
            color: #16537e;
            padding: 10px 16px;
        }

        .perm-group-title {
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .perm-group-body {
            padding: 14px 16px;
            background: #fff;
        }

        .permission-group.d-none-search {
            display: none !important;
        }

        .permission-item.d-none-search {
            display: none !important;
        }

        .permission-item mark {
            background: #fff2c9;
            padding: 0 1px;
            border-radius: 2px;
        }

        .group-count {
            font-size: 11px;
            font-weight: 600;
        }
    </style>

    <script>
        $(document).ready(function() {

            function updateGroupCount($group) {
                var total = $group.find('.permissionCheck').length;
                var checked = $group.find('.permissionCheck:checked').length;
                $group.find('.group-count').text(checked + '/' + total);
                $group.find('.group-check-all').prop('checked', total > 0 && checked === total);
            }

            function refreshAllCounts() {
                $('.permission-group').each(function() {
                    updateGroupCount($(this));
                });
            }

            refreshAllCounts();

            // Keep each group's running count in sync as checkboxes change
            $(document).on('change', '.permissionCheck', function() {
                updateGroupCount($(this).closest('.permission-group'));
            });

            // Per-group "select all"
            $(document).on('change', '.group-check-all', function() {
                var checked = $(this).is(':checked');
                $(this).closest('.permission-group').find('.permissionCheck').prop('checked', checked);
                updateGroupCount($(this).closest('.permission-group'));
            });

            // Global check all / uncheck all
            $('#checkAll').on('click', function() {
                $('.permissionCheck').prop('checked', true);
                refreshAllCounts();
            });
            $('#uncheckAll').on('click', function() {
                $('.permissionCheck').prop('checked', false);
                refreshAllCounts();
            });

            // ---------------- Live search ----------------
            var $search = $('#permissionSearch');
            var $clear = $('#clearSearch');
            var $hint = $('#searchHint');
            var $noResults = $('#noResultsMsg');

            function highlight(text, term) {
                if (!term) return text;
                var re = new RegExp('(' + term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'ig');
                return text.replace(re, '<mark>$1</mark>');
            }

            $search.on('keyup', function() {
                var term = $(this).val().trim().toLowerCase();
                $clear.toggleClass('d-none', term === '');

                if (term === '') {
                    $('.permission-item, .permission-group').removeClass('d-none-search');
                    $('.permission-item label').each(function() {
                        $(this).text($(this).text());
                    });
                    $noResults.hide();
                    $hint.text('');
                    return;
                }

                var visibleCount = 0;

                $('.permission-group').each(function() {
                    var $group = $(this);
                    var groupHasMatch = false;

                    $group.find('.permission-item').each(function() {
                        var name = $(this).data('perm-name').toString();
                        var isMatch = name.indexOf(term) !== -1;
                        $(this).toggleClass('d-none-search', !isMatch);

                        var $label = $(this).find('label');
                        var original = $(this).find('input').next('label').text();
                        if (isMatch) {
                            groupHasMatch = true;
                            visibleCount++;
                            $label.html(highlight(original, term));
                        } else {
                            $label.html(original);
                        }
                    });

                    $group.toggleClass('d-none-search', !groupHasMatch);
                });

                $hint.text(visibleCount + ' permission(s) found');
                $noResults.toggle(visibleCount === 0);
            });

            $clear.on('click', function() {
                $search.val('').trigger('keyup');
            });

            $('#updateRoleForm').on('submit', function(e) {
                e.preventDefault();

                $("#saveBtn").attr('disabled', true);
                $(".spinner-border").removeClass('d-none');

                $.ajax({
                    url: "{{ route('admin.roles.update') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {

                        toastr.success("Role updated successfully");

                        $("#saveBtn").attr('disabled', false);
                        $(".spinner-border").addClass('d-none');

                        setTimeout(() => {
                            window.location.href = "{{ route('admin.roles.list') }}";
                        }, 1000);
                    },
                    error: function(xhr) {
                        let error = xhr.responseJSON.message ?? "Something went wrong!";
                        toastr.error(error);

                        $("#saveBtn").attr('disabled', false);
                        $(".spinner-border").addClass('d-none');
                    }
                });
            });

        });
    </script>
@endsection

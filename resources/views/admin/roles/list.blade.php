@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || Roles List')
@section('content')
    <div class="content">

        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Roles List</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="javascript:void(0);"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">Roles</li>
                        <li class="breadcrumb-item active" aria-current="page">Role List</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Role List</h4>
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="rolesTable">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;">Role Name</th>
                                        <th style="width: 30%;">Permissions</th>
                                        <th style="width: 30%;">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($roles as $role)
                                        @php
                                            $groupedRolePerms = $role->permissions
                                                ->groupBy(fn($p) => $p->group_name ?: 'Other')
                                                ->sortKeys();

                                            if ($groupedRolePerms->has('Other')) {
                                                $otherPerms = $groupedRolePerms->pull('Other');
                                                $groupedRolePerms->put('Other', $otherPerms);
                                            }

                                            $totalPermCount = $role->permissions->count();

                                            // Build a small payload for the modal (name + count per group)
                                            $modalPayload = $groupedRolePerms
                                                ->map(function ($perms, $groupName) {
                                                    return [
                                                        'group' => $groupName,
                                                        'items' => $perms->pluck('name')->values(),
                                                    ];
                                                })
                                                ->values();
                                        @endphp
                                        <tr>
                                            <td class="fw-semibold">{{ ucfirst($role->name) }}</td>

                                            <td>
                                                @if ($totalPermCount === 0)
                                                    <span class="text-muted fst-italic">None</span>
                                                @else
                                                    <span class="badge perm-count-badge">
                                                        {{ $totalPermCount }} permission{{ $totalPermCount > 1 ? 's' : '' }}
                                                    </span>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-success p-1 view-permissions-btn"
                                                        data-role-name="{{ ucfirst($role->name) }}"
                                                        data-permissions='@json($modalPayload)'>
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                @endif
                                            </td>

                                            <td class="text-nowrap">
                                                @can('Edit Roles')
                                                    <a href="{{ route('admin.roles.edit', $role->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="ti ti-edit"></i> Edit
                                                    </a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Single shared modal, filled dynamically per role on click --}}
    <div class="modal fade" id="permissionsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Permissions — <span id="permModalRoleName"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="permModalBody">
                    {{-- filled via JS --}}
                </div>
            </div>
        </div>
    </div>

    <style>
        .perm-count-badge {
            background-color: #eaf6ec;
            color: #1c7c30;
            border: 1px solid #cdebd1;
            font-weight: 600;
            font-size: 12px;
            padding: 5px 10px;
        }

        #rolesTable td {
            vertical-align: middle;
        }

        .perm-modal-group {
            margin-bottom: 16px;
        }

        .perm-modal-group-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .3px;
            color: #16537e;
            border-bottom: 1px dashed #dfe6ea;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }

        .perm-modal-badge {
            background-color: #eaf6ec;
            color: #1c7c30;
            border: 1px solid #cdebd1;
            font-weight: 500;
            font-size: 11.5px;
            padding: 5px 8px;
            display: inline-block;
            margin: 3px 4px 3px 0;
        }
    </style>

    {{-- Datatable + modal-fill script --}}
    <script>
        $(document).ready(function() {
            $('#rolesTable').DataTable({
                columnDefs: [{
                    orderable: false,
                    targets: [1, 2]
                }]
            });

            $(document).on('click', '.view-permissions-btn', function() {
                var roleName = $(this).data('role-name');
                var groups = $(this).data('permissions'); // already parsed as array by jQuery

                $('#permModalRoleName').text(roleName);

                var html = '';
                if (!groups || groups.length === 0) {
                    html = '<p class="text-muted fst-italic mb-0">No permissions assigned.</p>';
                } else {
                    groups.forEach(function(g) {
                        html += '<div class="perm-modal-group">';
                        html += '<div class="perm-modal-group-title">' + g.group + ' (' + g.items
                            .length + ')</div>';
                        html += '<div>';
                        g.items.forEach(function(name) {
                            html += '<span class="perm-modal-badge">' + name + '</span>';
                        });
                        html += '</div></div>';
                    });
                }

                $('#permModalBody').html(html);
                var modal = new bootstrap.Modal(document.getElementById('permissionsModal'));
                modal.show();
            });
        });
    </script>
@endsection
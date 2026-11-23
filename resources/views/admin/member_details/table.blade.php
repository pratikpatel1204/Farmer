<div class="table-responsive">
    <table class="table table-bordered align-middle" id="memberTable">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Action</th>
                <th>Name</th>
                <th>Member Details</th>
                <th>Sponsor</th>
                <th>Contact Details</th>
                <th>Address</th>
                <th class="text-center">Total Product</th>
                <th class="text-center">Profile Complete</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($members as $member)
                @php $profile = $member->profile; @endphp
                <tr>
                    <td>{{ $loop->iteration + ($members->currentPage() - 1) * $members->perPage() }}</td>

                    {{-- ACTION --}}
                    <td>
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex gap-1">
                                @if ($profile)
                                    <a href="{{ route('admin.member.details.show', $member->id) }}"
                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="View Profile">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                @endif
                                <a href="{{ route('admin.member.details.edit', $member->id) }}"
                                    class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </a>
                            </div>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.member.details.login', $member->unique_id) }}"
                                    class="btn btn-sm btn-secondary" data-bs-toggle="tooltip" title="Login"
                                    target="_blank">
                                    <i class="ti ti-login"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger deleteMemberBtn"
                                    data-id="{{ $member->id }}" data-bs-toggle="tooltip" title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </div>
                    </td>

                    {{-- NAME --}}
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if ($member->profile_image)
                                <img src="{{ asset($member->profile_image) }}" alt="{{ $member->full_name }}"
                                    class="member-avatar">
                            @else
                                <span class="member-avatar-placeholder"><i class="ti ti-user"></i></span>
                            @endif
                            <div>
                                <div class="fw-semibold">{{ $member->full_name }}</div>
                                @if ($member->role === 'Farmer')
                                    <span class="badge bg-success">Farmer</span>
                                @else
                                    <span class="badge bg-info">{{ $member->role }}</span>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- MEMBER DETAILS --}}
                    <td>
                        <div class="d-flex align-items-center mb-1">
                            <i class="ti ti-fingerprint me-1 text-primary"></i>
                            {{ $member->unique_id }}
                        </div>
                        <div class="d-flex align-items-center mb-1" title="Password">
                            <i class="ti ti-key me-1 text-danger"></i>
                            {{ $member->temp_password ?? '-' }}
                        </div>
                    </td>

                    {{-- SPONSOR --}}
                    <td>
                        @if ($member->sponsor_id)
                            <div class="fw-semibold">{{ $member->sponsor_name ?? '-' }}</div>
                            <small class="text-muted">{{ $member->sponsor_id }}</small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    {{-- CONTACT --}}
                    <td>
                        <div class="d-flex align-items-center mb-1">
                            <i class="ti ti-mail me-2 text-primary"></i>
                            {{ $member->email ?? '-' }}
                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <i class="ti ti-phone me-2 text-success"></i>
                            <span class="me-1">{{ $member->mobile ?? '-' }}</span>
                            @if ((int) $member->mobile_verified === 1)
                                <i class="ti ti-circle-check text-primary ms-1" title="Verified"></i>
                            @else
                                <i class="ti ti-circle-x text-danger ms-1" title="Not verified"></i>
                            @endif
                        </div>
                    </td>

                    {{-- ADDRESS --}}
                    @php
                        $cityState = collect([$member->city_name, $member->state_name])->filter()->implode(', ');
                    @endphp
                    <td style="white-space: normal; min-width: 180px;">
                        @if ($cityState)
                            <div class="d-flex align-items-center mb-1 fw-medium">
                                <i class="ti ti-building-community me-1 text-primary"></i>
                                {{ $cityState }}
                            </div>
                        @endif
                        @if ($member->area || $member->pincode)
                            <div class="d-flex align-items-center mb-1">
                                <i class="ti ti-map-pin me-1 text-danger"></i>
                                {{ collect([$member->area, $member->pincode])->filter()->implode(' - ') }}
                            </div>
                        @endif
                        @if ($member->address)
                            <small class="text-muted" title="{{ $member->address }}">
                                {{ \Illuminate\Support\Str::limit($member->address, 40) }}
                            </small>
                        @endif
                        @if (!$cityState && !$member->area && !$member->pincode && !$member->address)
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    {{-- TOTAL PRODUCT --}}
                    <td class="text-center">
                        <span class="badge bg-info">{{ $member->products_count ?? 0 }}</span>
                    </td>

                    {{-- PROFILE COMPLETE --}}
                    <td class="text-center">
                        @if ($member->profile_complete)
                            <span class="badge bg-success">Yes</span>
                        @else
                            <span class="badge bg-danger">No</span>
                        @endif
                    </td>

                    {{-- STATUS --}}
                    <td>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input user-status-toggle" type="checkbox" role="switch"
                                id="userStatusSwitch{{ $member->id }}" data-user-id="{{ $member->id }}"
                                {{ (int) $member->status === 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="userStatusSwitch{{ $member->id }}">
                                {{ (int) $member->status === 1 ? 'Active' : 'Inactive' }}
                            </label>
                        </div>

                        @if (!$profile)
                            <div class="alert alert-warning mt-1 p-1 mb-0" role="alert">
                                Profile <br> not created.
                            </div>
                        @else
                            <div class="form-check form-switch">
                                <input class="form-check-input approval-toggle" type="checkbox" role="switch"
                                    id="approveSwitch{{ $profile->id }}" data-profile-id="{{ $profile->id }}"
                                    {{ $profile->status === 'Approved' ? 'checked' : '' }}>
                                <label class="form-check-label" for="approveSwitch{{ $profile->id }}">
                                    @if ($profile->status === 'Approved')
                                        <span class="text-success">Approved</span>
                                    @elseif ($profile->status === 'Rejected')
                                        <span class="text-danger">Rejected</span>
                                    @else
                                        <span class="text-warning">Pending</span>
                                    @endif
                                </label>
                            </div>
                            @if ($profile->status === 'Rejected' && $profile->reject_reason)
                                <small class="text-danger d-block mt-1" style="white-space: normal; max-width: 180px;">
                                    <i class="ti ti-info-circle"></i> {{ $profile->reject_reason }}
                                </small>
                            @endif
                        @endif
                    </td>

                    <td>{{ optional($member->created_at)->format('d-m-Y') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">No members found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-md-flex justify-content-between align-items-center mt-3">
    <div class="text-muted mb-2 mb-md-0">
        Showing {{ $members->firstItem() ?? 0 }} to {{ $members->lastItem() ?? 0 }} of {{ $members->total() }}
        entries
    </div>
    <nav>
        <ul class="pagination mb-0">
            @if ($members->onFirstPage())
                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
            @else
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="{{ $members->currentPage() - 1 }}">&laquo;</a>
                </li>
            @endif

            @foreach (range(1, max(1, $members->lastPage())) as $page)
                @if ($page == $members->currentPage())
                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                @elseif ($page == 1 || $page == $members->lastPage() || abs($page - $members->currentPage()) <= 2)
                    <li class="page-item">
                        <a class="page-link paginateLink" href="#"
                            data-page="{{ $page }}">{{ $page }}</a>
                    </li>
                @elseif ($page == $members->currentPage() - 3 || $page == $members->currentPage() + 3)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
            @endforeach

            @if ($members->hasMorePages())
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="{{ $members->currentPage() + 1 }}">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
            @endif
        </ul>
    </nav>
</div>
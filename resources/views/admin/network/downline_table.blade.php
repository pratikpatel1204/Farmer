<div class="table-responsive">
    <table class="table table-bordered align-middle" id="downlineTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Member</th>
                <th width="110">Role</th>
                <th width="80">Level</th>
                <th>Sponsor</th>
                <th>Mobile</th>
                <th width="100">Status</th>
                <th width="110">Joined</th>
                <th width="110">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($members as $member)
                <tr>
                    <td>{{ $loop->iteration + ($members->currentPage() - 1) * $members->perPage() }}</td>
                    <td>
                        <div class="fw-semibold">{{ $member->full_name }}</div>
                        <small class="text-muted">{{ $member->unique_id }}</small>
                    </td>
                    <td>
                        @if ($member->role === 'Farmer')
                            <span class="badge bg-success">Farmer</span>
                        @else
                            <span class="badge bg-info">{{ $member->role }}</span>
                        @endif
                    </td>
                    <td><span class="badge bg-secondary">Level {{ $member->level }}</span></td>
                    <td>
                        @if ($member->sponsor_id)
                            <div>{{ $member->sponsor_name ?? '-' }}</div>
                            <small class="text-muted">{{ $member->sponsor_id }}</small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $member->mobile ?? '-' }}</td>
                    <td>
                        @if ((int) $member->status === 1)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </td>
                    <td>{{ optional($member->created_at)->format('d-m-Y') }}</td>
                    <td>
                        <a href="{{ route('admin.member.tree', $member->unique_id) }}" class="btn btn-sm btn-info"
                            title="Tree View">
                            <i class="ti ti-hierarchy-2"></i>
                        </a>
                        <a href="{{ route('admin.member.downline', $member->unique_id) }}"
                            class="btn btn-sm btn-primary" title="Downline">
                            <i class="ti ti-users"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">No downline members found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-md-flex justify-content-between align-items-center mt-3">
    <div class="text-muted mb-2 mb-md-0">
        Showing {{ $members->firstItem() ?? 0 }} to {{ $members->lastItem() ?? 0 }} of {{ $members->total() }} entries
    </div>
    <nav>
        <ul class="pagination mb-0">
            {{-- Previous --}}
            @if ($members->onFirstPage())
                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
            @else
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="{{ $members->currentPage() - 1 }}">&laquo;</a>
                </li>
            @endif

            {{-- Page numbers --}}
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

            {{-- Next --}}
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

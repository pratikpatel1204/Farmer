<div class="table-responsive">
    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th width="50">#</th>
                <th>Title</th>
                <th>Description</th>
                <th width="110">Default</th>
                <th width="120">Status</th>
                <th width="140">Created</th>
                <th width="110">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($notifications as $notification)
                <tr>
                    <td>{{ $loop->iteration + ($notifications->currentPage() - 1) * $notifications->perPage() }}</td>
                    <td>
                        {{ $notification->title }}
                        @if ($notification->is_default)
                            <span class="badge bg-warning text-dark ms-1"><i class="ti ti-pin"></i> Pinned</span>
                        @endif
                    </td>
                    <td>
                        {{ \Illuminate\Support\Str::limit(trim(html_entity_decode(strip_tags($notification->description))), 70) }}
                        {{-- Edit modal ke CKEditor me bharne ke liye pura HTML --}}
                        <template class="notification-html">{!! $notification->description !!}</template>
                    </td>
                    <td>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input notificationDefaultToggle" type="checkbox" role="switch"
                                data-id="{{ $notification->id }}" @checked($notification->is_default)
                                title="Pin on top for members">
                        </div>
                    </td>
                    <td>
                        <div class="form-check form-switch mb-0 d-flex align-items-center gap-2">
                            <input class="form-check-input notificationStatusToggle" type="checkbox" role="switch"
                                data-id="{{ $notification->id }}" @checked($notification->status === 'Active')>
                            <span class="badge {{ $notification->status === 'Active' ? 'bg-success' : 'bg-danger' }}">
                                {{ $notification->status }}
                            </span>
                        </div>
                    </td>
                    <td>{{ $notification->created_at?->format('d M Y, h:i A') }}</td>
                    <td>
                        <button class="btn btn-sm btn-info editNotificationBtn" data-id="{{ $notification->id }}"
                            data-title="{{ $notification->title }}" data-status="{{ $notification->status }}"
                            data-default="{{ $notification->is_default ? 1 : 0 }}">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteNotificationBtn" data-id="{{ $notification->id }}">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No notifications found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($notifications->hasPages())
    <div class="d-md-flex justify-content-between align-items-center mt-3">
        <div class="text-muted mb-2 mb-md-0">
            Showing {{ $notifications->firstItem() ?? 0 }} to {{ $notifications->lastItem() ?? 0 }} of
            {{ $notifications->total() }} entries
        </div>
        <nav>
            <ul class="pagination mb-0">
                @if ($notifications->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                @else
                    <li class="page-item">
                        <a class="page-link paginateLink" href="#" data-page="{{ $notifications->currentPage() - 1 }}">&laquo;</a>
                    </li>
                @endif

                @foreach (range(1, $notifications->lastPage()) as $page)
                    @if ($page == $notifications->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                    @elseif ($page == 1 || $page == $notifications->lastPage() || abs($page - $notifications->currentPage()) <= 2)
                        <li class="page-item">
                            <a class="page-link paginateLink" href="#" data-page="{{ $page }}">{{ $page }}</a>
                        </li>
                    @elseif ($page == $notifications->currentPage() - 3 || $page == $notifications->currentPage() + 3)
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    @endif
                @endforeach

                @if ($notifications->hasMorePages())
                    <li class="page-item">
                        <a class="page-link paginateLink" href="#" data-page="{{ $notifications->currentPage() + 1 }}">&raquo;</a>
                    </li>
                @else
                    <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                @endif
            </ul>
        </nav>
    </div>
@endif

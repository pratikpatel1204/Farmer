<div class="table-responsive">
    <table class="table table-bordered align-middle" id="popupTable">
        <thead>
            <tr>
                <th>#</th>
                <th width="110">Image</th>
                <th>Title</th>
                <th>Description</th>
                <th>Button URL</th>
                <th width="100">Status</th>
                <th width="120">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($popups as $popup)
                <tr id="row_{{ $popup->id }}">
                    <td>{{ $loop->iteration + ($popups->currentPage() - 1) * $popups->perPage() }}</td>
                    <td>
                        @if ($popup->image)
                            <img src="{{ asset($popup->image) }}" alt="{{ $popup->title }}"
                                style="width: 90px; height: 60px; object-fit: cover; border-radius: 8px;">
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $popup->title }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($popup->short_description, 50) }}</td>
                    <td>
                        @if ($popup->btn_url)
                            <a href="{{ $popup->btn_url }}" target="_blank" rel="noopener noreferrer">
                                {{ \Illuminate\Support\Str::limit($popup->btn_url, 30) }}
                            </a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if ($popup->status === 'Active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info editPopupBtn" data-id="{{ $popup->id }}"
                            data-title="{{ $popup->title }}" data-description="{{ $popup->short_description }}"
                            data-btn-url="{{ $popup->btn_url }}" data-status="{{ $popup->status }}"
                            data-image="{{ $popup->image ? asset($popup->image) : '' }}">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deletePopupBtn" data-id="{{ $popup->id }}">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No popups found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-md-flex justify-content-between align-items-center mt-3">
    <div class="text-muted mb-2 mb-md-0">
        Showing {{ $popups->firstItem() ?? 0 }} to {{ $popups->lastItem() ?? 0 }} of {{ $popups->total() }} entries
    </div>
    <nav>
        <ul class="pagination mb-0">
            {{-- Previous --}}
            @if ($popups->onFirstPage())
                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
            @else
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="{{ $popups->currentPage() - 1 }}">&laquo;</a>
                </li>
            @endif

            {{-- Page numbers --}}
            @foreach (range(1, max(1, $popups->lastPage())) as $page)
                @if ($page == $popups->currentPage())
                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                @elseif ($page == 1 || $page == $popups->lastPage() || abs($page - $popups->currentPage()) <= 2)
                    <li class="page-item">
                        <a class="page-link paginateLink" href="#"
                            data-page="{{ $page }}">{{ $page }}</a>
                    </li>
                @elseif ($page == $popups->currentPage() - 3 || $page == $popups->currentPage() + 3)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
            @endforeach

            {{-- Next --}}
            @if ($popups->hasMorePages())
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="{{ $popups->currentPage() + 1 }}">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
            @endif
        </ul>
    </nav>
</div>

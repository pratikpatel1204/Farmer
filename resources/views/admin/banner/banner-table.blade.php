<div class="table-responsive">
    <table class="table table-bordered align-middle" id="bannerTable">
        <thead>
            <tr>
                <th>#</th>
                <th width="140">Image</th>
                <th>Title</th>
                <th>Description</th>
                <th width="100">Status</th>
                <th width="120">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($banners as $banner)
                <tr id="row_{{ $banner->id }}">
                    <td>{{ $loop->iteration + ($banners->currentPage() - 1) * $banners->perPage() }}</td>
                    <td>
                        @if ($banner->image)
                            <img src="{{ asset($banner->image) }}" alt="{{ $banner->title }}"
                                style="width: 120px; height: 60px; object-fit: cover; border-radius: 8px;">
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $banner->title }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($banner->description, 60) }}</td>
                    <td>
                        @if ($banner->status == 'Active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info editBannerBtn" data-id="{{ $banner->id }}"
                            data-title="{{ $banner->title }}" data-description="{{ $banner->description }}"
                            data-status="{{ $banner->status }}"
                            data-image="{{ $banner->image ? asset($banner->image) : '' }}">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteBannerBtn" data-id="{{ $banner->id }}">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No banners found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-md-flex justify-content-between align-items-center mt-3">
    <div class="text-muted mb-2 mb-md-0">
        Showing {{ $banners->firstItem() ?? 0 }} to {{ $banners->lastItem() ?? 0 }} of {{ $banners->total() }}
        entries
    </div>
    <nav>
        <ul class="pagination mb-0">
            {{-- Previous --}}
            @if ($banners->onFirstPage())
                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
            @else
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="{{ $banners->currentPage() - 1 }}">&laquo;</a>
                </li>
            @endif

            {{-- Page numbers --}}
            @foreach (range(1, max(1, $banners->lastPage())) as $page)
                @if ($page == $banners->currentPage())
                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                @elseif ($page == 1 || $page == $banners->lastPage() || abs($page - $banners->currentPage()) <= 2)
                    <li class="page-item">
                        <a class="page-link paginateLink" href="#"
                            data-page="{{ $page }}">{{ $page }}</a>
                    </li>
                @elseif ($page == $banners->currentPage() - 3 || $page == $banners->currentPage() + 3)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
            @endforeach

            {{-- Next --}}
            @if ($banners->hasMorePages())
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="{{ $banners->currentPage() + 1 }}">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
            @endif
        </ul>
    </nav>
</div>
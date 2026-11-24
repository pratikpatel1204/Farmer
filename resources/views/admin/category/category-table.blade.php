<div class="table-responsive">
    <table class="table table-bordered align-middle" id="categoryTable">
        <thead>
            <tr>
                <th>#</th>
                <th width="100">Image</th>
                <th>Category Name</th>
                <th width="140">Sub Categories</th>
                <th width="120">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr id="row_{{ $category->id }}">
                    <td>{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
                    <td>
                        @if ($category->image)
                            <img src="{{ asset($category->image) }}" alt="{{ $category->name }}"
                                style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $category->name }}</td>
                    <td>
                        <a href="{{ route('admin.sub-category.index', ['category_id' => $category->id]) }}" class="badge bg-primary text-decoration-none"
                            title="View sub categories">{{ $category->sub_categories_count }}</a>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info editCategoryBtn" data-id="{{ $category->id }}"
                            data-name="{{ $category->name }}"
                            data-image="{{ $category->image ? asset($category->image) : '' }}">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteCategoryBtn" data-id="{{ $category->id }}">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No categories found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-md-flex justify-content-between align-items-center mt-3">
    <div class="text-muted mb-2 mb-md-0">
        Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of
        {{ $categories->total() }} entries
    </div>
    <nav>
        <ul class="pagination mb-0">
            {{-- Previous --}}
            @if ($categories->onFirstPage())
                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
            @else
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="{{ $categories->currentPage() - 1 }}">&laquo;</a>
                </li>
            @endif

            {{-- Page numbers --}}
            @foreach (range(1, max(1, $categories->lastPage())) as $page)
                @if ($page == $categories->currentPage())
                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                @elseif ($page == 1 || $page == $categories->lastPage() || abs($page - $categories->currentPage()) <= 2)
                    <li class="page-item">
                        <a class="page-link paginateLink" href="#"
                            data-page="{{ $page }}">{{ $page }}</a>
                    </li>
                @elseif ($page == $categories->currentPage() - 3 || $page == $categories->currentPage() + 3)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
            @endforeach

            {{-- Next --}}
            @if ($categories->hasMorePages())
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="{{ $categories->currentPage() + 1 }}">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
            @endif
        </ul>
    </nav>
</div>
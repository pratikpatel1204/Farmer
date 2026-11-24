<div class="table-responsive">
    <table class="table table-bordered" id="subCategoryTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Category Name</th>
                <th>Sub Category Name</th>
                <th width="120">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($subCategories as $sub)
                <tr id="row_{{ $sub->id }}">
                    <td>{{ $loop->iteration + ($subCategories->currentPage() - 1) * $subCategories->perPage() }}</td>
                    <td>{{ $sub->category->name ?? '-' }}</td>
                    <td>{{ $sub->name }}</td>
                    <td>
                        <button class="btn btn-sm btn-info editSubCategoryBtn" data-id="{{ $sub->id }}"
                            data-category="{{ $sub->category_id }}" data-name="{{ $sub->name }}">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteSubCategoryBtn" data-id="{{ $sub->id }}">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No sub categories found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-md-flex justify-content-between align-items-center mt-3">
    <div class="text-muted mb-2 mb-md-0">
        Showing {{ $subCategories->firstItem() ?? 0 }} to {{ $subCategories->lastItem() ?? 0 }} of
        {{ $subCategories->total() }} entries
    </div>
    <nav>
        <ul class="pagination mb-0">
            @if ($subCategories->onFirstPage())
                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
            @else
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="{{ $subCategories->currentPage() - 1 }}">&laquo;</a>
                </li>
            @endif

            @foreach (range(1, max(1, $subCategories->lastPage())) as $page)
                @if ($page == $subCategories->currentPage())
                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                @elseif ($page == 1 || $page == $subCategories->lastPage() || abs($page - $subCategories->currentPage()) <= 2)
                    <li class="page-item">
                        <a class="page-link paginateLink" href="#" data-page="{{ $page }}">{{ $page }}</a>
                    </li>
                @elseif ($page == $subCategories->currentPage() - 3 || $page == $subCategories->currentPage() + 3)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
            @endforeach

            @if ($subCategories->hasMorePages())
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="{{ $subCategories->currentPage() + 1 }}">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
            @endif
        </ul>
    </nav>
</div>

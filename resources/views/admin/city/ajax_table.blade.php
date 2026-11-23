<div class="table-responsive">
    <table class="table table-bordered" id="cityTable">
        <thead>
            <tr>
                <th>#</th>
                <th>State Name</th>
                <th>City Name</th>
                <th width="120">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($cities as $city)
                <tr id="row_{{ $city->id }}">
                    <td>{{ $loop->iteration + ($cities->currentPage() - 1) * $cities->perPage() }}</td>
                    <td>{{ $city->state->name ?? '-' }}</td>
                    <td>{{ $city->name }}</td>
                    <td>
                        <button class="btn btn-sm btn-info editCityBtn" data-id="{{ $city->id }}"
                            data-state="{{ $city->state_id }}" data-name="{{ $city->name }}">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteCityBtn" data-id="{{ $city->id }}">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No cities found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-md-flex justify-content-between align-items-center mt-3">
    <div class="text-muted mb-2 mb-md-0">
        Showing {{ $cities->firstItem() ?? 0 }} to {{ $cities->lastItem() ?? 0 }} of {{ $cities->total() }} entries
    </div>
    <nav>
        <ul class="pagination mb-0">
            {{-- Previous --}}
            @if ($cities->onFirstPage())
                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
            @else
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="{{ $cities->currentPage() - 1 }}">&laquo;</a>
                </li>
            @endif

            {{-- Page numbers --}}
            @foreach (range(1, $cities->lastPage()) as $page)
                @if ($page == $cities->currentPage())
                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                @elseif ($page == 1 || $page == $cities->lastPage() || abs($page - $cities->currentPage()) <= 2)
                    <li class="page-item">
                        <a class="page-link paginateLink" href="#"
                            data-page="{{ $page }}">{{ $page }}</a>
                    </li>
                @elseif ($page == $cities->currentPage() - 3 || $page == $cities->currentPage() + 3)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
            @endforeach

            {{-- Next --}}
            @if ($cities->hasMorePages())
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="{{ $cities->currentPage() + 1 }}">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
            @endif
        </ul>
    </nav>
</div>
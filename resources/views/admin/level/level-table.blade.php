<div class="table-responsive">
    <table class="table table-bordered align-middle" id="levelTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Level</th>
                <th>Amount</th>
                <th width="120">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($levels as $level)
                <tr id="row_{{ $level->id }}">
                    <td>{{ $loop->iteration + ($levels->currentPage() - 1) * $levels->perPage() }}</td>
                    <td>Level {{ $level->level }}</td>
                    <td>₹{{ number_format($level->amount, 2) }}</td>
                    <td>
                        <button class="btn btn-sm btn-info editLevelBtn" data-id="{{ $level->id }}"
                            data-level="{{ $level->level }}" data-amount="{{ $level->amount }}">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteLevelBtn" data-id="{{ $level->id }}">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No levels found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-md-flex justify-content-between align-items-center mt-3">
    <div class="text-muted mb-2 mb-md-0">
        Showing {{ $levels->firstItem() ?? 0 }} to {{ $levels->lastItem() ?? 0 }} of {{ $levels->total() }} entries
    </div>
    <nav>
        <ul class="pagination mb-0">
            {{-- Previous --}}
            @if ($levels->onFirstPage())
                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
            @else
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="{{ $levels->currentPage() - 1 }}">&laquo;</a>
                </li>
            @endif

            {{-- Page numbers --}}
            @foreach (range(1, max(1, $levels->lastPage())) as $page)
                @if ($page == $levels->currentPage())
                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                @elseif ($page == 1 || $page == $levels->lastPage() || abs($page - $levels->currentPage()) <= 2)
                    <li class="page-item">
                        <a class="page-link paginateLink" href="#"
                            data-page="{{ $page }}">{{ $page }}</a>
                    </li>
                @elseif ($page == $levels->currentPage() - 3 || $page == $levels->currentPage() + 3)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
            @endforeach

            {{-- Next --}}
            @if ($levels->hasMorePages())
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="{{ $levels->currentPage() + 1 }}">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
            @endif
        </ul>
    </nav>
</div>

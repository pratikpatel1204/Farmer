<div class="table-responsive">
    <table class="table table-nowrap mb-0">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Action</th>
                <th>Product</th>
                <th>Seller</th>
                <th class="text-center">Attributes</th>
                <th>Created</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $i => $p)
                <tr>
                    <td>{{ $products->firstItem() + $i }}</td>

                    {{-- ACTION --}}
                    <td>
                        <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                            <a href="{{ route('admin.product.view', $p->id) }}" class="btn btn-primary btn-sm"
                                title="View">
                                <i class="ti ti-eye"></i>
                            </a>
                            @if ($p->status !== 'Approved')
                                <button type="button" class="btn btn-success btn-approve btn-sm"
                                    data-id="{{ $p->id }}" title="Approve">
                                    <i class="ti ti-check"></i>
                                </button>
                            @endif
                            @if ($p->status !== 'Rejected')
                                <button type="button" class="btn btn-warning btn-reject btn-sm"
                                    data-id="{{ $p->id }}" title="Reject">
                                    <i class="ti ti-x"></i>
                                </button>
                            @endif
                            <button type="button" class="btn btn-danger btn-delete btn-sm"
                                data-id="{{ $p->id }}" title="Delete">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    </td>

                    {{-- PRODUCT (image + title) --}}
                    <td>
                        <div class="d-flex align-items-center">
                            <a href="{{ route('admin.product.view', $p->id) }}" class="avatar avatar-md me-2">
                                @if ($thumb = \App\Support\Market::productImage($p))
                                    <img src="{{ $thumb }}" class="rounded" alt="{{ $p->title }}">
                                @else
                                    <span class="avatar-title rounded bg-light text-muted"><i class="ti ti-photo"></i></span>
                                @endif
                            </a>
                            <h6 class="fw-medium mb-0">
                                <a href="{{ route('admin.product.view', $p->id) }}">{{ $p->title }}</a>
                            </h6>
                        </div>
                    </td>

                    {{-- SELLER (name + unique id) --}}
                    <td>
                        @if ($p->member)
                            <div class="fw-medium">{{ $p->member->full_name }}</div>
                            <small class="text-muted">{{ $p->member->unique_id }}</small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    {{-- ATTRIBUTE COUNT --}}
                    <td class="text-center">
                        <span class="badge bg-info">{{ $p->product_attrs_count ?? 0 }}</span>
                    </td>

                    <td>{{ $p->created_at?->format('d M Y, h:i A') }}</td>

                    {{-- STATUS --}}
                    <td>
                        @if ($p->status === 'Approved')
                            <span class="badge badge-soft-success">Approved</span>
                        @elseif($p->status === 'Rejected')
                            <span class="badge badge-soft-danger" title="{{ $p->rejection_reason }}">Rejected</span>
                            @if ($p->rejection_reason)
                                <div class="text-muted small mt-1" style="max-width:200px;white-space:normal">
                                    {{ $p->rejection_reason }}</div>
                            @endif
                        @else
                            <span class="badge badge-soft-warning">Pending</span>
                        @endif
                        @if ($p->completed_at)
                            <span class="badge bg-info">Completed</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted p-5">
                        No {{ strtolower($status) }} products found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($products->hasPages())
    <div class="d-flex align-items-center justify-content-between flex-wrap p-3">
        <small class="text-muted">
            Showing {{ $products->firstItem() }} - {{ $products->lastItem() }} of {{ $products->total() }}
        </small>
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
@endif

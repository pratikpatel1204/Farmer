{{-- Ajax partial: loaded into #ProductTableContainer by member/products/index.blade.php
Place at: resources/views/member/products/table.blade.php --}}
@php
    $hasFilters = $search !== '' || strtolower($status) !== 'all' || in_array($type, ['have', 'want'], true);
@endphp

@if ($products->isEmpty())
    <div class="text-center py-5">
        @if ($hasFilters)
            <p class="text-muted mb-0">
                No products match your filters
                @if ($search !== '')
                    for "<strong>{{ $search }}</strong>"
                @endif
                .
            </p>
        @else
            <p class="text-muted mb-3">No products found.</p>
            <a href="{{ route('member.product.create') }}" class="btn-zentic btn-primary">
                <i class="icon-plus"></i> Create Product
            </a>
        @endif
    </div>
@else
    <div class="table-responsive">
        <table class="zentic-table table" id="productTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>SKU</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $products->firstItem() + $loop->index }}</td>
                        <td>
                            @if (!empty($product->main_image))
                                <img src="{{ asset($product->main_image) }}" alt="{{ $product->title }}"
                                    class="avatar-gen-img avatar-48 zentic-border-radius-xl"
                                    style="width:48px;height:48px;object-fit:cover;">
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $product->sku }}</td>
                        <td class="fw-600" title="{{ $product->title }}">{{ Str::limit($product->title, 30) }}</td>
                        <td>{{ optional($product->categorys)->name ?? '-' }}</td>
                        <td>
                            @if ($product->listing_type === 'want')
                                <span class="badge-zentic badge-orange">I Want</span>
                            @else
                                <span class="badge-zentic badge-green">I Have</span>
                            @endif
                        </td>
                        <td>
                            @if ($product->completed_at)
                                <span class="badge-zentic badge-orange badge-dot" title="Completed on {{ \Carbon\Carbon::parse($product->completed_at)->format('d M Y') }}">Completed</span>
                            @elseif ($product->status === 'Approved')
                                <span class="badge-zentic badge-green badge-dot">Approved</span>
                            @elseif ($product->status === 'Rejected')
                                    <span class="badge-zentic badge-red badge-dot">Rejected</span>
                            @else
                                <span class="badge-zentic badge-orange badge-dot text-dark">Pending</span>
                            @endif
                        </td>
                        <td>{{ $product->created_at?->format('d M Y') ?? '-' }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('member.product.edit', $product->id) }}"
                                class="btn-zentic btn-warning btn-sm" title="Edit">
                                <i class="icon-pencil"></i>
                            </a>
                            <button type="button" class="btn-zentic btn-danger btn-sm delete-product"
                                data-id="{{ $product->id }}" title="Delete">
                                <i class="icon-trash"></i>
                            </button>
                            @if ($product->completed_at)
                                <button type="button" class="btn-zentic btn-primary btn-sm js-product-complete"
                                    data-id="{{ $product->id }}" data-completed="0" title="Show on website again">
                                    <i class="icon-rotate-ccw"></i>
                                </button>
                            @elseif ($product->status === 'Approved')
                                <button type="button" class="btn-zentic btn-success btn-sm js-product-complete"
                                        data-id="{{ $product->id }}" data-completed="1" title="Deal done, hide from website">
                                    <i class="icon-check-circle"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Manual ajax pagination (handled by .page-btn click in index) --}}
    <div class="product-pagination d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
        <small class="text-muted">
            Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} entries
        </small>

        @if ($products->lastPage() > 1)
            @php
                $current = $products->currentPage();
                $last = $products->lastPage();
                $start = max(1, $current - 2);
                $end = min($last, $current + 2);
            @endphp
            <div class="d-flex flex-wrap gap-1">
                <button type="button" class="btn-zentic btn-outline btn-sm page-btn" data-page="{{ $current - 1 }}"
                    {{ $current <= 1 ? 'disabled' : '' }}>
                    <i class="icon-chevron-left"></i>
                </button>

                @if ($start > 1)
                    <button type="button" class="btn-zentic btn-outline btn-sm page-btn" data-page="1">1</button>
                    @if ($start > 2)
                        <span class="px-1 align-self-center text-muted">…</span>
                    @endif
                @endif

                @for ($i = $start; $i <= $end; $i++)
                    <button type="button"
                        class="btn-zentic btn-sm page-btn {{ $i === $current ? 'btn-primary' : 'btn-outline' }}"
                        data-page="{{ $i }}">{{ $i }}</button>
                @endfor

                @if ($end < $last)
                    @if ($end < $last - 1)
                        <span class="px-1 align-self-center text-muted">…</span>
                    @endif
                    <button type="button" class="btn-zentic btn-outline btn-sm page-btn"
                        data-page="{{ $last }}">{{ $last }}</button>
                @endif

                <button type="button" class="btn-zentic btn-outline btn-sm page-btn" data-page="{{ $current + 1 }}"
                    {{ $current >= $last ? 'disabled' : '' }}>
                    <i class="icon-chevron-right"></i>
                </button>
            </div>
        @endif
    </div>
@endif
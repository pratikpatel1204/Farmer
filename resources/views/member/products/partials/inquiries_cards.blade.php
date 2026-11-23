@forelse ($groups as $group)
    @php
        $product = $group->product;
        $title = $product->title ?? 'Product removed';
        $image = $product && $product->main_image ? asset($product->main_image) : null;
        $total = (int) $group->total_count;
        $pending = (int) $group->pending_count;
        $contacted = (int) $group->contacted_count;
        $closed = (int) $group->closed_count;
        $last = $group->last_inquiry_at ? \Illuminate\Support\Carbon::parse($group->last_inquiry_at) : null;
        $type = $product->listing_type ?? null;
    @endphp
    <div class="col-xxl-3 col-lg-4 col-sm-6">
        <div class="inq-product-card {{ $pending ? 'has-pending' : '' }}" role="button" tabindex="0"
            data-product-id="{{ $group->product_id }}"
            data-url="{{ route('member.products.inquiries.product', $group->product_id) }}"
            data-title="{{ $title }}" data-image="{{ $image }}"
            aria-label="View inquiries for {{ $title }}">

            <div class="inq-pc-media">
                @if ($image)
                    <img src="{{ $image }}" alt="{{ $title }}" loading="lazy">
                @else
                    <span class="inq-pc-ph"><i class="icon-image"></i></span>
                @endif

                <span class="inq-pc-count">
                    <i class="icon-message-square"></i>
                    <span data-pc="total">{{ $total }}</span>
                    <span data-pc-label>{{ $total === 1 ? 'Inquiry' : 'Inquiries' }}</span>
                </span>

                @if ($type)
                    <span class="inq-pc-type">{{ $type === 'want' ? 'I Want' : 'I Have' }}</span>
                @endif

                <span class="inq-pc-new" title="Pending inquiries">New</span>
            </div>

            <div class="inq-pc-body">
                <h3 class="inq-pc-title" title="{{ $title }}">{{ $title }}</h3>
                @if ($product && $product->sku)
                    <div class="inq-sub mb-2">SKU: {{ $product->sku }}</div>
                @endif

                <div class="inq-pc-pills">
                    <span class="inq-pill pill-warning"><b data-pc="pending">{{ $pending }}</b> Pending</span>
                    <span class="inq-pill pill-success"><b data-pc="contacted">{{ $contacted }}</b> Contacted</span>
                    <span class="inq-pill pill-danger"><b data-pc="closed">{{ $closed }}</b> Closed</span>
                </div>

                <div class="inq-pc-foot">
                    <span class="inq-sub" title="{{ $last?->format('d M Y, h:i A') }}">
                        <i class="icon-clock"></i> {{ $last ? $last->diffForHumans() : '—' }}
                    </span>
                    <span class="inq-pc-link">View all <i class="icon-arrow-right"></i></span>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="col-12">
        <div class="inq-empty">
            <i class="icon-inbox"></i>
            @if (request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                <div class="fw-medium text-dark">No matching inquiries</div>
                <div>Try changing the search or filters.</div>
            @else
                <div class="fw-medium text-dark">No Inquiries Yet!</div>
                <div>You haven't received any product inquiries so far.</div>
            @endif
        </div>
    </div>
@endforelse
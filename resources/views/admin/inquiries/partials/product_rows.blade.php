@php
    $statusMap = [
        0 => ['label' => 'Pending', 'variant' => 'warning'],
        1 => ['label' => 'Contacted', 'variant' => 'success'],
        2 => ['label' => 'Closed', 'variant' => 'danger'],
    ];
@endphp

@forelse ($inquiries as $inquiry)
    @php
        $status = array_key_exists((int) $inquiry->status, $statusMap) ? (int) $inquiry->status : 0;
        $product = $inquiry->product;
        $seller = $inquiry->member;
        $productUrl = $product ? route('products.show', $product->slug) : null;
        $modalData = [
            'fields' => [
                ['label' => 'Product', 'value' => $product->title ?? 'Product removed', 'type' => 'text'],
                ['label' => 'Seller', 'value' => $seller->full_name ?? '—', 'type' => 'text'],
                ['label' => 'Customer', 'value' => $inquiry->full_name, 'type' => 'text'],
                ['label' => 'Email', 'value' => $inquiry->email, 'type' => 'email'],
                ['label' => 'Phone', 'value' => $inquiry->phone, 'type' => 'tel'],
                ['label' => 'Date', 'value' => $inquiry->created_at?->format('d M Y, h:i A'), 'type' => 'text'],
            ],
            'message' => $inquiry->message,
        ];
    @endphp
    <tr data-row-id="{{ $inquiry->id }}">
        <td>{{ $inquiries->firstItem() + $loop->index }}</td>

        {{-- Product --}}
        <td>
            <div class="d-flex align-items-center gap-2">
                @if ($product && $product->main_image)
                    <a href="{{ $productUrl }}" target="_blank">
                        <img class="inq-thumb" src="{{ asset($product->main_image) }}" alt="{{ $product->title }}">
                    </a>
                @else
                    <span class="inq-thumb-ph"><i class="ti ti-photo"></i></span>
                @endif
                <div class="text-truncate" style="max-width: 170px;">
                    @if ($product)
                        <a href="{{ $productUrl }}" target="_blank" class="fw-medium text-dark" title="{{ $product->title }}">
                            {{ $product->title }}
                        </a>
                    @else
                        <span class="text-muted fst-italic">Product removed</span>
                    @endif
                </div>
            </div>
        </td>

        {{-- Seller --}}
        <td>
            @if ($seller)
                <h6 class="fw-medium fs-14 mb-0">{{ $seller->full_name }}</h6>
                @if ($seller->email)
                    <span class="inq-sub">{{ $seller->email }}</span>
                @endif
            @else
                <span class="text-muted">—</span>
            @endif
        </td>

        {{-- Customer --}}
        <td>
            <div class="d-flex align-items-center gap-2">
                <span class="inq-initial">{{ strtoupper(mb_substr($inquiry->full_name ?: '?', 0, 1)) }}</span>
                <div>
                    <h6 class="fw-medium fs-14 mb-0">{{ $inquiry->full_name }}</h6>
                    @if ($inquiry->email)
                        <a href="mailto:{{ $inquiry->email }}" class="inq-sub">{{ $inquiry->email }}</a>
                    @endif
                </div>
            </div>
        </td>

        {{-- Phone --}}
        <td class="text-nowrap">
            @if ($inquiry->phone)
                <a href="tel:{{ $inquiry->phone }}" class="text-dark">{{ $inquiry->phone }}</a>
            @else
                —
            @endif
        </td>

        {{-- Message --}}
        <td>
            @if ($inquiry->message)
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted text-nowrap" title="{{ $inquiry->message }}">
                        {{ \Illuminate\Support\Str::limit($inquiry->message, 25, '...') }}
                    </span>
                    <button type="button"
                            class="btn btn-sm btn-outline-primary d-inline-flex align-items-center text-nowrap py-1 px-2 inq-view-btn"
                            data-inquiry="{{ json_encode($modalData) }}" title="Read full message">
                            <i class="ti ti-eye me-1"></i>View
                        </button>
                </div>
            @else
                —
            @endif
        </td>

        {{-- Date --}}
        <td class="text-nowrap">
            <div>{{ $inquiry->created_at?->format('d M Y') }}</div>
            <div class="inq-sub">{{ $inquiry->created_at?->format('h:i A') }}</div>
        </td>

        {{-- Status --}}
        <td>
            <select class="form-select inq-status-select status-{{ $statusMap[$status]['variant'] }}"
                data-id="{{ $inquiry->id }}" data-current="{{ $status }}">
                @foreach ($statusMap as $value => $opt)
                    <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>{{ $opt['label'] }}</option>
                @endforeach
            </select>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8">
            <div class="inq-empty">
                <i class="ti ti-inbox"></i>
                @if (request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                    <div class="fw-medium text-dark">No matching inquiries</div>
                    <div>Try changing the search or filters.</div>
                @else
                    <div class="fw-medium text-dark">No Product Inquiries Yet!</div>
                    <div>Inquiries sent on member products will appear here.</div>
                @endif
            </div>
        </td>
    </tr>
@endforelse
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
        $current = $statusMap[$status];
        $product = $inquiry->product;
        $productUrl = $product ? route('products.show', $product->slug) : null;
    @endphp
    <tr data-row-id="{{ $inquiry->id }}">
        <td>{{ $inquiries->firstItem() + $loop->index }}</td>

        {{-- Product --}}
        <td>
            <div class="d-flex align-items-center gap-2">
                @if ($product && $product->main_image)
                    <a href="{{ $productUrl }}" target="_blank">
                        <img class="inq-thumb" src="{{ asset($product->main_image) }}" alt="{{ $product->name }}">
                    </a>
                @else
                    <span class="inq-thumb-placeholder"><i class="icon-image"></i></span>
                @endif
                <div class="text-truncate" style="max-width: 180px;">
                    @if ($product)
                        <a href="{{ $productUrl }}" target="_blank" class="fw-medium text-dark"
                            title="{{ $product->name }}">
                            {{ $product->name }}
                        </a>
                    @else
                        <span class="text-muted fst-italic">Product removed</span>
                    @endif
                </div>
            </div>
        </td>

        {{-- Customer --}}
        <td>
            <div class="d-flex align-items-center gap-2">
                <span class="inq-avatar">{{ strtoupper(mb_substr($inquiry->full_name ?: '?', 0, 1)) }}</span>
                <div>
                    <div class="fw-medium">{{ $inquiry->full_name }}</div>
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
                    <span class="inq-message">{{ $inquiry->message }}</span>
                    <button type="button" class="inq-view-msg-btn" title="Read full message"
                        data-name="{{ $inquiry->full_name }}" data-email="{{ $inquiry->email }}"
                        data-phone="{{ $inquiry->phone }}" data-product="{{ $product->name ?? 'Product removed' }}"
                        data-date="{{ $inquiry->created_at?->format('d M Y, h:i A') }}"
                        data-status="{{ $current['label'] }}" data-variant="{{ $current['variant'] }}"
                        data-message="{{ $inquiry->message }}">
                        <i class="icon-eye"></i> View
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
            <select class="form-select form-control-zentic inq-status-select status-{{ $current['variant'] }}"
                data-id="{{ $inquiry->id }}" data-current="{{ $status }}">
                @foreach ($statusMap as $value => $opt)
                    <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>
                        {{ $opt['label'] }}</option>
                @endforeach
            </select>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7">
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
        </td>
    </tr>
@endforelse

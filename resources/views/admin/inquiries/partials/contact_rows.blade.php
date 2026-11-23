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
        $modalData = [
            'fields' => [
                ['label' => 'Customer', 'value' => $inquiry->full_name, 'type' => 'text'],
                ['label' => 'Subject', 'value' => $inquiry->subject, 'type' => 'text'],
                ['label' => 'Email', 'value' => $inquiry->email, 'type' => 'email'],
                ['label' => 'Phone', 'value' => $inquiry->phone, 'type' => 'tel'],
                ['label' => 'Date', 'value' => $inquiry->created_at?->format('d M Y, h:i A'), 'type' => 'text'],
            ],
            'message' => $inquiry->message,
        ];
    @endphp
    <tr data-row-id="{{ $inquiry->id }}">
        <td>{{ $inquiries->firstItem() + $loop->index }}</td>

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

        <td class="text-nowrap">
            @if ($inquiry->phone)
                <a href="tel:{{ $inquiry->phone }}" class="text-dark">{{ $inquiry->phone }}</a>
            @else
                —
            @endif
        </td>

        <td>
            @if ($inquiry->subject)
                <span class="badge badge-soft-info">{{ $inquiry->subject }}</span>
            @else
                —
            @endif
        </td>

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

        <td class="text-nowrap">
            <div>{{ $inquiry->created_at?->format('d M Y') }}</div>
            <div class="inq-sub">{{ $inquiry->created_at?->format('h:i A') }}</div>
        </td>

        <td>
            <select class="form-select inq-status-select status-{{ $statusMap[$status]['variant'] }}"
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
                <i class="ti ti-inbox"></i>
                @if (request()->anyFilled(['search', 'status', 'subject', 'date_from', 'date_to']))
                    <div class="fw-medium text-dark">No matching inquiries</div>
                    <div>Try changing the search or filters.</div>
                @else
                    <div class="fw-medium text-dark">No Contact Inquiries Yet!</div>
                    <div>Messages sent from the Contact Us page will appear here.</div>
                @endif
            </div>
        </td>
    </tr>
@endforelse

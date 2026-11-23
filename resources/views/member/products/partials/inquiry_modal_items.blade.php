@php
    $statusMap = [
        0 => ['label' => 'Pending', 'variant' => 'warning'],
        1 => ['label' => 'Contacted', 'variant' => 'success'],
        2 => ['label' => 'Closed', 'variant' => 'danger'],
    ];
@endphp

@foreach ($inquiries as $inquiry)
    @php
        $status = array_key_exists((int) $inquiry->status, $statusMap) ? (int) $inquiry->status : 0;
        $digits = preg_replace('/\D/', '', (string) $inquiry->phone);
        if (strlen($digits) === 10) {
            $digits = '91' . $digits;
        }
        $isLong = mb_strlen((string) $inquiry->message) > 220;
    @endphp
    <div class="inq-item" data-status="{{ $status }}">
        <div class="inq-item-head">
            <span class="inq-avatar">{{ strtoupper(mb_substr($inquiry->full_name ?: '?', 0, 1)) }}</span>

            <div class="inq-item-who">
                <div class="inq-item-name">{{ $inquiry->full_name }}</div>
                <div class="inq-item-contact">
                    @if ($inquiry->phone)
                        <a href="tel:{{ $inquiry->phone }}"><i class="icon-phone"></i> {{ $inquiry->phone }}</a>
                    @endif
                    @if (strlen($digits) >= 11)
                        <a href="https://wa.me/{{ $digits }}" target="_blank" rel="noopener" class="inq-wa">WhatsApp</a>
                    @endif
                    @if ($inquiry->email)
                        <a href="mailto:{{ $inquiry->email }}"><i class="icon-mail"></i> {{ $inquiry->email }}</a>
                    @endif
                </div>
            </div>

            <div class="inq-item-side">
                <select class="form-control-zentic inq-status-select status-{{ $statusMap[$status]['variant'] }}"
                    data-id="{{ $inquiry->id }}" data-current="{{ $status }}" aria-label="Inquiry status">
                    @foreach ($statusMap as $value => $opt)
                        <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>{{ $opt['label'] }}</option>
                    @endforeach
                </select>
                <div class="inq-sub text-nowrap" title="{{ $inquiry->created_at?->format('d M Y, h:i A') }}">
                    {{ $inquiry->created_at?->format('d M Y, h:i A') }}
                </div>
            </div>
        </div>

        @if ($inquiry->message)
            <div class="inq-item-msg {{ $isLong ? 'is-long' : '' }}">{{ $inquiry->message }}</div>
            @if ($isLong)
                <button type="button" class="inq-readmore">Read more</button>
            @endif
        @else
            <div class="inq-item-msg text-muted fst-italic">No message.</div>
        @endif
    </div>
@endforeach
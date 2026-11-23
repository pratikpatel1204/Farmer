@php
    $statCards = [
        ['status' => '', 'key' => 'total', 'label' => 'Total Inquiries', 'icon' => 'ti ti-inbox', 'color' => 'primary'],
        [
            'status' => '0',
            'key' => 'pending',
            'label' => 'Pending',
            'icon' => 'ti ti-clock-hour-4',
            'color' => 'warning',
        ],
        [
            'status' => '1',
            'key' => 'contacted',
            'label' => 'Contacted',
            'icon' => 'ti ti-phone-check',
            'color' => 'success',
        ],
        ['status' => '2', 'key' => 'closed', 'label' => 'Closed', 'icon' => 'ti ti-circle-x', 'color' => 'danger'],
    ];
    $activeStatus = (string) request('status', '');
@endphp

<div class="row">
    @foreach ($statCards as $card)
        <div class="col-xl-3 col-sm-6 d-flex">
            <div class="card flex-fill inq-stat-card inq-c-{{ $card['color'] }} {{ $activeStatus === $card['status'] ? 'active' : '' }}"
                data-status="{{ $card['status'] }}" role="button">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <p class="fs-13 fw-medium text-muted mb-1">{{ $card['label'] }}</p>
                        <h3 class="mb-0" data-count="{{ $card['key'] }}">{{ (int) ($counts[$card['key']] ?? 0) }}</h3>
                    </div>
                    <span class="inq-stat-icon"><i class="{{ $card['icon'] }}"></i></span>
                </div>
            </div>
        </div>
    @endforeach
</div>

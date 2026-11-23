@extends('member.layout.main-layout')
@section('title', config('app.name') . ' || Notifications')
@php
    // Icon => [Zentic icon class, notif-icon bg class]. Bell wale partial jaisa hi.
    $nMap = [
        'inquiry' => ['icon-message-square', 'bg-primary'],
        'contact' => ['icon-mail', 'bg-info'],
        'user' => ['icon-user-plus', 'bg-success'],
        'product' => ['icon-package', 'bg-warning'],
        'profile' => ['icon-circle-user', 'bg-primary'],
        'survey' => ['icon-clipboard-list', 'bg-info'],
        'bell' => ['icon-bell', 'bg-primary'],
    ];

    $tabs = ['all' => 'All', 'unread' => 'Unread', 'requests' => 'Enquiries', 'system' => 'Updates'];

    // Current page ke notifications ko Today / Yesterday / Earlier me baanto
    $groups = $notifications->getCollection()->groupBy(function ($n) {
        if ($n->created_at->isToday()) {
            return 'Today';
        }
        if ($n->created_at->isYesterday()) {
            return 'Yesterday';
        }
        return 'Earlier';
    });
@endphp
@section('content')
    <main class="zentic-content km-notif-page">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
            <div>
                <h1 class="page-title mb-1">Notifications</h1>
                <p class="text-muted mb-0">
                    {{ $stats['unread'] ? $stats['unread'] . ' unread ' . \Illuminate\Support\Str::plural('notification', $stats['unread']) : 'You are all caught up' }}
                </p>
            </div>
            @if ($stats['unread'])
                <form action="{{ route('member.notifications.readAll') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn-zentic"><i class="icon-circle-check"></i> Mark All Read</button>
                </form>
            @endif
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="zentic-card">
                    <div class="zentic-card-body">
                        <div class="notif-tabs mb-3">
                            @foreach ($tabs as $key => $label)
                                <a href="{{ route('member.notification', $key === 'all' ? [] : ['tab' => $key]) }}"
                                    class="notif-tab {{ $tab === $key ? 'active' : '' }}">{{ $label }}</a>
                            @endforeach
                        </div>

                        @forelse ($groups as $day => $items)
                            <p class="km-notif-day">{{ $day }}</p>
                            <div class="notif-list">
                                @foreach ($items as $n)
                                    @php [$ic, $bg] = $nMap[$n->data['icon'] ?? 'bell'] ?? $nMap['bell']; @endphp
                                    <div class="notif-item {{ $n->read_at ? '' : 'unread' }}">
                                        <div class="notif-icon {{ $bg }}"><i class="{{ $ic }}"></i></div>
                                        <a href="{{ route('member.notifications.open', $n->id) }}" class="notif-content">
                                            <p class="notif-title">{{ $n->data['title'] ?? 'Notification' }}</p>
                                            <p class="notif-desc">{{ $n->data['message'] ?? '' }}</p>
                                            <span class="notif-time">
                                                {{ $n->created_at->isToday() ? $n->created_at->diffForHumans() : $n->created_at->format('d M Y, h:i A') }}
                                            </span>
                                        </a>
                                        <form action="{{ route('member.notifications.destroy', $n->id) }}" method="POST" class="m-0"
                                            onsubmit="return confirm('Delete this notification?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="km-notif-del" title="Delete" aria-label="Delete">
                                                <i class="icon-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @empty
                            <div class="km-notif-empty py-5">
                                <i class="icon-bell d-block mb-2" style="font-size:28px;"></i>
                                {{ $tab === 'all' ? 'No notifications yet. Enquiries and approvals will show here.' : 'Nothing here right now.' }}
                            </div>
                        @endforelse

                        @if ($notifications->hasPages())
                            <div class="mt-3">{{ $notifications->links('pagination::bootstrap-5') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="zentic-card mb-4">
                    <div class="zentic-card-body">
                        <p class="fw-semibold mb-3">Summary</p>
                        <div class="km-notif-stats">
                            <div><span>Unread</span><strong>{{ $stats['unread'] }}</strong></div>
                            <div><span>Today</span><strong>{{ $stats['today'] }}</strong></div>
                            <div><span>This Week</span><strong>{{ $stats['week'] }}</strong></div>
                            <div><span>Total</span><strong>{{ $stats['total'] }}</strong></div>
                        </div>
                    </div>
                </div>

                <div class="zentic-card">
                    <div class="zentic-card-body">
                        <p class="fw-semibold mb-2">Clean up</p>
                        <p class="text-muted small mb-3">Delete all notifications you have already read.</p>
                        <form action="{{ route('member.notifications.clearRead') }}" method="POST" class="m-0"
                            onsubmit="return confirm('Delete all read notifications?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-zentic w-100">Clear read notifications</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
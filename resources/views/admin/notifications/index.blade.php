@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || Notifications')
@php
    $nIcons = [
        'inquiry' => ['ti-message-question', 'bg-primary'],
        'contact' => ['ti-address-book', 'bg-info'],
        'user' => ['ti-user-plus', 'bg-success'],
        'product' => ['ti-package', 'bg-warning'],
        'profile' => ['ti-id-badge', 'bg-purple'],
        'survey' => ['ti-clipboard-list', 'bg-pink'],
        'bell' => ['ti-bell', 'bg-secondary'],
    ];
@endphp
@section('content')
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Notifications</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Notifications</li>
                    </ol>
                </nav>
            </div>
            @if (auth()->user()->unreadNotifications()->exists())
                <form action="{{ route('admin.notifications.readAll') }}" method="POST" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-primary"><i class="ti ti-checks me-1"></i>Mark all as read</button>
                </form>
            @endif
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body p-0">
                @forelse ($notifications as $notification)
                    @php [$ic, $bg] = $nIcons[$notification->data['icon'] ?? 'bell'] ?? $nIcons['bell']; @endphp
                    <div class="d-flex align-items-start gap-3 p-3 border-bottom {{ $notification->read_at ? '' : 'bg-light' }}">
                        <span class="avatar avatar-md rounded-circle {{ $bg }} flex-shrink-0">
                            <i class="ti {{ $ic }} fs-16 text-white"></i>
                        </span>
                        <a href="{{ route('admin.notifications.open', $notification->id) }}" class="flex-grow-1 text-body">
                            <p class="mb-1">
                                <span class="text-dark fw-semibold">{{ $notification->data['title'] ?? 'Notification' }}</span>
                                @unless ($notification->read_at)
                                    <span class="badge bg-danger ms-1">New</span>
                                @endunless
                            </p>
                            <p class="mb-1">{{ $notification->data['message'] ?? '' }}</p>
                            <span class="text-muted fs-12">
                                {{ $notification->created_at->format('d M Y, h:i A') }} ¡¤ {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </a>
                        <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST"
                            onsubmit="return confirm('Delete this notification?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-icon btn-light" title="Delete">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="ti ti-bell-off fs-24 d-block mb-2"></i>
                        No notifications yet.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="mt-3">{{ $notifications->links('pagination::bootstrap-5') }}</div>
    </div>
@endsection
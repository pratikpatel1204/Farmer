@php
    $nUser = auth()->user();
    $nUnread = $nUser ? $nUser->unreadNotifications()->take(8)->get() : collect();
    $nCount = $nUser ? $nUser->unreadNotifications()->count() : 0;
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

@if ($nUser)
    <div class="me-1 notification_item dropdown">
        <a href="#" class="btn btn-menubar position-relative me-1" id="notification_popup" data-bs-toggle="dropdown"
            data-bs-auto-close="outside" aria-expanded="false">
            <i class="ti ti-bell"></i>
            <span class="km-noti-dot {{ $nCount ? 'notification-status-dot' : '' }}" id="kmNotiDot"></span>
        </a>

        <div class="dropdown-menu dropdown-menu-end notification-dropdown p-0" style="width:360px;">
            <div class="d-flex align-items-center justify-content-between border-bottom px-3 py-3">
                <h4 class="notification-title mb-0 fs-16">
                    Notifications (<span id="kmNotiCount">{{ $nCount }}</span>)
                </h4>
                <form action="{{ route('admin.notifications.readAll') }}" method="POST" class="m-0" id="kmNotiReadAll"
                    @if (!$nCount) style="display:none" @endif>
                    @csrf
                    <button type="submit" class="btn btn-link p-0 fs-13 text-primary text-decoration-none">
                        <i class="ti ti-checks me-1"></i>Mark all read
                    </button>
                </form>
            </div>

            <div class="noti-content px-3 pt-3" style="max-height:360px; overflow-y:auto;" id="kmNotiList">
                @forelse ($nUnread as $notification)
                    @php [$ic, $bg] = $nIcons[$notification->data['icon'] ?? 'bell'] ?? $nIcons['bell']; @endphp
                    <div class="border-bottom mb-3 pb-3">
                        <a href="{{ route('admin.notifications.open', $notification->id) }}" class="d-flex text-body">
                            <span class="avatar avatar-md rounded-circle {{ $bg }} flex-shrink-0 me-2">
                                <i class="ti {{ $ic }} fs-16 text-white"></i>
                            </span>
                            <div class="flex-grow-1">
                                <p class="mb-1">
                                    <span class="text-dark fw-semibold">{{ $notification->data['title'] ?? 'Notification' }}</span><br>
                                    <span class="fs-13">{{ $notification->data['message'] ?? '' }}</span>
                                </p>
                                <span class="text-muted fs-12">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                        </a>
                    </div>
                @empty
                    <p class="text-muted text-center py-3 mb-0">No new notifications</p>
                @endforelse
            </div>

            <div class="border-top px-3 py-2 text-center">
                <a href="{{ route('admin.notifications') }}" class="fw-medium text-primary">View all notifications</a>
            </div>
        </div>
    </div>

    <audio id="notificationSound" src="{{ asset('admin/notification.mp3') }}" preload="auto"></audio>

    <script>
        (function() {
            const icons = @json($nIcons);
            const latestUrl = @json(route('admin.notifications.latest'));
            let lastCount = {{ $nCount }};
            let audioUnlocked = false;
            const unlock = () => { audioUnlocked = true; };
            ['click', 'keydown'].forEach(e => document.addEventListener(e, unlock, { once: true }));

            const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

            function render(data) {
                document.getElementById('kmNotiCount').textContent = data.count;
                document.getElementById('kmNotiDot').classList.toggle('notification-status-dot', data.count > 0);
                document.getElementById('kmNotiReadAll').style.display = data.count ? '' : 'none';
                const list = document.getElementById('kmNotiList');
                list.innerHTML = data.items.length ? data.items.map(n => {
                    const [ic, bg] = icons[n.icon] || icons.bell;
                    return `<div class="border-bottom mb-3 pb-3"><a href="${esc(n.url)}" class="d-flex text-body">
                        <span class="avatar avatar-md rounded-circle ${bg} flex-shrink-0 me-2"><i class="ti ${ic} fs-16 text-white"></i></span>
                        <div class="flex-grow-1"><p class="mb-1"><span class="text-dark fw-semibold">${esc(n.title)}</span><br>
                        <span class="fs-13">${esc(n.message)}</span></p><span class="text-muted fs-12">${esc(n.time)}</span></div></a></div>`;
                }).join('') : '<p class="text-muted text-center py-3 mb-0">No new notifications</p>';
            }

            // Har 30 sec naye notification check. Naya aaya to sound ek baar bajta hai.
            setInterval(() => {
                if (document.hidden) return;
                fetch(latestUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.ok ? r.json() : null)
                    .then(data => {
                        if (!data) return;
                        if (data.count > lastCount && audioUnlocked) {
                            document.getElementById('notificationSound')?.play().catch(() => {});
                        }
                        lastCount = data.count;
                        render(data);
                    })
                    .catch(() => {});
            }, 30000);
        })();
    </script>
@endif
@php
    $nUser = auth()->user();
    $nUnread = $nUser->unreadNotifications()->take(8)->get();
    $nCount = $nUser->unreadNotifications()->count();
    // Icon => [Zentic icon class, notif-icon bg class]. Koi icon na dikhe to yahan badlo.
    $nMap = [
        'inquiry' => ['icon-message-square', 'bg-primary'],
        'contact' => ['icon-mail', 'bg-info'],
        'user' => ['icon-user-plus', 'bg-success'],
        'product' => ['icon-package', 'bg-warning'],
        'profile' => ['icon-circle-user', 'bg-primary'],
        'survey' => ['icon-clipboard-list', 'bg-info'],
        'bell' => ['icon-bell', 'bg-primary'],
    ];
    // "Enquiries" tab me ye types, baaki sab "Updates" tab me
    $nRequestTypes = ['product_inquiry', 'contact_inquiry', 'member_registered'];
    $nTab = fn ($n) => in_array($n->data['type'] ?? '', $nRequestTypes, true) ? 'requests' : 'system';
@endphp

<div class="topbar-btn-notification" id="kmNotif">
    <button type="button" class="topbar-btn header-icon" id="kmNotifBtn" aria-label="Notifications" aria-expanded="false">
        <i class="icon-bell"></i>
        <span class="notif-badge" id="kmNotifBadge" @if (!$nCount) style="display:none" @endif></span>
    </button>

    <div class="notif-panel" id="kmNotifPanel">
        <div class="notif-header">
            <h1 class="page-title">Notifications <span class="km-notif-num" id="kmNotifNum">{{ $nCount ? '(' . $nCount . ')' : '' }}</span></h1>
            <form action="{{ route('member.notifications.readAll') }}" method="POST" class="m-0" id="kmNotifReadAll"
                @if (!$nCount) style="display:none" @endif>
                @csrf
                <button type="submit" class="btn-text">Mark all read</button>
            </form>
        </div>

        <div class="notif-tabs">
            <button type="button" class="notif-tab active" data-km-tab="all">All</button>
            <button type="button" class="notif-tab" data-km-tab="requests">Enquiries</button>
            <button type="button" class="notif-tab" data-km-tab="system">Updates</button>
        </div>

        <div class="notif-list" id="kmNotifList">
            @forelse ($nUnread as $n)
                @php [$ic, $bg] = $nMap[$n->data['icon'] ?? 'bell'] ?? $nMap['bell']; @endphp
                <a href="{{ route('member.notifications.open', $n->id) }}" class="notif-item unread" data-tab="{{ $nTab($n) }}">
                    <div class="notif-icon {{ $bg }}"><i class="{{ $ic }}"></i></div>
                    <div class="notif-content">
                        <p class="notif-title">{{ $n->data['title'] ?? 'Notification' }}</p>
                        <p class="notif-desc">{{ $n->data['message'] ?? '' }}</p>
                        <span class="notif-time">{{ $n->created_at->diffForHumans() }}</span>
                    </div>
                </a>
            @empty
                <div class="km-notif-empty">You're all caught up. No new notifications.</div>
            @endforelse
            <div class="km-notif-empty km-notif-tab-empty" style="display:none">Nothing new here.</div>
        </div>

        <div class="notif-footer notif-footer-split">
            <form action="{{ route('member.notifications.clearRead') }}" method="POST" class="m-0"
                onsubmit="return confirm('Delete all read notifications?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="notif-footer-link km-link-btn">Clear read</button>
            </form>
            <a href="{{ route('member.notification') }}">View all notifications</a>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function() {
            const box = document.getElementById('kmNotif');
            if (!box) return;
            const btn = document.getElementById('kmNotifBtn');
            const panel = document.getElementById('kmNotifPanel');
            const list = document.getElementById('kmNotifList');
            const icons = @json($nMap);
            const requestTypes = @json($nRequestTypes);
            const latestUrl = @json(route('member.notifications.latest'));
            let activeTab = 'all';

            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const open = panel.classList.toggle('open');
                btn.setAttribute('aria-expanded', open);
            });
            document.addEventListener('click', e => {
                if (!box.contains(e.target)) {
                    panel.classList.remove('open');
                    btn.setAttribute('aria-expanded', 'false');
                }
            });

            function applyTab() {
                let shown = 0;
                list.querySelectorAll('.notif-item').forEach(item => {
                    const show = activeTab === 'all' || item.dataset.tab === activeTab;
                    item.style.display = show ? '' : 'none';
                    if (show) shown++;
                });
                const hasItems = list.querySelector('.notif-item');
                list.querySelector('.km-notif-tab-empty').style.display = hasItems && !shown ? '' : 'none';
            }

            box.querySelectorAll('[data-km-tab]').forEach(tab => {
                tab.addEventListener('click', e => {
                    e.stopPropagation();
                    box.querySelectorAll('[data-km-tab]').forEach(t => t.classList.toggle('active', t === tab));
                    activeTab = tab.dataset.kmTab;
                    applyTab();
                });
            });

            const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

            // Har 30 sec naye notifications (panel band ho tab), page reload ki zarurat nahi
            setInterval(() => {
                if (document.hidden || panel.classList.contains('open')) return;
                fetch(latestUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.ok ? r.json() : null)
                    .then(data => {
                        if (!data) return;
                        document.getElementById('kmNotifBadge').style.display = data.count ? '' : 'none';
                        document.getElementById('kmNotifReadAll').style.display = data.count ? '' : 'none';
                        document.getElementById('kmNotifNum').textContent = data.count ? '(' + data.count + ')' : '';
                        const items = data.items.map(n => {
                            const [ic, bg] = icons[n.icon] || icons.bell;
                            const tab = requestTypes.includes(n.type) ? 'requests' : 'system';
                            return `<a href="${esc(n.url)}" class="notif-item unread" data-tab="${tab}">
                                <div class="notif-icon ${bg}"><i class="${ic}"></i></div>
                                <div class="notif-content"><p class="notif-title">${esc(n.title)}</p>
                                <p class="notif-desc">${esc(n.message)}</p><span class="notif-time">${esc(n.time)}</span></div></a>`;
                        }).join('');
                        list.innerHTML = (items || '<div class="km-notif-empty">You\'re all caught up. No new notifications.</div>')
                            + '<div class="km-notif-empty km-notif-tab-empty" style="display:none">Nothing new here.</div>';
                        applyTab();
                    })
                    .catch(() => {});
            }, 30000);
        })();
    </script>
@endpush
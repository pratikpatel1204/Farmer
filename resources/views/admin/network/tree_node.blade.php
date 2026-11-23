@php
    $children = $childrenMap->get($user->unique_id, collect());
    $hasChildren = $children->isNotEmpty();
    $isRoot = $user->unique_id === $rootId;
    $isActive = (int) ($user->status ?? 1) === 1;
    $level = (int) ($user->level ?? 0);

    $headerClass = match ($user->role) {
        'Admin' => 'node-admin',
        'Farmer' => 'node-farmer',
        default => 'node-nonfarmer',
    };
    if (!$isActive) {
        $headerClass = 'node-inactive';
    }
@endphp

<div class="member-view-box {{ $hasChildren ? 'has-children' : '' }}"
    title="{{ $hasChildren ? 'Click to expand / collapse' : '' }}">
    <div class="member-header {{ $headerClass }}">
        {{ $user->role }}
        @if ($level > 0)
            &middot; L{{ $level }}
        @endif
    </div>
    <div class="member-image">
        <i class="ti ti-user-circle {{ $isActive ? 'text-success' : 'text-danger' }}"></i>
    </div>
    <div class="member-footer">
        <div class="name" title="{{ $user->full_name }}">{{ $user->full_name }}</div>
        <div class="id">{{ $user->unique_id }}</div>
        <div class="meta">
            @if ($hasChildren)
                <span class="badge bg-light text-dark">{{ $children->count() }} Direct</span>
            @endif
            @if (!$isRoot)
                <a href="{{ route('admin.member.tree', $user->unique_id) }}" class="view-tree-link"
                    title="View {{ $user->unique_id }} tree">
                    <i class="ti ti-external-link"></i>
                </a>
            @endif
        </div>
    </div>
</div>

@if ($hasChildren)
    <ul>
        @foreach ($children as $child)
            <li>
                @include('admin.network.tree_node', [
                    'user' => $child,
                    'childrenMap' => $childrenMap,
                    'rootId' => $rootId,
                ])
            </li>
        @endforeach
    </ul>
@endif

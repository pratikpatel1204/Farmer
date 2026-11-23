{{-- Recursive node. Place at: resources/views/member/network/tree_node.blade.php --}}
@php
    $hasChildren = isset($children) && count($children);
    $isRoot = empty($user->level);
    $isActive = $user->status == 1;
    $headerClass = $isRoot || $hasChildren ? 'leader' : ($isActive ? 'active' : 'inactive');
@endphp
<div class="member-view-box" data-toggle="children">
    <div class="member-header {{ $headerClass }}">
        <span>{{ $isRoot ? 'You' : ($hasChildren ? 'Leader' : 'Member') }}</span>
    </div>
    <div class="member-image">
        <i class="icon-circle-user {{ $isActive ? 'text-success-c' : 'text-danger-c' }}"></i>
    </div>
    <div class="member-footer">
        <div class="name" title="{{ $user->full_name }}">{{ $user->full_name }}</div>
        <div class="id">{{ $user->unique_id }}</div>
        <div class="meta">
            {{ $user->role }}@if (!$isRoot)
                &middot; Lvl {{ $user->level }}
            @endif
        </div>
    </div>
</div>

@if ($hasChildren)
    <ul>
        @foreach ($children as $child)
            <li>
                @include('member.network.tree_node', [
                    'user' => $child,
                    'children' => $child->children,
                ])
            </li>
        @endforeach
    </ul>
@endif

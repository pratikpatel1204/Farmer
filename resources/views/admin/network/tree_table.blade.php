<div class="genealogy-tree">
    <ul>
        <li>
            @include('admin.network.tree_node', [
                'user' => $root,
                'childrenMap' => $childrenMap,
                'rootId' => $root->unique_id,
            ])
        </li>
    </ul>
</div>

@if ($childrenMap->isEmpty())
    <p class="text-center text-muted mb-0">No downline members found</p>
@endif

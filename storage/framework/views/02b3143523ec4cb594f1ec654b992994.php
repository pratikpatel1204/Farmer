<div class="genealogy-tree">
    <ul>
        <li>
            <?php echo $__env->make('admin.network.tree_node', [
                'user' => $root,
                'childrenMap' => $childrenMap,
                'rootId' => $root->unique_id,
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </li>
    </ul>
</div>

<?php if($childrenMap->isEmpty()): ?>
    <p class="text-center text-muted mb-0">No downline members found</p>
<?php endif; ?>
<?php /**PATH /home/yftnutez/rgmmart.in/resources/views/admin/network/tree_table.blade.php ENDPATH**/ ?>
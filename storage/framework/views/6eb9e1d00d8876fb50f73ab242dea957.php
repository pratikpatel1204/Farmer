<?php
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
?>

<div class="member-view-box <?php echo e($hasChildren ? 'has-children' : ''); ?>"
    title="<?php echo e($hasChildren ? 'Click to expand / collapse' : ''); ?>">
    <div class="member-header <?php echo e($headerClass); ?>">
        <?php echo e($user->role); ?>

        <?php if($level > 0): ?>
            &middot; L<?php echo e($level); ?>

        <?php endif; ?>
    </div>
    <div class="member-image">
        <i class="ti ti-user-circle <?php echo e($isActive ? 'text-success' : 'text-danger'); ?>"></i>
    </div>
    <div class="member-footer">
        <div class="name" title="<?php echo e($user->full_name); ?>"><?php echo e($user->full_name); ?></div>
        <div class="id"><?php echo e($user->unique_id); ?></div>
        <div class="meta">
            <?php if($hasChildren): ?>
                <span class="badge bg-light text-dark"><?php echo e($children->count()); ?> Direct</span>
            <?php endif; ?>
            <?php if(!$isRoot): ?>
                <a href="<?php echo e(route('admin.member.tree', $user->unique_id)); ?>" class="view-tree-link"
                    title="View <?php echo e($user->unique_id); ?> tree">
                    <i class="ti ti-external-link"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if($hasChildren): ?>
    <ul>
        <?php $__currentLoopData = $children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
                <?php echo $__env->make('admin.network.tree_node', [
                    'user' => $child,
                    'childrenMap' => $childrenMap,
                    'rootId' => $rootId,
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
<?php endif; ?>
<?php /**PATH /home/yftnutez/rgmmart.in/resources/views/admin/network/tree_node.blade.php ENDPATH**/ ?>
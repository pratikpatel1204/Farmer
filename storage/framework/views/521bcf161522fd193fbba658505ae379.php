<div class="table-responsive">
    <table class="table table-bordered align-middle" id="downlineTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Member</th>
                <th width="110">Role</th>
                <th width="80">Level</th>
                <th>Sponsor</th>
                <th>Mobile</th>
                <th width="100">Status</th>
                <th width="110">Joined</th>
                <th width="110">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($loop->iteration + ($members->currentPage() - 1) * $members->perPage()); ?></td>
                    <td>
                        <div class="fw-semibold"><?php echo e($member->full_name); ?></div>
                        <small class="text-muted"><?php echo e($member->unique_id); ?></small>
                    </td>
                    <td>
                        <?php if($member->role === 'Farmer'): ?>
                            <span class="badge bg-success">Farmer</span>
                        <?php else: ?>
                            <span class="badge bg-info"><?php echo e($member->role); ?></span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge bg-secondary">Level <?php echo e($member->level); ?></span></td>
                    <td>
                        <?php if($member->sponsor_id): ?>
                            <div><?php echo e($member->sponsor_name ?? '-'); ?></div>
                            <small class="text-muted"><?php echo e($member->sponsor_id); ?></small>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($member->mobile ?? '-'); ?></td>
                    <td>
                        <?php if((int) $member->status === 1): ?>
                            <span class="badge bg-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e(optional($member->created_at)->format('d-m-Y')); ?></td>
                    <td>
                        <a href="<?php echo e(route('admin.member.tree', $member->unique_id)); ?>" class="btn btn-sm btn-info"
                            title="Tree View">
                            <i class="ti ti-hierarchy-2"></i>
                        </a>
                        <a href="<?php echo e(route('admin.member.downline', $member->unique_id)); ?>"
                            class="btn btn-sm btn-primary" title="Downline">
                            <i class="ti ti-users"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="9" class="text-center">No downline members found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="d-md-flex justify-content-between align-items-center mt-3">
    <div class="text-muted mb-2 mb-md-0">
        Showing <?php echo e($members->firstItem() ?? 0); ?> to <?php echo e($members->lastItem() ?? 0); ?> of <?php echo e($members->total()); ?> entries
    </div>
    <nav>
        <ul class="pagination mb-0">
            
            <?php if($members->onFirstPage()): ?>
                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
            <?php else: ?>
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="<?php echo e($members->currentPage() - 1); ?>">&laquo;</a>
                </li>
            <?php endif; ?>

            
            <?php $__currentLoopData = range(1, max(1, $members->lastPage())); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($page == $members->currentPage()): ?>
                    <li class="page-item active"><span class="page-link"><?php echo e($page); ?></span></li>
                <?php elseif($page == 1 || $page == $members->lastPage() || abs($page - $members->currentPage()) <= 2): ?>
                    <li class="page-item">
                        <a class="page-link paginateLink" href="#"
                            data-page="<?php echo e($page); ?>"><?php echo e($page); ?></a>
                    </li>
                <?php elseif($page == $members->currentPage() - 3 || $page == $members->currentPage() + 3): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <?php if($members->hasMorePages()): ?>
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="<?php echo e($members->currentPage() + 1); ?>">&raquo;</a>
                </li>
            <?php else: ?>
                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>
<?php /**PATH /home/yftnutez/rgmmart.in/resources/views/admin/network/downline_table.blade.php ENDPATH**/ ?>
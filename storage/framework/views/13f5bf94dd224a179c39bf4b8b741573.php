<div class="table-responsive">
    <table class="table table-bordered" id="subCategoryTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Category Name</th>
                <th>Sub Category Name</th>
                <th width="120">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $subCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr id="row_<?php echo e($sub->id); ?>">
                    <td><?php echo e($loop->iteration + ($subCategories->currentPage() - 1) * $subCategories->perPage()); ?></td>
                    <td><?php echo e($sub->category->name ?? '-'); ?></td>
                    <td><?php echo e($sub->name); ?></td>
                    <td>
                        <button class="btn btn-sm btn-info editSubCategoryBtn" data-id="<?php echo e($sub->id); ?>"
                            data-category="<?php echo e($sub->category_id); ?>" data-name="<?php echo e($sub->name); ?>">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteSubCategoryBtn" data-id="<?php echo e($sub->id); ?>">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="4" class="text-center">No sub categories found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="d-md-flex justify-content-between align-items-center mt-3">
    <div class="text-muted mb-2 mb-md-0">
        Showing <?php echo e($subCategories->firstItem() ?? 0); ?> to <?php echo e($subCategories->lastItem() ?? 0); ?> of
        <?php echo e($subCategories->total()); ?> entries
    </div>
    <nav>
        <ul class="pagination mb-0">
            <?php if($subCategories->onFirstPage()): ?>
                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
            <?php else: ?>
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="<?php echo e($subCategories->currentPage() - 1); ?>">&laquo;</a>
                </li>
            <?php endif; ?>

            <?php $__currentLoopData = range(1, max(1, $subCategories->lastPage())); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($page == $subCategories->currentPage()): ?>
                    <li class="page-item active"><span class="page-link"><?php echo e($page); ?></span></li>
                <?php elseif($page == 1 || $page == $subCategories->lastPage() || abs($page - $subCategories->currentPage()) <= 2): ?>
                    <li class="page-item">
                        <a class="page-link paginateLink" href="#" data-page="<?php echo e($page); ?>"><?php echo e($page); ?></a>
                    </li>
                <?php elseif($page == $subCategories->currentPage() - 3 || $page == $subCategories->currentPage() + 3): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if($subCategories->hasMorePages()): ?>
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="<?php echo e($subCategories->currentPage() + 1); ?>">&raquo;</a>
                </li>
            <?php else: ?>
                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>
<?php /**PATH D:\xampp\htdocs\Farmer\resources\views/admin/sub_category/ajax_table.blade.php ENDPATH**/ ?>
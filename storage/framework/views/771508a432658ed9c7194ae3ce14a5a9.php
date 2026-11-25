<div class="table-responsive">
    <table class="table table-bordered align-middle" id="categoryTable">
        <thead>
            <tr>
                <th>#</th>
                <th width="100">Image</th>
                <th>Category Name</th>
                <th width="140">Sub Categories</th>
                <th width="120">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr id="row_<?php echo e($category->id); ?>">
                    <td><?php echo e($loop->iteration + ($categories->currentPage() - 1) * $categories->perPage()); ?></td>
                    <td>
                        <?php if($category->image): ?>
                            <img src="<?php echo e(asset($category->image)); ?>" alt="<?php echo e($category->name); ?>"
                                style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($category->name); ?></td>
                    <td>
                        <a href="<?php echo e(route('admin.sub-category.index', ['category_id' => $category->id])); ?>" class="badge bg-primary text-decoration-none"
                            title="View sub categories"><?php echo e($category->sub_categories_count); ?></a>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info editCategoryBtn" data-id="<?php echo e($category->id); ?>"
                            data-name="<?php echo e($category->name); ?>"
                            data-image="<?php echo e($category->image ? asset($category->image) : ''); ?>">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteCategoryBtn" data-id="<?php echo e($category->id); ?>">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="text-center">No categories found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="d-md-flex justify-content-between align-items-center mt-3">
    <div class="text-muted mb-2 mb-md-0">
        Showing <?php echo e($categories->firstItem() ?? 0); ?> to <?php echo e($categories->lastItem() ?? 0); ?> of
        <?php echo e($categories->total()); ?> entries
    </div>
    <nav>
        <ul class="pagination mb-0">
            
            <?php if($categories->onFirstPage()): ?>
                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
            <?php else: ?>
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="<?php echo e($categories->currentPage() - 1); ?>">&laquo;</a>
                </li>
            <?php endif; ?>

            
            <?php $__currentLoopData = range(1, max(1, $categories->lastPage())); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($page == $categories->currentPage()): ?>
                    <li class="page-item active"><span class="page-link"><?php echo e($page); ?></span></li>
                <?php elseif($page == 1 || $page == $categories->lastPage() || abs($page - $categories->currentPage()) <= 2): ?>
                    <li class="page-item">
                        <a class="page-link paginateLink" href="#"
                            data-page="<?php echo e($page); ?>"><?php echo e($page); ?></a>
                    </li>
                <?php elseif($page == $categories->currentPage() - 3 || $page == $categories->currentPage() + 3): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <?php if($categories->hasMorePages()): ?>
                <li class="page-item">
                    <a class="page-link paginateLink" href="#"
                        data-page="<?php echo e($categories->currentPage() + 1); ?>">&raquo;</a>
                </li>
            <?php else: ?>
                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
            <?php endif; ?>
        </ul>
    </nav>
</div><?php /**PATH D:\xampp\htdocs\Farmer\resources\views/admin/category/category-table.blade.php ENDPATH**/ ?>
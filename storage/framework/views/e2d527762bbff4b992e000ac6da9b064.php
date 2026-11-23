
<?php
    $hasFilters = $search !== '' || strtolower($status) !== 'all' || in_array($type, ['have', 'want'], true);
?>

<?php if($products->isEmpty()): ?>
    <div class="text-center py-5">
        <?php if($hasFilters): ?>
            <p class="text-muted mb-0">
                No products match your filters
                <?php if($search !== ''): ?>
                    for "<strong><?php echo e($search); ?></strong>"
                <?php endif; ?>
                .
            </p>
        <?php else: ?>
            <p class="text-muted mb-3">No products found.</p>
            <a href="<?php echo e(route('member.product.create')); ?>" class="btn-zentic btn-primary">
                <i class="icon-plus"></i> Create Product
            </a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="zentic-table table" id="productTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>SKU</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($products->firstItem() + $loop->index); ?></td>
                        <td>
                            <?php if(!empty($product->main_image)): ?>
                                <img src="<?php echo e(asset($product->main_image)); ?>" alt="<?php echo e($product->title); ?>"
                                    class="avatar-gen-img avatar-48 zentic-border-radius-xl"
                                    style="width:48px;height:48px;object-fit:cover;">
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($product->sku); ?></td>
                        <td class="fw-600" title="<?php echo e($product->title); ?>"><?php echo e(Str::limit($product->title, 30)); ?></td>
                        <td><?php echo e(optional($product->categorys)->name ?? '-'); ?></td>
                        <td>
                            <?php if($product->listing_type === 'want'): ?>
                                <span class="badge-zentic badge-orange">I Want</span>
                            <?php else: ?>
                                <span class="badge-zentic badge-green">I Have</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($product->completed_at): ?>
                                <span class="badge-zentic badge-orange badge-dot" title="Completed on <?php echo e(\Carbon\Carbon::parse($product->completed_at)->format('d M Y')); ?>">Completed</span>
                            <?php elseif($product->status === 'Approved'): ?>
                                <span class="badge-zentic badge-green badge-dot">Approved</span>
                            <?php elseif($product->status === 'Rejected'): ?>
                                    <span class="badge-zentic badge-red badge-dot">Rejected</span>
                            <?php else: ?>
                                <span class="badge-zentic badge-orange badge-dot text-dark">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($product->created_at?->format('d M Y') ?? '-'); ?></td>
                        <td class="text-end text-nowrap">
                            <a href="<?php echo e(route('member.product.edit', $product->id)); ?>"
                                class="btn-zentic btn-warning btn-sm" title="Edit">
                                <i class="icon-pencil"></i>
                            </a>
                            <button type="button" class="btn-zentic btn-danger btn-sm delete-product"
                                data-id="<?php echo e($product->id); ?>" title="Delete">
                                <i class="icon-trash"></i>
                            </button>
                            <?php if($product->completed_at): ?>
                                <button type="button" class="btn-zentic btn-primary btn-sm js-product-complete"
                                    data-id="<?php echo e($product->id); ?>" data-completed="0" title="Show on website again">
                                    <i class="icon-rotate-ccw"></i>
                                </button>
                            <?php elseif($product->status === 'Approved'): ?>
                                <button type="button" class="btn-zentic btn-success btn-sm js-product-complete"
                                        data-id="<?php echo e($product->id); ?>" data-completed="1" title="Deal done, hide from website">
                                    <i class="icon-check-circle"></i>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    
    <div class="product-pagination d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
        <small class="text-muted">
            Showing <?php echo e($products->firstItem()); ?> to <?php echo e($products->lastItem()); ?> of <?php echo e($products->total()); ?> entries
        </small>

        <?php if($products->lastPage() > 1): ?>
            <?php
                $current = $products->currentPage();
                $last = $products->lastPage();
                $start = max(1, $current - 2);
                $end = min($last, $current + 2);
            ?>
            <div class="d-flex flex-wrap gap-1">
                <button type="button" class="btn-zentic btn-outline btn-sm page-btn" data-page="<?php echo e($current - 1); ?>"
                    <?php echo e($current <= 1 ? 'disabled' : ''); ?>>
                    <i class="icon-chevron-left"></i>
                </button>

                <?php if($start > 1): ?>
                    <button type="button" class="btn-zentic btn-outline btn-sm page-btn" data-page="1">1</button>
                    <?php if($start > 2): ?>
                        <span class="px-1 align-self-center text-muted">…</span>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for($i = $start; $i <= $end; $i++): ?>
                    <button type="button"
                        class="btn-zentic btn-sm page-btn <?php echo e($i === $current ? 'btn-primary' : 'btn-outline'); ?>"
                        data-page="<?php echo e($i); ?>"><?php echo e($i); ?></button>
                <?php endfor; ?>

                <?php if($end < $last): ?>
                    <?php if($end < $last - 1): ?>
                        <span class="px-1 align-self-center text-muted">…</span>
                    <?php endif; ?>
                    <button type="button" class="btn-zentic btn-outline btn-sm page-btn"
                        data-page="<?php echo e($last); ?>"><?php echo e($last); ?></button>
                <?php endif; ?>

                <button type="button" class="btn-zentic btn-outline btn-sm page-btn" data-page="<?php echo e($current + 1); ?>"
                    <?php echo e($current >= $last ? 'disabled' : ''); ?>>
                    <i class="icon-chevron-right"></i>
                </button>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/member/products/table.blade.php ENDPATH**/ ?>
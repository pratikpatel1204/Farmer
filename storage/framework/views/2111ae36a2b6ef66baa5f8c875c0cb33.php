<?php
    $statusMap = [
        0 => ['label' => 'Pending', 'variant' => 'warning'],
        1 => ['label' => 'Contacted', 'variant' => 'success'],
        2 => ['label' => 'Closed', 'variant' => 'danger'],
    ];
?>

<?php $__empty_1 = true; $__currentLoopData = $inquiries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inquiry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php
        $status = array_key_exists((int) $inquiry->status, $statusMap) ? (int) $inquiry->status : 0;
        $product = $inquiry->product;
        $seller = $inquiry->member;
        $productUrl = $product ? route('products.show', $product->slug) : null;
        $modalData = [
            'fields' => [
                ['label' => 'Product', 'value' => $product->title ?? 'Product removed', 'type' => 'text'],
                ['label' => 'Seller', 'value' => $seller->full_name ?? '—', 'type' => 'text'],
                ['label' => 'Customer', 'value' => $inquiry->full_name, 'type' => 'text'],
                ['label' => 'Email', 'value' => $inquiry->email, 'type' => 'email'],
                ['label' => 'Phone', 'value' => $inquiry->phone, 'type' => 'tel'],
                ['label' => 'Date', 'value' => $inquiry->created_at?->format('d M Y, h:i A'), 'type' => 'text'],
            ],
            'message' => $inquiry->message,
        ];
    ?>
    <tr data-row-id="<?php echo e($inquiry->id); ?>">
        <td><?php echo e($inquiries->firstItem() + $loop->index); ?></td>

        
        <td>
            <div class="d-flex align-items-center gap-2">
                <?php if($product && $product->main_image): ?>
                    <a href="<?php echo e($productUrl); ?>" target="_blank">
                        <img class="inq-thumb" src="<?php echo e(asset($product->main_image)); ?>" alt="<?php echo e($product->title); ?>">
                    </a>
                <?php else: ?>
                    <span class="inq-thumb-ph"><i class="ti ti-photo"></i></span>
                <?php endif; ?>
                <div class="text-truncate" style="max-width: 170px;">
                    <?php if($product): ?>
                        <a href="<?php echo e($productUrl); ?>" target="_blank" class="fw-medium text-dark" title="<?php echo e($product->title); ?>">
                            <?php echo e($product->title); ?>

                        </a>
                    <?php else: ?>
                        <span class="text-muted fst-italic">Product removed</span>
                    <?php endif; ?>
                </div>
            </div>
        </td>

        
        <td>
            <?php if($seller): ?>
                <h6 class="fw-medium fs-14 mb-0"><?php echo e($seller->full_name); ?></h6>
                <?php if($seller->email): ?>
                    <span class="inq-sub"><?php echo e($seller->email); ?></span>
                <?php endif; ?>
            <?php else: ?>
                <span class="text-muted">—</span>
            <?php endif; ?>
        </td>

        
        <td>
            <div class="d-flex align-items-center gap-2">
                <span class="inq-initial"><?php echo e(strtoupper(mb_substr($inquiry->full_name ?: '?', 0, 1))); ?></span>
                <div>
                    <h6 class="fw-medium fs-14 mb-0"><?php echo e($inquiry->full_name); ?></h6>
                    <?php if($inquiry->email): ?>
                        <a href="mailto:<?php echo e($inquiry->email); ?>" class="inq-sub"><?php echo e($inquiry->email); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </td>

        
        <td class="text-nowrap">
            <?php if($inquiry->phone): ?>
                <a href="tel:<?php echo e($inquiry->phone); ?>" class="text-dark"><?php echo e($inquiry->phone); ?></a>
            <?php else: ?>
                —
            <?php endif; ?>
        </td>

        
        <td>
            <?php if($inquiry->message): ?>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted text-nowrap" title="<?php echo e($inquiry->message); ?>">
                        <?php echo e(\Illuminate\Support\Str::limit($inquiry->message, 25, '...')); ?>

                    </span>
                    <button type="button"
                            class="btn btn-sm btn-outline-primary d-inline-flex align-items-center text-nowrap py-1 px-2 inq-view-btn"
                            data-inquiry="<?php echo e(json_encode($modalData)); ?>" title="Read full message">
                            <i class="ti ti-eye me-1"></i>View
                        </button>
                </div>
            <?php else: ?>
                —
            <?php endif; ?>
        </td>

        
        <td class="text-nowrap">
            <div><?php echo e($inquiry->created_at?->format('d M Y')); ?></div>
            <div class="inq-sub"><?php echo e($inquiry->created_at?->format('h:i A')); ?></div>
        </td>

        
        <td>
            <select class="form-select inq-status-select status-<?php echo e($statusMap[$status]['variant']); ?>"
                data-id="<?php echo e($inquiry->id); ?>" data-current="<?php echo e($status); ?>">
                <?php $__currentLoopData = $statusMap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($value); ?>" <?php echo e($status === $value ? 'selected' : ''); ?>><?php echo e($opt['label']); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </td>
    </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <tr>
        <td colspan="8">
            <div class="inq-empty">
                <i class="ti ti-inbox"></i>
                <?php if(request()->anyFilled(['search', 'status', 'date_from', 'date_to'])): ?>
                    <div class="fw-medium text-dark">No matching inquiries</div>
                    <div>Try changing the search or filters.</div>
                <?php else: ?>
                    <div class="fw-medium text-dark">No Product Inquiries Yet!</div>
                    <div>Inquiries sent on member products will appear here.</div>
                <?php endif; ?>
            </div>
        </td>
    </tr>
<?php endif; ?><?php /**PATH /home/yftnutez/rgmmart.in/resources/views/admin/inquiries/partials/product_rows.blade.php ENDPATH**/ ?>
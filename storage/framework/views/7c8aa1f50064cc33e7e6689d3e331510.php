
<?php
    $fallbackIcons = [['fa-carrot', 'thumb-red'], ['fa-wheat-awn', 'thumb-yellow'], ['fa-seedling', 'thumb-lime'], ['fa-apple-whole', 'thumb-red'], ['fa-tractor', 'thumb-brown'], ['fa-lemon', 'thumb-yellow']];
?>

<div class="row <?php echo e($columns ?? 'row-cols-2 row-cols-md-3'); ?> g-2 g-sm-3 product-grid">
    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $cardType = \App\Support\Market::typeOf($product);
            $cat = \App\Support\Market::categoryFor(data_get($product, 'categorys.name') ?? $product->category);
            [$icon, $tint] = $cat
                ? [$cat['icon'], 'thumb-' . $cat['color']]
                : $fallbackIcons[$loop->index % count($fallbackIcons)];
        ?>
        <div class="col">
            <?php echo $__env->make('front.partials.product-card', ['product' => $product, 'type' => $cardType, 'icon' => $icon, 'tint' => $tint], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<?php if($products->hasPages()): ?>
    <div class="farm-pagination">
        <?php echo e($products->links('pagination::bootstrap-5')); ?>

    </div>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\Farmer\resources\views/front/partials/product-grid.blade.php ENDPATH**/ ?>
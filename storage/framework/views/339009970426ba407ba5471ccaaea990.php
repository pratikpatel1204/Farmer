
<?php
    $categoryName = data_get($product, 'categorys.name');
    $ownerName = data_get($product, 'member.full_name') ?? data_get($product, 'owner_name');
    $city = data_get($product, 'member.citys.name');
    $state = data_get($product, 'member.states.name');
    $location = collect([$city, $state])->filter()->implode(', ') ?: (data_get($product, 'member.area') ?? data_get($product, 'owner_area'));
    $productUrl = url('product/' . $product->slug);
    // Main image, warna People Want me category ki image
    $thumbImage = \App\Support\Market::productImage($product);
?>
<div class="product-card card-<?php echo e($type); ?>">
    <a href="<?php echo e($productUrl); ?>" class="product-thumb <?php echo e($thumbImage ? '' : $tint); ?>">
        <?php if($thumbImage): ?>
            <img src="<?php echo e($thumbImage); ?>" alt="<?php echo e($product->title); ?>" loading="lazy">
        <?php else: ?>
            <i class="fa-solid <?php echo e($icon); ?> thumb-icon" aria-hidden="true"></i>
        <?php endif; ?>

        <span class="product-type <?php echo e($type === 'want' ? 'type-want' : 'type-have'); ?>">
            <?php if($categoryName): ?>
                <i class="fa-solid fa-tag"></i><?php echo e($categoryName); ?>

            <?php elseif($type === 'want'): ?>
                <i class="fa-solid fa-cart-shopping"></i>Wanted
            <?php else: ?>
                <i class="fa-solid fa-seedling"></i>Available
            <?php endif; ?>
        </span>
    </a>

    <div class="product-body">
        <h3 class="product-title"><a href="<?php echo e($productUrl); ?>"><?php echo e($product->title); ?></a></h3>
        <ul class="product-owner">
            <?php if($ownerName): ?>
                <li>
                    <i class="fa-solid <?php echo e($type === 'want' ? 'fa-user-tie' : 'fa-user'); ?>"></i>
                    <span><?php echo e($ownerName); ?></span>
                </li>
            <?php endif; ?>
            <?php if($location): ?>
                <li>
                    <i class="fa-solid fa-location-dot"></i>
                    <span><?php echo e($location); ?></span>
                </li>
            <?php endif; ?>
        </ul>
        <a href="<?php echo e($productUrl); ?>" class="btn btn-product">
            <?php if($type === 'want'): ?>
                <i class="fa-solid fa-hand-holding-heart me-1"></i>People Want
            <?php else: ?>
                <i class="fa-solid fa-phone me-1"></i>Contact seller
            <?php endif; ?>
        </a>
    </div>
</div>
<?php /**PATH D:\xampp\htdocs\Farmer\resources\views/front/partials/product-card.blade.php ENDPATH**/ ?>
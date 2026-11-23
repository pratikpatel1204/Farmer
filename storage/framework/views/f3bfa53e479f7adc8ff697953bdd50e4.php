
<?php
    $blogUrl = url('blog/' . ($blog->slug ?? $blog->id));
    $tint = 'tint-' . ((($index ?? 0) % 4) + 1);
    $blogIcons = ['fa-leaf', 'fa-cloud-sun-rain', 'fa-chart-line', 'fa-tractor'];
?>
<article class="blog-card">
    <a href="<?php echo e($blogUrl); ?>" class="blog-thumb <?php echo e($tint); ?>">
        <?php if(!empty($blog->image)): ?>
            <img src="<?php echo e(asset($blog->image)); ?>" alt="<?php echo e($blog->title ?? ''); ?>" loading="lazy">
        <?php else: ?>
            <i class="fa-solid <?php echo e($blogIcons[($index ?? 0) % 4]); ?> thumb-icon" aria-hidden="true"></i>
        <?php endif; ?>
        <span class="blog-date"><?php echo e(\Carbon\Carbon::parse($blog->created_at ?? now())->format('d M Y')); ?></span>
    </a>
    <div class="blog-body">
        <h3 class="blog-title"><a href="<?php echo e($blogUrl); ?>"><?php echo e($blog->title ?? ''); ?></a></h3>
        <p class="blog-excerpt"><?php echo e(\Illuminate\Support\Str::limit(strip_tags(preg_replace('#</(p|h[1-6]|li|div)>#i', ' ', $blog->description ?? '')), 120)); ?></p>
        <a href="<?php echo e($blogUrl); ?>" class="blog-more">Read more <i class="fa-solid fa-arrow-right ms-1"></i></a>
    </div>
</article>
<?php /**PATH /home/yftnutez/rgmmart.in/resources/views/front/partials/blog-card.blade.php ENDPATH**/ ?>
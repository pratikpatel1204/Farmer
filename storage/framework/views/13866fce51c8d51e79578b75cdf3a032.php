<div class="filter-box">
    <h3 class="filter-title">Search articles</h3>
    <form action="<?php echo e(url('blog')); ?>" method="GET" class="filter-search" role="search">
        <input type="search" name="q" value="<?php echo e(request('q')); ?>" class="form-control" placeholder="e.g. soil, monsoon…" aria-label="Search articles">
        <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>
</div>

<?php if($recent->count()): ?>
    <div class="filter-box">
        <h3 class="filter-title">Recent posts</h3>
        <ul class="recent-posts">
            <?php $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                    <a href="<?php echo e(url('blog/' . ($post->slug ?? $post->id))); ?>">
                        <span class="recent-thumb">
                            <?php if(!empty($post->image)): ?>
                                <img src="<?php echo e(asset($post->image)); ?>" alt="" loading="lazy">
                            <?php else: ?>
                                <i class="fa-solid fa-leaf"></i>
                            <?php endif; ?>
                        </span>
                        <span>
                            <strong><?php echo e($post->title); ?></strong>
                            <small><?php echo e(\Carbon\Carbon::parse($post->created_at ?? now())->format('d M Y')); ?></small>
                        </span>
                    </a>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<div class="side-cta">
    <h3>Grow with KisanMandi</h3>
    <p>Sell your produce or find what you need, free of cost.</p>
    <a href="<?php echo e(url('register')); ?>" class="btn btn-farm btn-farm-yellow w-100">Register free</a>
</div>
<?php /**PATH /home/yftnutez/rgmmart.in/resources/views/front/blog/_sidebar.blade.php ENDPATH**/ ?>
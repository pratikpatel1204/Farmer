<?php if($inquiries->hasPages()): ?>
    <?php echo e($inquiries->onEachSide(1)->links('pagination::bootstrap-5')); ?>

<?php elseif($inquiries->total() > 0): ?>
    <p class="small text-muted mb-0">
        Showing <?php echo e($inquiries->firstItem()); ?> to <?php echo e($inquiries->lastItem()); ?> of <?php echo e($inquiries->total()); ?> results
    </p>
<?php endif; ?>
<?php /**PATH /home/yftnutez/rgmmart.in/resources/views/admin/inquiries/partials/pagination.blade.php ENDPATH**/ ?>
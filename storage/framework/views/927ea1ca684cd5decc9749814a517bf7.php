
<?php $footerContact = \App\Models\ContactSetting::current(); ?>
<footer class="site-footer">
    <div class="field-stripe" aria-hidden="true"></div>
    <div class="container-xl">
        <div class="row g-4 footer-top">
            <div class="col-12 col-lg-4">
                <a href="<?php echo e(url('/')); ?>" class="brand-name footer-brand">
                    
                    <img src="<?php echo e(asset('front/image/logo.png')); ?>" alt="KisanMandi" class="brand-logo-img"
                        style="height: 50px; width: auto; max-width: 200px; object-fit: contain;">
                </a>
                <p class="footer-about">
                    An open marketplace where farmers sell directly to buyers. No middlemen, fair
                    prices, verified members.
                </p>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <h2 class="footer-title">Explore</h2>
                <ul class="footer-links">
                    <li><a href="<?php echo e(url('i-have')); ?>">I Have</a></li>
                    <li><a href="<?php echo e(url('i-want')); ?>">I Want</a></li>
                    <li><a href="<?php echo e(url('category')); ?>">Categories</a></li>
                    <li><a href="<?php echo e(url('blog')); ?>">Blog</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <h2 class="footer-title">Company</h2>
                <ul class="footer-links">
                    <li><a href="<?php echo e(url('about')); ?>">About Us</a></li>
                    <li><a href="<?php echo e(url('contact-us')); ?>">Contact Us</a></li>
                    <li><a href="<?php echo e(url('login')); ?>">Login</a></li>
                    <li><a href="<?php echo e(url('register')); ?>">Register</a></li>
                </ul>
            </div>
            <div class="col-12 col-md-4 col-lg-4">
                <h2 class="footer-title">Get in touch</h2>
                <ul class="footer-contact">
                    <?php if($footerContact->phone): ?>
                        <li>
                            <i class="fa-solid fa-phone"></i>
                            <a href="<?php echo e($footerContact->phone_link); ?>">
                                <?php echo e($footerContact->phone); ?>

                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if($footerContact->email): ?>
                        <li>
                            <i class="fa-solid fa-envelope"></i>
                            <a href="<?php echo e($footerContact->email_link); ?>">
                                <?php echo e($footerContact->email); ?>

                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if($footerContact->office_address): ?>
                        <li>
                            <i class="fa-solid fa-location-dot"></i>
                            <?php echo e($footerContact->office_address); ?>

                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; <?php echo e(date('Y')); ?> RGM Mart. All rights reserved.</span>
            <span>Made for Indian farmers <i class="fa-solid fa-seedling"></i></span>
        </div>
    </div>
</footer>
<?php /**PATH /home/yftnutez/rgmmart.in/resources/views/front/layout/footer.blade.php ENDPATH**/ ?>
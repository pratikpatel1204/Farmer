{{-- resources/views/front/partials/cta-band.blade.php : page ke niche "Join" patti --}}
<section class="page-section pb-0">
    <div class="container-xl">
        <div class="cta-band">
            <div>
                <h2>{{ $title ?? 'Start buying and selling today' }}</h2>
                <p>{{ $text ?? 'Create a free profile in 2 minutes and connect directly with farmers and buyers.' }}</p>
            </div>
            <div class="cta-actions">
                <a href="{{ url('register') }}" class="btn btn-farm btn-farm-yellow"><i class="fa-solid fa-user-plus me-1"></i>Register free</a>
                <a href="{{ url('contact-us') }}" class="btn btn-light-glass">Talk to us</a>
            </div>
        </div>
    </div>
</section>

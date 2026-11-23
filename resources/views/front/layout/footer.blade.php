{{-- resources/views/front/layout/footer.blade.php
Naya footer (header jaisa design). Apna purana footer rakhna ho to ye file copy na karein. --}}
@php $footerContact = \App\Models\ContactSetting::current(); @endphp
<footer class="site-footer">
    <div class="field-stripe" aria-hidden="true"></div>
    <div class="container-xl">
        <div class="row g-4 footer-top">
            <div class="col-12 col-lg-4">
                <a href="{{ url('/') }}" class="brand-name footer-brand">
                    {{-- <span class="brand-name-first">Kisan</span>
                    <span class="brand-name-second">Mandi</span> --}}
                    <img src="{{ asset('front/image/logo.png') }}" alt="KisanMandi" class="brand-logo-img"
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
                    <li><a href="{{ url('i-have') }}">I Have</a></li>
                    <li><a href="{{ url('i-want') }}">I Want</a></li>
                    <li><a href="{{ url('category') }}">Categories</a></li>
                    <li><a href="{{ url('blog') }}">Blog</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <h2 class="footer-title">Company</h2>
                <ul class="footer-links">
                    <li><a href="{{ url('about') }}">About Us</a></li>
                    <li><a href="{{ url('contact-us') }}">Contact Us</a></li>
                    <li><a href="{{ url('login') }}">Login</a></li>
                    <li><a href="{{ url('register') }}">Register</a></li>
                </ul>
            </div>
            <div class="col-12 col-md-4 col-lg-4">
                <h2 class="footer-title">Get in touch</h2>
                <ul class="footer-contact">
                    @if ($footerContact->phone)
                        <li>
                            <i class="fa-solid fa-phone"></i>
                            <a href="{{ $footerContact->phone_link }}">
                                {{ $footerContact->phone }}
                            </a>
                        </li>
                    @endif
                    @if ($footerContact->email)
                        <li>
                            <i class="fa-solid fa-envelope"></i>
                            <a href="{{ $footerContact->email_link }}">
                                {{ $footerContact->email }}
                            </a>
                        </li>
                    @endif
                    @if ($footerContact->office_address)
                        <li>
                            <i class="fa-solid fa-location-dot"></i>
                            {{ $footerContact->office_address }}
                        </li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} RGM Mart. All rights reserved.</span>
            <span>Made for Indian farmers <i class="fa-solid fa-seedling"></i></span>
        </div>
    </div>
</footer>

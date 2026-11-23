{{-- resources/views/front/partials/page-hero.blade.php
     Har inner page ke upar rangeen banner.
     Variables:
       $title     (zaroori)
       $text      chhota subtitle
       $badge     ['icon' => 'fa-leaf', 'label' => 'About us']
       $variant   green | want | have | soil | sun   (default green)
       $art       bada decorative icon, jaise 'fa-wheat-awn'
       $crumbs    ['Category' => url('category'), 'Vegetables' => null]   (Home apne aap lagta hai)
     Buttons ke liye @slot nahi, $actions me HTML (optional). --}}
@php
    $variant = $variant ?? 'green';
    $crumbs = $crumbs ?? [];
@endphp
<div class="page-hero-wrap">
    <div class="container-fluid">
        <section class="page-hero hero-{{ $variant }}">
            @if (!empty($art))
                <i class="fa-solid {{ $art }} page-hero-art" aria-hidden="true"></i>
            @endif

            <div class="page-hero-content">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        @foreach ($crumbs as $label => $link)
                            @if ($link && !$loop->last)
                                <li class="breadcrumb-item"><a href="{{ $link }}">{{ $label }}</a></li>
                            @else
                                <li class="breadcrumb-item active" aria-current="page">{{ $label }}</li>
                            @endif
                        @endforeach
                    </ol>
                </nav>

                @if (!empty($badge))
                    <span class="page-hero-badge"><i class="fa-solid {{ $badge['icon'] }}"></i>{{ $badge['label'] }}</span>
                @endif

                <h1 class="page-hero-title">{{ $title }}</h1>

                @if (!empty($text))
                    <p class="page-hero-text">{{ $text }}</p>
                @endif

                @if (!empty($actions))
                    <div class="page-hero-actions">{!! $actions !!}</div>
                @endif
            </div>
        </section>
    </div>
</div>

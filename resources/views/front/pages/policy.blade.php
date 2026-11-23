@extends('front.layout.app')

@section('title', $title . ' - ' . config('app.name'))

@push('styles')
    {{-- YAHA apne about/contact page wali home.css aur pages.css ki 2 link lines paste karein --}}
@endpush

@section('content')
<div class="inner-page">

    @include('front.partials.page-hero', [
        'title'  => $title,
        'text'   => $text,
        'badge'  => ['icon' => $icon, 'label' => 'Legal'],
        'art'    => $icon,
        'crumbs' => [$title => null],
    ])

    <section class="page-section">
        <div class="container-xl">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10 col-xl-9">
                    <div class="bg-white border rounded-4 shadow-sm p-3 p-md-4 p-lg-5">

                        @if ($updatedAt)
                            <p class="text-muted small mb-4">
                                <i class="fa-solid fa-calendar-check me-1"></i>
                                Last updated: {{ $updatedAt->format('d M Y') }}
                            </p>
                        @endif

                        @if ($content)
                            <div class="rich-text policy-content" style="line-height:1.75">
                                {!! $content !!}
                            </div>
                        @else
                            <p class="text-muted mb-0">This page will be updated soon.</p>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
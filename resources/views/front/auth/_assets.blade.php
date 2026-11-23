{{-- resources/views/front/auth/_assets.blade.php
     Auth pages ki common files: CSS, jQuery, toastr (sab public/front se, offline).
     Use: @include('front.auth._assets')  ya  @include('front.auth._assets', ['select2' => true]) --}}
@push('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('front/css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/pages.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/toastr.min.css') }}">
    @if (!empty($select2))
        <link rel="stylesheet" href="{{ asset('front/vendor/select2/select2.min.css') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('front/css/auth.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('front/js/pages.js') }}"></script>
    <script src="{{ asset('front/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('front/js/toastr.min.js') }}"></script>
    @if (!empty($select2))
        <script src="{{ asset('front/vendor/select2/select2.min.js') }}"></script>
    @endif
    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 4000,
            preventDuplicates: true
        };
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
@endpush

@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || Product Details')
@section('content')
    <div class="content">

        <div class="d-md-flex align-items-center justify-content-between mb-3">
            <div>
                <h2 class="mb-1">Product Details</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}"><i
                                    class="ti ti-smart-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.product.list') }}">Product Master</a></li>
                        <li class="breadcrumb-item active">{{ $product->title }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2 mt-2 mt-md-0">
                <a href="{{ route('admin.product.list') }}" class="btn btn-light"><i
                        class="ti ti-arrow-left me-1"></i>Back</a>
                @if ($product->status !== 'Approved')
                    <button class="btn btn-success" id="btnApprove"><i class="ti ti-check me-1"></i>Approve</button>
                @endif
                @if ($product->status !== 'Rejected')
                    <button class="btn btn-warning" id="btnReject"><i class="ti ti-x me-1"></i>Reject</button>
                @endif
            </div>
        </div>

        @if ($product->status === 'Rejected' && $product->rejection_reason)
            <div class="alert alert-danger"><strong>Rejection reason:</strong> {{ $product->rejection_reason }}</div>
        @endif

        <div class="row">
            {{-- Images --}}
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-body">
                        @if ($mainImage = \App\Support\Market::productImage($product))
                            <img src="{{ $mainImage }}" class="img-fluid rounded w-100 mb-3"
                                alt="{{ $product->title }}">
                            @if (empty($product->main_image))
                                <small class="text-muted d-block mb-3">No product image uploaded. Showing the category image.</small>
                            @endif
                        @endif
                        @if (count($product->sub_images))
                            <div class="row g-2">
                                @foreach ($product->sub_images as $img)
                                    <div class="col-3">
                                        <a href="{{ asset($img) }}" target="_blank">
                                            <img src="{{ asset($img) }}" class="img-fluid rounded border" alt="">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Details --}}
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $product->title }}</h5>
                        @if ($product->status === 'Approved')
                            <span class="badge badge-soft-success">Approved</span>
                        @elseif($product->status === 'Rejected')
                            <span class="badge badge-soft-danger">Rejected</span>
                        @else
                            <span class="badge badge-soft-warning">Pending</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <th style="width:35%">SKU</th>
                                <td>{{ $product->sku }}</td>
                            </tr>
                            <tr>
                                <th>Seller</th>
                                <td>
                                    @if ($product->member)
                                        {{ $product->member->full_name }}
                                        <small class="text-muted">({{ $product->member->unique_id }})</small>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Tags</th>
                                <td>{{ $product->product_tags ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Category</th>
                                <td>{{ $product->categorys->name ?? ($product->category ?? '-') }}</td>
                            </tr>
                            <tr>
                                <th>Created</th>
                                <td>{{ $product->created_at?->format('d M Y, h:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Brochure</th>
                                <td>
                                    @if ($product->brochure)
                                        <a href="{{ asset($product->brochure) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-download me-1"></i>Download</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if ($product->attributesList->count())
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Attributes</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Label</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($product->attributesList as $attr)
                                        <tr>
                                            <td>{{ $attr->label }}</td>
                                            <td>{{ $attr->value }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Description</h5>
            </div>
            {{-- Description is HTML, so {!! !!} is used. Purify it when saving (mews/purifier), otherwise XSS risk --}}
            <div class="card-body">{!! $product->description !!}</div>
        </div>
    </div>

    <script>
        $(function() {
            const csrf = "{{ csrf_token() }}";
            const id = {{ $product->id }};

            function post(url, data) {
                $.ajax({
                        url,
                        type: 'POST',
                        data: {
                            ...data,
                            _token: csrf
                        }
                    })
                    .done(res => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    })
                    .fail(x => {
                        let msg = 'Something went wrong. Please try again.';
                        if (x.responseJSON?.errors) {
                            msg = Object.values(x.responseJSON.errors)[0][0];
                        } else if (x.responseJSON?.message) {
                            msg = x.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: msg
                        });
                    });
            }

            // Approve
            $('#btnApprove').on('click', function() {
                Swal.fire({
                    title: 'Approve this product?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    confirmButtonText: 'Yes, approve it',
                    cancelButtonText: 'Cancel'
                }).then(result => {
                    if (result.isConfirmed) post("{{ route('admin.product.approve') }}", {
                        id: id
                    });
                });
            });

            // Reject (reason is asked inside SweetAlert)
            $('#btnReject').on('click', function() {
                Swal.fire({
                    title: 'Reject this product?',
                    input: 'textarea',
                    inputLabel: 'Rejection reason',
                    inputPlaceholder: 'Write the reason for rejection...',
                    inputAttributes: {
                        maxlength: 1000
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Reject',
                    cancelButtonText: 'Cancel',
                    inputValidator: value => {
                        if (!value || value.trim() === '') {
                            return 'Rejection reason is required.';
                        }
                    }
                }).then(result => {
                    if (result.isConfirmed) post("{{ route('admin.product.reject') }}", {
                        id: id,
                        rejection_reason: result.value.trim()
                    });
                });
            });
        });
    </script>
@endsection

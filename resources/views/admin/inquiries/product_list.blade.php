@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || Product Inquiries')

@section('content')
    @include('admin.inquiries.partials.styles')

    <div class="content">

        {{-- Breadcrumb --}}
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Product Inquiries</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">Inquiries</li>
                        <li class="breadcrumb-item active" aria-current="page">Product Inquiry</li>
                    </ol>
                </nav>
            </div>
        </div>

        @include('admin.inquiries.partials.stats', ['counts' => $counts])

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                <h5 class="mb-0">Product Inquiry List</h5>
                <span class="badge badge-soft-primary" id="inqResultBadge">Results : {{ $inquiries->total() }}</span>
            </div>

            <div class="card-body">
                {{-- Filters --}}
                <form id="inqFilterForm" class="row g-2 align-items-end mb-3" autocomplete="off">
                    <div class="col-xl-4 col-md-12">
                        <label class="form-label fs-12 mb-1" for="inqSearch">Search</label>
                        <div class="inq-search-box">
                            <i class="ti ti-search"></i>
                            <input type="text" id="inqSearch" name="search" class="form-control inq-filter"
                                placeholder="Customer, seller, product, email, phone, message..."
                                value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <label class="form-label fs-12 mb-1" for="inqStatus">Status</label>
                        <select id="inqStatus" name="status" class="form-select inq-filter">
                            <option value="">All Status</option>
                            <option value="0" @selected(request('status') === '0')>Pending</option>
                            <option value="1" @selected(request('status') === '1')>Contacted</option>
                            <option value="2" @selected(request('status') === '2')>Closed</option>
                        </select>
                    </div>
                    <div class="col-xl-1 col-md-4 col-6">
                        <label class="form-label fs-12 mb-1" for="inqDateFrom">From</label>
                        <input type="date" id="inqDateFrom" name="date_from" class="form-control inq-filter"
                            value="{{ request('date_from') }}">
                    </div>
                    <div class="col-xl-1 col-md-4 col-6">
                        <label class="form-label fs-12 mb-1" for="inqDateTo">To</label>
                        <input type="date" id="inqDateTo" name="date_to" class="form-control inq-filter"
                            value="{{ request('date_to') }}">
                    </div>
                    <div class="col-xl-2 col-md-6 col-6">
                        <label class="form-label fs-12 mb-1" for="inqPerPage">Show</label>
                        <select id="inqPerPage" name="per_page" class="form-select inq-filter">
                            @foreach ([10, 25, 50, 100] as $size)
                                <option value="{{ $size }}" @selected((int) request('per_page', 10) === $size)>{{ $size }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-6 col-12">
                        <button type="button" id="inqResetBtn" class="btn btn-light border w-100">
                            <i class="ti ti-refresh me-1"></i>Reset
                        </button>
                    </div>
                </form>

                {{-- Table --}}
                <div class="inq-table-wrap">
                    <div class="inq-loader d-none" id="inqLoader">
                        <div class="inq-spinner"></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover inq-table" id="inqTable">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width:50px;">#</th>
                                    <th>Product</th>
                                    <th>Seller</th>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="inqTableBody">
                                @include('admin.inquiries.partials.product_rows', [
                                    'inquiries' => $inquiries,
                                ])
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="inqPagination" class="mt-3">
                    @include('admin.inquiries.partials.pagination', ['inquiries' => $inquiries])
                </div>
            </div>
        </div>
    </div>

    @include('admin.inquiries.partials.modal')

    @include('admin.inquiries.partials.scripts', [
        'listRoute' => route('admin.product.inquiries.list'),
        'statusRoute' => route('admin.product.inquiries.status.update'),
        'currentPage' => $inquiries->currentPage(),
    ])
@endsection

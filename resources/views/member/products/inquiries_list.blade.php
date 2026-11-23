@extends('member.layout.main-layout')
@section('title', config('app.name') . ' || Product Inquiries')

@section('content')
    {{-- Styles yahin rakhe hain taaki layout me @stack('styles') na ho tab bhi lagein --}}
    <style>
        /* ---------- Stat cards ---------- */
        .inq-stat-card { cursor: pointer; border: 1px solid #E8EBED; transition: all .2s ease; height: 100%; }
        .inq-stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0, 0, 0, .06); }
        .inq-stat-card.active { border-color: var(--inq-color); box-shadow: 0 0 0 1px var(--inq-color); }
        .inq-stat-label { font-size: 13px; color: #6B7280; margin-bottom: 4px; }
        .inq-stat-count { font-size: 24px; font-weight: 700; color: #111827; margin: 0; }
        .inq-stat-icon {
            width: 46px; height: 46px; border-radius: 10px; font-size: 20px; color: #fff;
            display: inline-flex; align-items: center; justify-content: center; background: var(--inq-color);
        }
        .inq-stat-primary { --inq-color: #F26522; }
        .inq-stat-warning { --inq-color: #FFC107; }
        .inq-stat-success { --inq-color: #03C95A; }
        .inq-stat-danger  { --inq-color: #E70D0D; }

        /* ---------- Filters ---------- */
        .inq-search-box { position: relative; }
        .inq-search-box > i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9CA3AF; }
        .inq-search-box input { padding-left: 36px; }
        .inq-filter-label { font-size: 12px; font-weight: 500; color: #6B7280; margin-bottom: 4px; display: block; }

        /* ---------- Loader ---------- */
        .inq-grid-wrap { position: relative; min-height: 220px; }
        .inq-loader {
            position: absolute; inset: 0; z-index: 5; background: rgba(255, 255, 255, .7);
            display: flex; align-items: flex-start; justify-content: center; padding-top: 80px;
        }
        .inq-spinner {
            width: 34px; height: 34px; border-radius: 50%;
            border: 3px solid #F3F4F6; border-top-color: #F26522; animation: inq-spin .7s linear infinite;
        }
        @keyframes inq-spin { to { transform: rotate(360deg); } }

        /* ---------- Product cards ---------- */
        .inq-product-card {
            height: 100%; display: flex; flex-direction: column; overflow: hidden; cursor: pointer;
            background: #fff; border: 1px solid #E8EBED; border-radius: 12px; transition: all .2s ease;
        }
        .inq-product-card:hover, .inq-product-card:focus-visible {
            border-color: #F26522; box-shadow: 0 10px 24px rgba(17, 24, 39, .08); transform: translateY(-2px); outline: none;
        }
        .inq-pc-media { position: relative; aspect-ratio: 4 / 3; background: #F3F4F6; }
        .inq-pc-media img { width: 100%; height: 250px; object-fit: cover; display: block; }
        .inq-pc-ph { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; color: #C4C9D0; font-size: 34px; }
        .inq-pc-count {
            position: absolute; left: 10px; bottom: 10px; display: inline-flex; align-items: center; gap: 5px;
            background: #F26522; color: #fff; font-size: 13px; font-weight: 700; padding: 5px 11px; border-radius: 20px;
            box-shadow: 0 4px 10px rgba(242, 101, 34, .35);
        }
        .inq-pc-type {
            position: absolute; left: 10px; top: 10px; background: rgba(17, 24, 39, .72); color: #fff;
            font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 20px;
        }
        .inq-pc-new {
            position: absolute; right: 10px; top: 10px; display: none; background: #FFC107; color: #4A3500;
            font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 20px;
        }
        .inq-product-card.has-pending .inq-pc-new { display: inline-block; }
        .inq-pc-body { padding: 14px 14px 12px; display: flex; flex-direction: column; flex: 1; }
        .inq-pc-title {
            font-size: 15px; font-weight: 600; color: #111827; margin: 0 0 2px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .inq-pc-pills { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 12px; }
        .inq-pill { font-size: 11.5px; padding: 3px 8px; border-radius: 6px; white-space: nowrap; }
        .inq-pill b { font-weight: 700; }
        .pill-warning { background: #FFF8E1; color: #946A00; }
        .pill-success { background: #E6F9EE; color: #027A36; }
        .pill-danger  { background: #FDE7E7; color: #B00A0A; }
        .inq-pc-foot {
            margin-top: auto; padding-top: 10px; border-top: 1px dashed #E8EBED;
            display: flex; align-items: center; justify-content: space-between; gap: 8px;
        }
        .inq-pc-link { font-size: 12.5px; font-weight: 600; color: #F26522; white-space: nowrap; }
        .inq-sub { font-size: 12px; color: #6B7280; }

        .inq-empty { padding: 50px 0; text-align: center; color: #6B7280; }
        .inq-empty i { font-size: 38px; color: #D1D5DB; display: block; margin-bottom: 8px; }
        #inqPagination .pagination { margin-bottom: 0; }

        /* ---------- Modal (Bootstrap JS ki zarurat nahi) ---------- */
        .inq-modal-backdrop {
            position: fixed; inset: 0; z-index: 1060; background: rgba(17, 24, 39, .55);
            display: flex; align-items: center; justify-content: center; padding: 16px;
            opacity: 0; visibility: hidden; transition: opacity .2s, visibility .2s;
        }
        .inq-modal-backdrop.show { opacity: 1; visibility: visible; }
        .inq-modal {
            background: #fff; border-radius: 14px; width: 100%; max-width: 760px; max-height: 92vh;
            display: flex; flex-direction: column; box-shadow: 0 20px 50px rgba(0, 0, 0, .2);
            transform: translateY(16px); transition: transform .2s;
        }
        .inq-modal-backdrop.show .inq-modal { transform: translateY(0); }
        .inq-modal-header {
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            padding: 16px 20px; border-bottom: 1px solid #E8EBED;
        }
        .inq-min-w-0 { min-width: 0; }
        .inq-pm-img { width: 52px; height: 52px; border-radius: 10px; object-fit: cover; border: 1px solid #E8EBED; flex-shrink: 0; }
        .inq-pm-ph { display: inline-flex; align-items: center; justify-content: center; background: #F3F4F6; color: #9CA3AF; font-size: 20px; }
        .inq-pm-title { margin: 0; font-size: 17px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .inq-modal-close {
            border: 0; background: #F3F4F6; width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0;
            font-size: 22px; line-height: 1; color: #374151; cursor: pointer;
        }
        .inq-modal-close:hover { background: #E5E7EB; }

        .inq-tabs { display: flex; gap: 6px; padding: 12px 20px; border-bottom: 1px solid #E8EBED; overflow-x: auto; }
        .inq-tab {
            border: 1px solid #E5E7EB; background: #fff; color: #374151; font-size: 13px; font-weight: 500;
            border-radius: 20px; padding: 5px 12px; white-space: nowrap; cursor: pointer;
        }
        .inq-tab span { display: inline-block; min-width: 20px; margin-left: 4px; padding: 0 6px; border-radius: 10px; background: #F3F4F6; font-size: 11.5px; font-weight: 700; }
        .inq-tab.active { background: #F26522; border-color: #F26522; color: #fff; }
        .inq-tab.active span { background: rgba(255, 255, 255, .25); }

        .inq-modal-body { position: relative; padding: 16px 20px; overflow-y: auto; min-height: 180px; }
        .inq-modal-footer { display: flex; justify-content: flex-end; padding: 12px 20px; border-top: 1px solid #E8EBED; }
        body.inq-modal-open { overflow: hidden; }

        .inq-item { border: 1px solid #E8EBED; border-radius: 10px; padding: 14px; }
        .inq-item + .inq-item { margin-top: 12px; }
        .inq-item-head { display: flex; align-items: flex-start; gap: 12px; }
        .inq-avatar {
            width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0; font-weight: 600;
            background: #FFF0E8; color: #F26522; display: inline-flex; align-items: center; justify-content: center;
        }
        .inq-item-who { flex: 1; min-width: 0; }
        .inq-item-name { font-weight: 600; color: #111827; }
        .inq-item-contact { display: flex; flex-wrap: wrap; gap: 4px 14px; font-size: 12.5px; margin-top: 2px; }
        .inq-item-contact a { color: #4B5563; word-break: break-all; }
        .inq-item-contact a:hover { color: #F26522; }
        .inq-item-contact .inq-wa { color: #15803D; font-weight: 600; }
        .inq-item-side { display: flex; flex-direction: column; align-items: flex-end; gap: 4px; flex-shrink: 0; }
        .inq-item-msg {
            margin-top: 12px; background: #F9FAFB; border-radius: 8px; padding: 10px 12px;
            font-size: 14px; line-height: 1.65; color: #1F2937; white-space: pre-wrap; word-break: break-word;
        }
        .inq-item-msg.is-long:not(.expanded) {
            display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden;
        }
        .inq-readmore { border: 0; background: none; padding: 4px 0 0; color: #F26522; font-size: 12.5px; font-weight: 600; cursor: pointer; }

        .inq-status-select {
            min-width: 120px; font-size: 12.5px; font-weight: 600; border-radius: 20px;
            padding: 4px 28px 4px 12px; cursor: pointer;
        }
        .inq-status-select.status-warning { background-color: #FFF8E1; color: #B78103; border-color: #FFE08A; }
        .inq-status-select.status-success { background-color: #E6F9EE; color: #028A3D; border-color: #A6EBC4; }
        .inq-status-select.status-danger  { background-color: #FDE7E7; color: #C20B0B; border-color: #F7B4B4; }

        @media (max-width: 575px) {
            .inq-item-head { flex-wrap: wrap; }
            .inq-item-side { flex-direction: row; align-items: center; width: 100%; justify-content: space-between; }
        }
    </style>

    <main class="zentic-content">

        {{-- ================= Stat Cards ================= --}}
        @php
            $statCards = [
                ['status' => '',  'key' => 'total',     'label' => 'Total Inquiries', 'icon' => 'icon-inbox',      'variant' => 'primary'],
                ['status' => '0', 'key' => 'pending',   'label' => 'Pending',         'icon' => 'icon-clock',      'variant' => 'warning'],
                ['status' => '1', 'key' => 'contacted', 'label' => 'Contacted',       'icon' => 'icon-phone-call', 'variant' => 'success'],
                ['status' => '2', 'key' => 'closed',    'label' => 'Closed',          'icon' => 'icon-circle-x',   'variant' => 'danger'],
            ];
            $activeStatus = (string) request('status', '');
            $activeSort = (string) request('sort', 'latest');
        @endphp

        <div class="row g-3 mb-4">
            @foreach ($statCards as $card)
                <div class="col-xl-3 col-sm-6">
                    <div class="zentic-card inq-stat-card inq-stat-{{ $card['variant'] }} {{ $activeStatus === $card['status'] ? 'active' : '' }}"
                        data-status="{{ $card['status'] }}" role="button">
                        <div class="zentic-card-body d-flex align-items-center justify-content-between">
                            <div>
                                <p class="inq-stat-label">{{ $card['label'] }}</p>
                                <h3 class="inq-stat-count" data-count="{{ $card['key'] }}">{{ (int) ($counts[$card['key']] ?? 0) }}</h3>
                            </div>
                            <span class="inq-stat-icon"><i class="{{ $card['icon'] }}"></i></span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ================= Product Cards ================= --}}
        <div class="zentic-card mb-4">
            <div class="zentic-card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h2 class="card-title mb-0">Product Inquiries</h2>
                <span class="badge-zentic badge-green text-xs" id="inqResultBadge">Products : {{ $groups->total() ?? 0 }}</span>
            </div>

            <div class="zentic-card-body">
                {{-- Filters --}}
                <form id="inqFilterForm" class="row g-2 align-items-end mb-4" autocomplete="off">
                    <div class="col-xl-3 col-md-12">
                        <label class="inq-filter-label" for="inqSearch">Search</label>
                        <div class="inq-search-box">
                            <i class="icon-search"></i>
                            <input type="text" id="inqSearch" name="search" class="form-control-zentic w-100 inq-filter"
                                placeholder="Product, SKU, customer, phone..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <label class="inq-filter-label" for="inqStatus">Status</label>
                        <select id="inqStatus" name="status" class="form-control-zentic w-100 inq-filter">
                            <option value="">All Status</option>
                            <option value="0" @selected($activeStatus === '0')>Has Pending</option>
                            <option value="1" @selected($activeStatus === '1')>Has Contacted</option>
                            <option value="2" @selected($activeStatus === '2')>Has Closed</option>
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <label class="inq-filter-label" for="inqDateFrom">From Date</label>
                        <input type="date" id="inqDateFrom" name="date_from" class="form-control-zentic w-100 inq-filter"
                            value="{{ request('date_from') }}">
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <label class="inq-filter-label" for="inqDateTo">To Date</label>
                        <input type="date" id="inqDateTo" name="date_to" class="form-control-zentic w-100 inq-filter"
                            value="{{ request('date_to') }}">
                    </div>
                    <div class="col-xl-1 col-md-4 col-6">
                        <label class="inq-filter-label" for="inqSort">Sort</label>
                        <select id="inqSort" name="sort" class="form-control-zentic w-100 inq-filter">
                            <option value="latest" @selected($activeSort === 'latest')>Latest</option>
                            <option value="most" @selected($activeSort === 'most')>Most</option>
                            <option value="pending" @selected($activeSort === 'pending')>Pending</option>
                        </select>
                    </div>
                    <div class="col-xl-1 col-md-4 col-6">
                        <label class="inq-filter-label" for="inqPerPage">Show</label>
                        <select id="inqPerPage" name="per_page" class="form-control-zentic w-100 inq-filter">
                            @foreach ([12, 24, 48] as $size)
                                <option value="{{ $size }}" @selected((int) request('per_page', 12) === $size)>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-1 col-md-4 col-12">
                        <button type="button" id="inqResetBtn" class="btn btn-light border w-100" title="Reset filters">
                            <i class="icon-rotate-ccw"></i> Reset
                        </button>
                    </div>
                </form>

                <div class="inq-grid-wrap">
                    <div class="inq-loader d-none" id="inqLoader"><div class="inq-spinner"></div></div>
                    <div class="row g-3" id="inqCardsGrid">
                        @include('member.products.partials.inquiries_cards', ['groups' => $groups])
                    </div>
                </div>

                <div id="inqPagination" class="mt-4">
                    @include('member.products.partials.inquiries_pagination', ['inquiries' => $groups])
                </div>
            </div>
        </div>

        {{-- ================= Product Inquiries Modal ================= --}}
        <div class="inq-modal-backdrop" id="inqProductModal" aria-hidden="true">
            <div class="inq-modal" role="dialog" aria-modal="true" aria-labelledby="inqPmTitle">
                <div class="inq-modal-header">
                    <div class="d-flex align-items-center gap-3 inq-min-w-0">
                        <img id="inqPmImg" class="inq-pm-img d-none" alt="">
                        <span id="inqPmImgPh" class="inq-pm-img inq-pm-ph"><i class="icon-image"></i></span>
                        <div class="inq-min-w-0">
                            <h5 class="inq-pm-title" id="inqPmTitle"></h5>
                            <div class="inq-sub">
                                <span id="inqPmSku"></span>
                                <a href="#" id="inqPmLink" target="_blank" rel="noopener" class="d-none ms-2">
                                    View product <i class="icon-external-link"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="inq-modal-close" data-inq-close aria-label="Close">&times;</button>
                </div>

                <div class="inq-tabs">
                    <button type="button" class="inq-tab active" data-tab="all">All <span data-tab-count="total">–</span></button>
                    <button type="button" class="inq-tab" data-tab="0">Pending <span data-tab-count="pending">–</span></button>
                    <button type="button" class="inq-tab" data-tab="1">Contacted <span data-tab-count="contacted">–</span></button>
                    <button type="button" class="inq-tab" data-tab="2">Closed <span data-tab-count="closed">–</span></button>
                </div>

                <div class="inq-modal-body">
                    <div class="inq-loader d-none" id="inqPmLoader"><div class="inq-spinner"></div></div>
                    <div id="inqPmBody"></div>
                    <div class="inq-empty d-none" id="inqPmEmpty">
                        <i class="icon-inbox"></i>
                        <div>No inquiries in this status.</div>
                    </div>
                </div>

                <div class="inq-modal-footer">
                    <button type="button" class="btn btn-light border" data-inq-close>Close</button>
                </div>
            </div>
        </div>
    </main>

    {{-- Script yahin hai taaki layout me @stack('scripts') na ho tab bhi chale --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            jQuery(function($) {
                const LIST_URL = @json(route('member.products.inquiries.list'));
                const STATUS_URL = @json(route('member.products.inquiries.status.update'));
                const CSRF = @json(csrf_token());
                const STATUS = {
                    0: { label: 'Pending', variant: 'warning' },
                    1: { label: 'Contacted', variant: 'success' },
                    2: { label: 'Closed', variant: 'danger' }
                };
                const DEFAULTS = { per_page: '12', sort: 'latest' };

                const $form = $('#inqFilterForm');
                const $grid = $('#inqCardsGrid');
                const $pagination = $('#inqPagination');
                const $loader = $('#inqLoader');

                let activeXhr = null;
                let searchTimer = null;
                let currentPage = {{ (int) $groups->currentPage() }};

                function notify(type, msg) {
                    if (window.toastr) toastr[type](msg);
                    else alert(msg);
                }

                /* ================= Cards list ================= */
                function getFilters() {
                    const params = {};
                    $form.find('.inq-filter').each(function() {
                        const val = $.trim($(this).val() ?? '');
                        if (val !== '' && DEFAULTS[this.name] !== val) params[this.name] = val;
                    });
                    return params;
                }

                function syncUrl(params) {
                    const clean = Object.assign({}, params);
                    if (String(clean.page) === '1') delete clean.page;
                    const qs = $.param(clean);
                    history.replaceState(null, '', LIST_URL + (qs ? '?' + qs : ''));
                }

                function updateCounts(counts) {
                    if (!counts) return;
                    $.each(counts, (key, val) => $('[data-count="' + key + '"]').text(val));
                }

                function highlightActiveStat() {
                    const status = $('#inqStatus').val();
                    $('.inq-stat-card').removeClass('active').filter('[data-status="' + status + '"]').addClass('active');
                }

                function loadInquiries(page = 1) {
                    currentPage = page;
                    if (activeXhr) activeXhr.abort();

                    const params = Object.assign(getFilters(), { page: page });
                    $loader.removeClass('d-none');

                    const req = $.ajax({ url: LIST_URL, type: 'GET', data: params, dataType: 'json', cache: false });
                    activeXhr = req;

                    req.done(function(res) {
                        $grid.html(res.cards);
                        $pagination.html(res.pagination);
                        $('#inqResultBadge').text('Products : ' + res.total);
                        updateCounts(res.counts);
                        highlightActiveStat();
                        syncUrl(params);
                    }).fail(function(xhr, textStatus) {
                        if (textStatus !== 'abort') notify('error', xhr.responseJSON?.message || 'Failed to load inquiries.');
                    }).always(function() {
                        if (activeXhr === req) {
                            $loader.addClass('d-none');
                            activeXhr = null;
                        }
                    });
                }

                $form.on('submit', e => e.preventDefault());

                $('#inqSearch').on('input', function() {
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(() => loadInquiries(1), 400);
                });

                $form.on('change', 'select.inq-filter', () => loadInquiries(1));

                $('#inqDateFrom').on('change', function() {
                    $('#inqDateTo').attr('min', this.value || null);
                    loadInquiries(1);
                });
                $('#inqDateTo').on('change', function() {
                    $('#inqDateFrom').attr('max', this.value || null);
                    loadInquiries(1);
                });

                $('#inqResetBtn').on('click', function() {
                    $form.find('.inq-filter').each(function() {
                        $(this).val(DEFAULTS[this.name] ?? '');
                    });
                    $('#inqDateFrom, #inqDateTo').removeAttr('min max');
                    loadInquiries(1);
                });

                $(document).on('click', '.inq-stat-card', function() {
                    $('#inqStatus').val(String($(this).data('status') ?? '')).trigger('change');
                });

                $(document).on('click', '#inqPagination a.page-link', function(e) {
                    e.preventDefault();
                    const page = new URL(this.href).searchParams.get('page') || 1;
                    loadInquiries(parseInt(page, 10));
                    $('html, body').animate({ scrollTop: $('#inqCardsGrid').offset().top - 140 }, 200);
                });

                /* ================= Product modal ================= */
                const $modal = $('#inqProductModal');
                let modalXhr = null;
                let modalDirty = false;
                let activeTab = 'all';

                function openModal() {
                    $modal.addClass('show').attr('aria-hidden', 'false');
                    $('body').addClass('inq-modal-open');
                }

                function closeModal() {
                    if (!$modal.hasClass('show')) return;
                    $modal.removeClass('show').attr('aria-hidden', 'true');
                    $('body').removeClass('inq-modal-open');
                    if (modalXhr) modalXhr.abort();
                    // Status badla ho to cards fresh karo (filter/sort ke hisab se)
                    if (modalDirty) {
                        modalDirty = false;
                        loadInquiries(currentPage);
                    }
                }

                function setHeader(title, image, sku, url) {
                    $('#inqPmTitle').text(title || 'Product removed').attr('title', title || '');
                    if (image) {
                        $('#inqPmImg').attr('src', image).removeClass('d-none');
                        $('#inqPmImgPh').addClass('d-none');
                    } else {
                        $('#inqPmImg').addClass('d-none').removeAttr('src');
                        $('#inqPmImgPh').removeClass('d-none');
                    }
                    $('#inqPmSku').text(sku ? 'SKU: ' + sku : '');
                    if (url) $('#inqPmLink').attr('href', url).removeClass('d-none');
                    else $('#inqPmLink').addClass('d-none').attr('href', '#');
                }

                function setModalCounts(counts) {
                    if (!counts) return;
                    $.each(counts, (key, val) => $modal.find('[data-tab-count="' + key + '"]').text(val));
                }

                function applyTab() {
                    $modal.find('.inq-tab').removeClass('active').filter('[data-tab="' + activeTab + '"]').addClass('active');
                    let visible = 0;
                    $modal.find('.inq-item').each(function() {
                        const show = activeTab === 'all' || String($(this).attr('data-status')) === activeTab;
                        $(this).toggleClass('d-none', !show);
                        if (show) visible++;
                    });
                    $('#inqPmEmpty').toggleClass('d-none', visible > 0 || !$modal.find('.inq-item').length);
                }

                function updateCardCounts(productId, counts) {
                    if (!counts) return;
                    const $card = $('.inq-product-card[data-product-id="' + productId + '"]');
                    $.each(counts, (key, val) => $card.find('[data-pc="' + key + '"]').text(val));
                    $card.find('[data-pc-label]').text(counts.total === 1 ? 'Inquiry' : 'Inquiries');
                    $card.toggleClass('has-pending', counts.pending > 0);
                }

                function openProductModal($card) {
                    const pageStatus = $('#inqStatus').val();
                    activeTab = pageStatus !== '' ? pageStatus : 'all';

                    setHeader($card.attr('data-title'), $card.attr('data-image'), '', '');
                    $modal.find('[data-tab-count]').text('–');
                    $('#inqPmBody').empty();
                    $('#inqPmEmpty').addClass('d-none');
                    $('#inqPmLoader').removeClass('d-none');
                    applyTab();
                    openModal();

                    if (modalXhr) modalXhr.abort();
                    const req = $.ajax({ url: $card.attr('data-url'), type: 'GET', dataType: 'json', cache: false });
                    modalXhr = req;

                    req.done(function(res) {
                        const p = res.product || {};
                        setHeader(p.title, p.image, p.sku, p.url);
                        setModalCounts(res.counts);
                        $('#inqPmBody').html(res.html);
                        applyTab();
                    }).fail(function(xhr, textStatus) {
                        if (textStatus === 'abort') return;
                        notify('error', xhr.responseJSON?.message || 'Failed to load inquiries.');
                        closeModal();
                    }).always(function() {
                        if (modalXhr === req) {
                            $('#inqPmLoader').addClass('d-none');
                            modalXhr = null;
                        }
                    });
                }

                $(document).on('click', '.inq-product-card', function() {
                    openProductModal($(this));
                });
                $(document).on('keydown', '.inq-product-card', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        openProductModal($(this));
                    }
                });

                $modal.on('click', '.inq-tab', function() {
                    activeTab = String($(this).data('tab'));
                    applyTab();
                });

                $modal.on('click', '.inq-readmore', function() {
                    const open = $(this).prev('.inq-item-msg').toggleClass('expanded').hasClass('expanded');
                    $(this).text(open ? 'Show less' : 'Read more');
                });

                $modal.on('click', '[data-inq-close]', closeModal);
                $modal.on('click', function(e) {
                    if (e.target === this) closeModal();
                });
                $(document).on('keydown', function(e) {
                    if (e.key === 'Escape') closeModal();
                });

                /* ---------- Status update (modal ke andar) ---------- */
                $modal.on('change', '.inq-status-select', function() {
                    const $select = $(this);
                    const oldStatus = String($select.data('current'));
                    const newStatus = String($select.val());

                    $select.prop('disabled', true);

                    $.ajax({
                        url: STATUS_URL,
                        type: 'POST',
                        dataType: 'json',
                        data: { _token: CSRF, id: $select.data('id'), status: newStatus }
                    }).done(function(res) {
                        if (!res.success) {
                            $select.val(oldStatus);
                            notify('error', res.message || 'Failed to update status.');
                            return;
                        }
                        notify('success', res.message);
                        $select.data('current', newStatus)
                            .removeClass('status-warning status-success status-danger')
                            .addClass('status-' + STATUS[newStatus].variant);
                        $select.closest('.inq-item').attr('data-status', newStatus);

                        updateCounts(res.counts);
                        setModalCounts(res.product_counts);
                        updateCardCounts(res.product_id, res.product_counts);
                        modalDirty = true;

                        if (activeTab !== 'all') setTimeout(applyTab, 600);
                    }).fail(function(xhr) {
                        $select.val(oldStatus);
                        notify('error', xhr.responseJSON?.message || 'Something went wrong. Please try again.');
                    }).always(function() {
                        $select.prop('disabled', false);
                    });
                });
            });
        });
    </script>
@endsection
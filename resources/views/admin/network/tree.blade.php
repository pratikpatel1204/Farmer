@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || Tree View')

@section('content')
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Tree View</h2>
                <nav>
                    <ol class="breadcrumb mb-0 flex-wrap" id="treeBreadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="ti ti-smart-home"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">Members</li>
                        <li class="breadcrumb-item" id="crumbTreeView">Tree View</li>
                        {{-- member crumbs JS se yahan add honge --}}
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2 mt-2 mt-md-0">
                @if ($root->sponsor_id && $root->role !== 'Admin')
                    <a href="{{ route('admin.member.tree', $root->sponsor_id) }}" class="btn btn-light tree-nav">
                        <i class="ti ti-arrow-up"></i> Sponsor Tree
                    </a>
                @endif
                <a href="{{ route('admin.member.downline', $root->unique_id) }}" class="btn btn-primary">
                    <i class="ti ti-list"></i> Downline List
                </a>
            </div>
        </div>

        {{-- COUNTS --}}
        <div class="row">
            <div class="col-md-4 col-sm-6">
                <div class="card">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Total Downline</p>
                            <h3 class="mb-0" id="count_total">0</h3>
                        </div>
                        <span class="count-icon bg-primary"><i class="ti ti-users"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="card">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Non Farmer</p>
                            <h3 class="mb-0" id="count_non_farmer">0</h3>
                        </div>
                        <span class="count-icon bg-info"><i class="ti ti-user"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="card">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Farmer</p>
                            <h3 class="mb-0" id="count_farmer">0</h3>
                        </div>
                        <span class="count-icon bg-success"><i class="ti ti-plant-2"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-md-flex justify-content-between align-items-center">
                        <div class="mb-2 mb-md-0">
                            <h4 class="card-title mb-1">Tree of {{ $root->full_name }} ({{ $root->unique_id }})</h4>
                            <div class="tree-legend">
                                <span><i class="legend-dot node-admin"></i> Admin</span>
                                <span><i class="legend-dot node-nonfarmer"></i> Non Farmer</span>
                                <span><i class="legend-dot node-farmer"></i> Farmer</span>
                                <span><i class="legend-dot node-inactive"></i> Inactive</span>
                            </div>
                        </div>
                        <form id="treeSearchForm" class="d-flex gap-2" style="max-width: 320px; width: 100%;">
                            <input type="text" id="treeSearch" class="form-control" placeholder="Enter Member ID...">
                            <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i></button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div id="tree_wrapper">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .count-icon {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .tree-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 12px;
            color: #6c757d;
        }

        .legend-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 2px;
            vertical-align: middle;
        }

        /* ---------- breadcrumb trail ---------- */
        #treeBreadcrumb .tree-crumb a,
        #treeBreadcrumb .tree-crumb span {
            display: inline-block;
            max-width: 160px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            vertical-align: bottom;
        }

        /* ---------- tree layout ---------- */
        .genealogy-tree {
            width: 100%;
            text-align: center;
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 20px;
        }

        .genealogy-tree ul {
            display: flex;
            justify-content: center;
            padding-top: 20px;
            padding-left: 0;
            margin: 0;
            position: relative;
        }

        /* root ul ko center rakhne ke liye */
        .genealogy-tree > ul {
            display: inline-flex;
            min-width: 100%;
        }

        .genealogy-tree li {
            list-style-type: none;
            position: relative;
            text-align: center;
            padding: 20px 6px 0 6px;
            white-space: normal;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* connection lines */
        .genealogy-tree li::before,
        .genealogy-tree li::after {
            content: '';
            position: absolute;
            top: 0;
            right: 50%;
            border-top: 2px solid #ccc;
            width: 50%;
            height: 20px;
        }

        .genealogy-tree li::after {
            right: auto;
            left: 50%;
            border-left: 2px solid #ccc;
        }

        .genealogy-tree li:only-child::before,
        .genealogy-tree li:only-child::after {
            display: none;
        }

        .genealogy-tree li:only-child {
            padding-top: 0;
        }

        .genealogy-tree li:first-child::before,
        .genealogy-tree li:last-child::after {
            border: 0 none;
        }

        .genealogy-tree li:last-child::before {
            border-right: 2px solid #ccc;
            border-radius: 0 6px 0 0;
        }

        .genealogy-tree li:first-child::after {
            border-radius: 6px 0 0 0;
        }

        .genealogy-tree ul ul::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            border-left: 2px solid #ccc;
            height: 20px;
        }

        /* ---------- member card ---------- */
        .member-view-box {
            text-align: center;
            border-radius: 6px;
            border: 1px solid #e4e4e4;
            background: #fff;
            width: 150px;
            min-height: 140px;
            margin: 0 auto;
            display: block;
            flex-shrink: 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .06);
        }

        .member-view-box.has-children {
            cursor: pointer;
        }

        .member-view-box.collapsed {
            border-style: dashed;
            border-color: #999;
        }

        .member-header {
            padding: 5px 0;
            color: #fff;
            font-size: 13px;
            border-radius: 6px 6px 0 0;
        }

        .node-admin { background: #343a40; }
        .node-nonfarmer { background: #0d72ff; }
        .node-farmer { background: #198754; }
        .node-inactive { background: #6e1111; }

        .member-image i {
            font-size: 34px;
            line-height: 34px;
            display: block;
            margin: 6px 4px;
        }

        .member-footer .name {
            color: #000;
            font-size: 14px;
            margin-bottom: 3px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            padding: 0 6px;
        }

        .member-footer .id {
            color: #000;
            font-size: 12px;
            font-weight: bold;
        }

        .member-footer .meta {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            padding: 4px 0 6px;
        }

        .member-footer .view-tree-link {
            font-size: 15px;
        }
    </style>

    <script>
        const ROOT_ID = @json($root->unique_id);
        const ROOT_NAME = @json($root->full_name);
        const TREE_URL = "{{ route('admin.member.tree') }}";

        const TRAIL_KEY = 'member_tree_trail';
        const NAV_FLAG = 'member_tree_nav';

        // ---------- breadcrumb trail ----------
        function getTrail() {
            try {
                return JSON.parse(sessionStorage.getItem(TRAIL_KEY)) || [];
            } catch (e) {
                return [];
            }
        }

        function saveTrail(trail) {
            try {
                sessionStorage.setItem(TRAIL_KEY, JSON.stringify(trail));
            } catch (e) {}
        }

        function markTreeNav() {
            try {
                sessionStorage.setItem(NAV_FLAG, '1');
            } catch (e) {}
        }

        function buildTrail() {
            // tree ke andar se aaye ho to purani trail rakho, warna nayi shuru karo
            let fromTree = false;
            try {
                fromTree = sessionStorage.getItem(NAV_FLAG) === '1';
                sessionStorage.removeItem(NAV_FLAG);
            } catch (e) {}

            let trail = fromTree ? getTrail() : [];
            const idx = trail.findIndex(m => m.id === ROOT_ID);

            if (idx > -1) {
                trail = trail.slice(0, idx + 1); // breadcrumb se peeche gaye -> aage wale hata do
                trail[idx].name = ROOT_NAME;
            } else {
                trail.push({ id: ROOT_ID, name: ROOT_NAME });
            }

            saveTrail(trail);
            return trail;
        }

        function treeLink(id) {
            return TREE_URL + '/' + encodeURIComponent(id);
        }

        function renderBreadcrumb(trail) {
            const $bc = $('#treeBreadcrumb');
            $bc.find('.tree-crumb').remove();

            // "Tree View" -> trail ka pehla member
            const $tv = $('#crumbTreeView').empty();
            if (trail.length > 1) {
                $tv.append($('<a>', {
                    href: treeLink(trail[0].id),
                    class: 'tree-nav',
                    text: 'Tree View'
                }));
            } else {
                $tv.text('Tree View');
            }

            trail.forEach(function(m, i) {
                const isLast = i === trail.length - 1;
                const $li = $('<li>', {
                    class: 'breadcrumb-item tree-crumb' + (isLast ? ' active' : '')
                });

                if (isLast) {
                    $li.append($('<span>', {
                        text: m.name,
                        title: m.name + ' (' + m.id + ')'
                    }));
                } else {
                    $li.append($('<a>', {
                        href: treeLink(m.id),
                        class: 'tree-nav',
                        text: m.name,
                        title: m.name + ' (' + m.id + ')'
                    }));
                }
                $bc.append($li);
            });
        }

        // ---------- tree load ----------
        function loadTree() {
            $.ajax({
                url: "{{ route('admin.member.tree.table') }}",
                type: "GET",
                data: {
                    unique_id: ROOT_ID
                },
                success: function(res) {
                    $('#tree_wrapper').html(res.html);
                    $('#count_total').text(res.counts.total);
                    $('#count_non_farmer').text(res.counts.non_farmer);
                    $('#count_farmer').text(res.counts.farmer);
                },
                error: function() {
                    toastr.error('Unable to load tree');
                }
            });
        }

        $(document).ready(function() {
            renderBreadcrumb(buildTrail());
            loadTree();
        });

        // tree ke andar ka navigation (member link, breadcrumb, sponsor button)
        $(document).on('click', '.view-tree-link, .tree-nav', function() {
            markTreeNav();
        });

        // EXPAND / COLLAPSE
        $(document).on('click', '.member-view-box.has-children', function(e) {
            if ($(e.target).closest('a').length) {
                return; // "view tree" link par click
            }
            $(this).toggleClass('collapsed').next('ul').toggle('fast');
            e.stopPropagation();
        });

        // MEMBER ID SEARCH -> us member ka tree
        $('#treeSearchForm').submit(function(e) {
            e.preventDefault();
            let id = $('#treeSearch').val().trim();
            if (!id) {
                toastr.error('Please enter member ID');
                return;
            }
            markTreeNav();
            window.location.href = treeLink(id);
        });
    </script>
@endsection
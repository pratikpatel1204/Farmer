@extends('member.layout.main-layout')
@section('title', config('app.name') . ' || Tree View')
@section('content')
    <main class="zentic-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Tree View</h1>
                <p class="page-subtitle">Your network. Click a member to expand or collapse.</p>
            </div>
        </div>

        <div class="zentic-card mb-4">
            <div class="zentic-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h2 class="card-title mb-0">
                    Your Network
                    <span class="badge-zentic badge-purple ms-2">{{ $members->count() }} members</span>
                </h2>
                <div class="d-flex align-items-center gap-3 small text-muted">
                    <span><span class="tree-dot" style="background:#7c3aed"></span> You / Leader</span>
                    <span><span class="tree-dot" style="background:#0d72ff"></span> Active</span>
                    <span><span class="tree-dot" style="background:#dc3545"></span> Inactive</span>
                </div>
            </div>
            <div class="zentic-card-body" style="overflow-x: hidden;">
                @if ($tree->isEmpty())
                    <p class="text-muted mb-3">No members in your network yet.</p>
                @endif
                <div class="genealogy-tree">
                    <ul>
                        <li>
                            @include('member.network.tree_node', [
                                'user' => $root,
                                'children' => $tree,
                            ])
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('styles')
    <style>
        .tree-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 4px;
        }

        .genealogy-tree {
            display: block;
            margin: 0 auto;
            width: 100%;
            text-align: center;
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 15px;
        }

        .genealogy-tree ul {
            padding-top: 20px;
            position: relative;
            padding-left: 0;
            display: inline-flex;
            justify-content: flex-start;
            flex-wrap: nowrap;
            width: max-content;
        }

        .genealogy-tree li {
            margin: 0 10px;
            position: relative;
            list-style-type: none;
        }

        .genealogy-tree li::before,
        .genealogy-tree li::after {
            content: '';
            position: absolute;
            top: 0;
            right: 50%;
            border-top: 2px solid var(--zentic-border, #e4e4e4);
            width: 50%;
            height: 15px;
        }

        .genealogy-tree li::after {
            right: auto;
            left: 50%;
            border-left: 2px solid var(--zentic-border, #e4e4e4);
        }

        .genealogy-tree li:only-child::after,
        .genealogy-tree li:only-child::before {
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
            border-right: 2px solid var(--zentic-border, #e4e4e4);
            border-radius: 0 5px 0 0;
        }

        .genealogy-tree li:first-child::after {
            border-radius: 5px 0 0 0;
        }

        .genealogy-tree ul ul::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            border-left: 2px solid var(--zentic-border, #e4e4e4);
            width: 0;
            height: 15px;
        }

        .member-view-box {
            padding-bottom: 8px;
            text-align: center;
            border-radius: 8px;
            position: relative;
            border: 1px solid var(--zentic-border, #e4e4e4);
            background: var(--zentic-card-bg, #fff);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            cursor: pointer;
            width: 160px;
            min-height: 130px;
            margin: 5px;
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }

        .member-view-box:hover {
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .member-header {
            padding: 5px 0;
            text-align: center;
            color: #fff !important;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px 8px 0 0;
        }

        .member-header.leader {
            background: #7c3aed;
        }

        .member-header.active {
            background: #0d72ff;
        }

        .member-header.inactive {
            background: #dc3545;
        }

        .member-image i {
            font-size: 32px;
            line-height: 32px;
            display: block;
            margin: 8px auto 4px;
        }

        .member-footer div.name {
            color: var(--zentic-text, #1a1a1a);
            font-size: 13px;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 0 6px;
        }

        .member-footer div.id {
            color: var(--zentic-text-muted, #6c757d);
            font-size: 11px;
            font-weight: 700;
        }

        .member-footer div.meta {
            color: var(--zentic-text-muted, #6c757d);
            font-size: 11px;
        }

        @media (max-width: 767px) {
            .genealogy-tree li {
                padding: 10px 3px 0 3px;
            }

            .member-view-box {
                max-width: 130px;
            }

            .member-header {
                font-size: 12px;
                padding: 4px 0;
            }

            .member-image i {
                font-size: 26px;
            }

            .member-footer div.name {
                font-size: 12px;
            }

            .member-footer div.id,
            .member-footer div.meta {
                font-size: 10px;
            }
        }
    </style>
@endpush
@push('scripts')
    <script>
        $(document).ready(function() {
            $('.member-view-box[data-toggle="children"]').on('click', function(e) {
                var children = $(this).next('ul');
                if (children.is(':visible')) {
                    children.hide('fast');
                } else {
                    children.show('fast');
                }
                e.stopPropagation();
            });
        });
    </script>
@endpush

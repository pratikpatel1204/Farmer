@extends('member.layout.main-layout')
@section('title', config('app.name') . ' || My Members')
@section('content')
    <main class="zentic-content">
        <div class="zentic-card mb-4">
            <div class="zentic-card-header">
                <h2 class="card-title mb-0">
                    My Members
                    <span class="badge-zentic badge-purple ms-2">{{ $members->count() }}</span>
                </h2>
            </div>
            <div class="zentic-card-body">
                @if ($members->isEmpty())
                    <p class="text-muted mb-3">No members in your network yet.</p>
                @endif
                <div class="zentic-table-wrapper">
                    <table class="zentic-table w-100 datatable" id="mainDataTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Unique ID</th>
                                <th>Role</th>
                                <th>Level</th>
                                <th>Sponsor ID</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Status</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($members as $member)
                                <tr>
                                    <td class="fw-600">{{ $member->full_name }}</td>
                                    <td class="mono">{{ $member->unique_id }}</td>
                                    <td>{{ $member->role }}</td>
                                    <td data-order="{{ $member->level }}">
                                        <span class="badge-zentic badge-purple">{{ $member->level }}</span>
                                    </td>
                                    <td class="mono">{{ $member->sponsor_id }}</td>
                                    {{-- Contact sirf direct members (level 1) ka dikhta hai --}}
                                    <td>{{ $member->level == 1 ? ($member->email ?: '-') : '-' }}</td>
                                    <td>{{ $member->level == 1 ? ($member->mobile ?: '-') : '-' }}</td>
                                    <td>
                                        <span
                                            class="badge-zentic badge-{{ $member->status == 1 ? 'green' : 'red' }} badge-dot">
                                            {{ $member->status == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="mono" data-order="{{ optional($member->created_at)->timestamp }}">
                                        {{ optional($member->created_at)->format('Y-m-d H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
@endsection

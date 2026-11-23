@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || Blogs')
@section('content')
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Blogs</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">Masters</li>
                        <li class="breadcrumb-item active" aria-current="page">Blogs</li>
                    </ol>
                </nav>
            </div>
            <div class="mb-2">
                <a href="{{ route('admin.blog.create') }}" class="btn btn-primary d-flex align-items-center">
                    <i class="ti ti-circle-plus me-2"></i>Add Blog
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center g-3">
                    <div class="col-md-3">
                        <h5 class="mb-0">Blog List</h5>
                    </div>

                    <div class="col-md-9">
                        <form method="GET" action="{{ route('admin.blog.index') }}">
                            <div class="row g-2 justify-content-md-end">
                                <div class="col-12 col-sm-5 col-md-4">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control" placeholder="Search title...">
                                </div>

                                <div class="col-12 col-sm-4 col-md-3">
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>
                                            Published
                                        </option>
                                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>
                                            Draft
                                        </option>
                                    </select>
                                </div>

                                <div class="col-6 col-sm-auto">
                                    <button type="submit" class="btn btn-dark w-100">
                                        <i class="ti ti-search"></i>
                                    </button>
                                </div>

                                @if (request()->hasAny(['search', 'status']))
                                    <div class="col-6 col-sm-auto">
                                        <a href="{{ route('admin.blog.index') }}" class="btn btn-light w-100">
                                            Reset
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Publish Date</th>
                                <th>Views</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($blogs as $blog)
                                <tr id="blog-row-{{ $blog->id }}">
                                    <td>{{ $blogs->firstItem() + $loop->index }}</td>
                                    <td>
                                        @if ($blog->image)
                                            <img src="{{ asset($blog->image) }}" alt="" class="rounded border"
                                                style="width: 64px; height: 44px; object-fit: cover;">
                                        @else
                                            <span class="avatar avatar-md bg-light text-muted rounded"><i
                                                    class="ti ti-photo"></i></span>
                                        @endif
                                    </td>
                                    <td>
                                        <h6 class="mb-0">{{ \Illuminate\Support\Str::limit($blog->title, 60) }}</h6>
                                        <small class="text-muted">/blog/{{ $blog->slug }}</small>
                                        @if ($blog->is_featured)
                                            <span class="badge bg-warning ms-1">Featured</span>
                                        @endif
                                    </td>
                                    <td>{{ $blog->display_date }}</td>
                                    <td>{{ number_format($blog->views) }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input toggle-status" type="checkbox"
                                                data-url="{{ route('admin.blog.toggle-status', $blog) }}"
                                                {{ $blog->status ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            @if ($blog->status)
                                                <a href="{{ $blog->url }}" target="_blank"
                                                    class="btn btn-sm btn-icon btn-light" title="View">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('admin.blog.edit', $blog) }}"
                                                class="btn btn-sm btn-icon btn-light" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-icon btn-light text-danger delete-blog"
                                                data-url="{{ route('admin.blog.destroy', $blog) }}"
                                                data-row="#blog-row-{{ $blog->id }}" title="Delete">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No blogs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($blogs->hasPages())
                <div class="card-footer">
                    {{ $blogs->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    <script>
        $(document).ready(function() {
            const csrf = "{{ csrf_token() }}";

            $(document).on('change', '.toggle-status', function() {
                const $el = $(this);
                $.post($el.data('url'), {
                        _token: csrf
                    })
                    .done(res => toastr.success(res.message))
                    .fail(() => {
                        $el.prop('checked', !$el.prop('checked'));
                        toastr.error('Unable to update the status.');
                    });
            });

            $(document).on('click', '.delete-blog', function() {
                if (!confirm('Are you sure you want to delete this blog?')) return;
                const $btn = $(this);

                $.ajax({
                    url: $btn.data('url'),
                    type: 'POST',
                    data: {
                        _token: csrf,
                        _method: 'DELETE'
                    },
                    success: function(res) {
                        toastr.success(res.message);
                        $($btn.data('row')).fadeOut(200, function() {
                            $(this).remove();
                        });
                    },
                    error: () => toastr.error('Unable to delete the blog.')
                });
            });
        });
    </script>
@endsection

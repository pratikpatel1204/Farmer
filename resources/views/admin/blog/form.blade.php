@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || ' . ($blog->exists ? 'Edit Blog' : 'Add Blog'))
@section('content')
    <style>
        .ck-editor__editable_inline {
            min-height: 320px;
        }

        #imagePreview {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
        }
    </style>

    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">{{ $blog->exists ? 'Edit Blog' : 'Add Blog' }}</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.blog.index') }}">Blogs</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $blog->exists ? 'Edit' : 'Add' }}</li>
                    </ol>
                </nav>
            </div>
            <div class="mb-2">
                <a href="{{ route('admin.blog.index') }}" class="btn btn-light"><i
                        class="ti ti-arrow-left me-1"></i>Back</a>
            </div>
        </div>

        <form id="blogForm" method="POST" enctype="multipart/form-data"
            data-url="{{ $blog->exists ? route('admin.blog.update', $blog) : route('admin.blog.store') }}">
            @csrf
            @if ($blog->exists)
                @method('PUT')
            @endif

            <div class="row">
                {{-- ================= LEFT: CONTENT ================= --}}
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" value="{{ $blog->title }}"
                                    class="form-control" placeholder="Blog title">
                                <span class="text-danger error-title"></span>
                                @if ($blog->exists)
                                    <small class="text-muted d-block mt-1">URL: /blog/{{ $blog->slug }}</small>
                                @else
                                    <small class="text-muted d-block mt-1">The URL slug will be generated
                                        automatically from the title.</small>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Short Description</label>
                                <textarea name="short_description" id="short_description" class="form-control" rows="2" maxlength="500"
                                    placeholder="Short summary shown on the blog list card">{{ $blog->short_description }}</textarea>
                                <div class="d-flex justify-content-between">
                                    <span class="text-danger error-short_description"></span>
                                    <small class="text-muted"><span id="shortCount">0</span>/500</small>
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea name="description" id="description">{!! $blog->description !!}</textarea>
                                <span class="text-danger error-description"></span>
                            </div>
                        </div>
                    </div>

                    {{-- SEO --}}
                    <div class="card">
                        <div class="card-body">
                            <div class="border-bottom mb-3 pb-3">
                                <h5 class="mb-0">SEO</h5>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Meta Title</label>
                                <input type="text" name="meta_title" value="{{ $blog->meta_title }}" class="form-control"
                                    placeholder="If left empty, the blog title will be used">
                                <span class="text-danger error-meta_title"></span>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Meta Description</label>
                                <textarea name="meta_description" class="form-control" rows="2" maxlength="500"
                                    placeholder="If left empty, the short description will be used">{{ $blog->meta_description }}</textarea>
                                <span class="text-danger error-meta_description"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= RIGHT: SETTINGS ================= --}}
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="border-bottom mb-3 pb-3">
                                <h5 class="mb-0">Publish</h5>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select">
                                    <option value="1" {{ $blog->status ? 'selected' : '' }}>Published</option>
                                    <option value="0" {{ !$blog->status ? 'selected' : '' }}>Draft</option>
                                </select>
                                <span class="text-danger error-status"></span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Publish Date</label>
                                <input type="datetime-local" name="published_at" class="form-control"
                                    value="{{ $blog->published_at?->format('Y-m-d\TH:i') }}">
                                <small class="text-muted">If you set a future date, the blog will appear on the website
                                    from that time.</small><br>
                                <span class="text-danger error-published_at"></span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Author Name</label>
                                <input type="text" name="author_name" value="{{ $blog->author_name }}"
                                    class="form-control" placeholder="KisanMandi Team">
                                <span class="text-danger error-author_name"></span>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input type="hidden" name="is_featured" value="0">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1"
                                    id="is_featured" {{ $blog->is_featured ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">Featured blog</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100" id="saveBlogBtn">
                                <span class="btn-text">{{ $blog->exists ? 'Update Blog' : 'Save Blog' }}</span>
                                <span class="spinner-border spinner-border-sm d-none btn-spinner"></span>
                            </button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="border-bottom mb-3 pb-3">
                                <h5 class="mb-0">Cover Image @if (!$blog->exists)
                                        <span class="text-danger">*</span>
                                    @endif
                                </h5>
                            </div>
                            <div class="mb-2 bg-light rounded d-flex align-items-center justify-content-center"
                                style="min-height: 150px;">
                                <img id="imagePreview" src="{{ $blog->image ? asset($blog->image) : '' }}"
                                    class="rounded {{ $blog->image ? '' : 'd-none' }}" alt="">
                                <i class="ti ti-photo fs-1 text-muted {{ $blog->image ? 'd-none' : '' }}"
                                    id="imageIcon"></i>
                            </div>
                            <input type="file" name="image" id="image" class="form-control"
                                accept="image/jpeg,image/png,image/webp">
                            <small class="text-muted">JPG / PNG / WEBP, max 2 MB. Recommended size: 1200x630.</small><br>
                            <span class="text-danger error-image"></span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        $(document).ready(function() {
            const $form = $('#blogForm');
            let editor = null;

            // -------- Rich text editor --------
            ClassicEditor.create(document.querySelector('#description'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList',
                    'blockQuote', 'insertTable', '|', 'undo', 'redo'
                ]
            }).then(e => editor = e).catch(err => console.error(err));

            // -------- Short description counter --------
            const updateCount = () => $('#shortCount').text($('#short_description').val().length);
            $('#short_description').on('input', updateCount);
            updateCount();

            // -------- Image preview --------
            $('#image').on('change', function() {
                const file = this.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = e => {
                    $('#imageIcon').addClass('d-none');
                    $('#imagePreview').attr('src', e.target.result).removeClass('d-none');
                };
                reader.readAsDataURL(file);
            });

            // -------- Submit --------
            $form.on('submit', function(e) {
                e.preventDefault();
                $form.find(".text-danger[class*='error-']").text('');

                if (editor) $('#description').val(editor.getData());
                const formData = new FormData(this);

                const $btn = $('#saveBlogBtn');
                $btn.find('.btn-text').addClass('d-none');
                $btn.find('.btn-spinner').removeClass('d-none');
                $btn.prop('disabled', true);

                $.ajax({
                    url: $form.data('url'),
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        if (res.status === true) {
                            toastr.success(res.message);
                            setTimeout(() => window.location.href = res.redirect, 700);
                        } else {
                            toastr.error(res.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $form.find('.error-' + key).text(value[0]);
                            });
                            toastr.error('Please fix the errors in the form.');
                        } else {
                            toastr.error(xhr.responseJSON?.message || 'Something went wrong!');
                        }
                    },
                    complete: function() {
                        $btn.find('.btn-text').removeClass('d-none');
                        $btn.find('.btn-spinner').addClass('d-none');
                        $btn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection

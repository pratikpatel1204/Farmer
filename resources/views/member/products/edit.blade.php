@extends('member.layout.main-layout')
@section('title', config('app.name') . ' || Edit Product')
@section('content')
    @php
        // Model accessor already returns an array, so no json_decode here
        $subImages = $product->sub_images ?? [];
        $selectedType = old('listing_type', $product->listing_type ?? 'have');
    @endphp

    <style>
        .listing-type-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .listing-type-option {
            flex: 1;
            min-width: 180px;
            margin: 0;
            cursor: pointer;
            position: relative;
        }

        .listing-type-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .listing-type-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border: 2px solid var(--border-color, #dfe3e8);
            border-radius: 12px;
            transition: all .2s ease;
            background: transparent;
        }

        .listing-type-card .lt-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(128, 128, 128, .12);
            font-size: 18px;
        }

        .listing-type-card strong {
            display: block;
            line-height: 1.2;
        }

        .listing-type-card small {
            opacity: .7;
        }

        .listing-type-option:hover .listing-type-card {
            border-color: #22c55e;
        }

        .listing-type-option input:checked+.listing-type-card {
            border-color: #22c55e;
            background: rgba(34, 197, 94, .08);
            box-shadow: 0 0 0 3px rgba(34, 197, 94, .15);
        }

        .listing-type-option input:checked+.listing-type-card .lt-icon {
            background: #22c55e;
            color: #fff;
        }

        .listing-type-option input:focus-visible+.listing-type-card {
            outline: 2px solid #22c55e;
        }
    </style>

    <main class="zentic-content">
        <div class="zentic-card mb-4">
            <div class="zentic-card-header">
                <h1 class="card-title mb-0">Edit Product</h1>
                <a href="{{ route('member.products') }}" class="btn-zentic btn-primary">
                    <i class="icon-arrow-left me-1"></i> Back
                </a>
            </div>
            <div class="zentic-card-body">
                <form id="productForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="product_id" value="{{ $product->id }}">
                    <div class="row">

                        <!-- Listing Type (I Have / I Want) -->
                        <div class="col-md-12 form-group mb-3">
                            <label class="form-label fw-600">Listing Type <span class="text-danger">*</span></label>
                            <div class="listing-type-group">
                                <label class="listing-type-option">
                                    <input type="radio" name="listing_type" value="have"
                                        {{ $selectedType === 'have' ? 'checked' : '' }}>
                                    <span class="listing-type-card">
                                        <span class="lt-icon"><i class="icon-package"></i></span>
                                        <span>
                                            <strong>I Have</strong>
                                            <small>I have this product available to sell</small>
                                        </span>
                                    </span>
                                </label>
                                <label class="listing-type-option">
                                    <input type="radio" name="listing_type" value="want"
                                        {{ $selectedType === 'want' ? 'checked' : '' }}>
                                    <span class="listing-type-card">
                                        <span class="lt-icon"><i class="icon-search"></i></span>
                                        <span>
                                            <strong>I Want</strong>
                                            <small>I am looking to buy this product</small>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <div id="listingTypeError" class="error text-danger-c text-xs mt-1"></div>
                        </div>

                        <!-- Title -->
                        <div class="col-md-6 form-group mb-3">
                            <label class="form-label fw-600">Product Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control-zentic" id="title" name="title"
                                placeholder="Enter Product Title" value="{{ old('title', $product->title) }}">
                            <div id="titleError" class="error text-danger-c text-xs mt-1"></div>
                        </div>

                        <!-- Category -->
                        <div class="col-md-6 form-group mb-3">
                            <label class="form-label fw-600">Category <span class="text-danger">*</span></label>
                            <select class="form-select-zentic select2" id="category" name="category"
                                data-placeholder="Select Category">
                                <option value="">Select Category</option>
                                @foreach ($categories ?? [] as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ (string) $product->category === (string) $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="categoryError" class="error text-danger-c text-xs mt-1"></div>
                        </div>

                        <!-- Product Tags -->
                        <div class="col-md-12 form-group mb-3">
                            <label class="form-label fw-600">Product Tags</label>
                            <div class="product-tag-container border p-2 rounded d-flex align-items-center flex-wrap gap-2">
                                <input type="text" class="form-control-zentic d-inline-block w-auto"
                                    id="product_tags_input" placeholder="Enter Tag">
                                <input type="hidden" name="product_tags" id="product_tags"
                                    value="{{ old('product_tags', $product->product_tags) }}">
                            </div>
                            <div id="productTagsError" class="error text-danger-c text-xs mt-1"></div>
                        </div>

                        <!-- Main Image Upload -->
                        <div class="col-md-4 form-group mb-3">
                            <label class="form-label fw-600">Main Image</label>
                            <div class="zentic-card mb-0">
                                <div class="zentic-card-body p-0">
                                    <div class="file-upload-zone" data-file-list="mainImageList">
                                        <input type="file" id="main_image" name="main_image" accept="image/*">
                                        <div class="file-upload-icon"><i class="icon-cloud-upload"></i></div>
                                        <div class="file-upload-title">Drop new image here or click to browse</div>
                                        <div class="file-upload-desc">Supports <span>JPG, PNG, WEBP</span><br>Leave
                                            empty to keep current image</div>
                                    </div>
                                    <div class="file-list" id="mainImageList"></div>
                                </div>
                                @if (!empty($product->main_image))
                                    <div class="current-file-preview mb-2 d-flex flex-wrap gap-2 p-2">
                                        <img src="{{ asset($product->main_image) }}" alt="Main Image"
                                            class="avatar-gen-img avatar-80 zentic-border-radius-xl">
                                    </div>
                                @endif
                            </div>
                            <div id="mainImageError" class="error text-danger-c text-xs mt-1"></div>
                        </div>

                        <!-- Sub Images Upload -->
                        <div class="col-md-4 form-group mb-3">
                            <label class="form-label fw-600">Sub Images</label>
                            <div class="zentic-card mb-0">
                                <div class="zentic-card-body p-0">
                                    <div class="file-upload-zone" data-file-list="subImagesList">
                                        <input type="file" id="sub_images" name="sub_images[]" accept="image/*"
                                            multiple data-max="4">
                                        <div class="file-upload-icon"><i class="icon-cloud-upload"></i></div>
                                        <div class="file-upload-title">Drop images here or click to browse</div>
                                        <div class="file-upload-desc">Supports <span>JPG, PNG, WEBP</span><br>Up to 4
                                            files · New files replace all current sub images</div>
                                    </div>
                                    <div class="file-list" id="subImagesList"></div>
                                </div>
                                @if (!empty($subImages))
                                    <div class="current-file-preview mb-2 d-flex flex-wrap gap-2 p-2">
                                        @foreach ($subImages as $img)
                                            <img src="{{ asset($img) }}" alt="Sub Image"
                                                class="avatar-gen-img avatar-80 zentic-border-radius-xl">
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div id="subImagesError" class="error text-danger-c text-xs mt-1"></div>
                        </div>

                        <!-- Brochure Upload -->
                        <div class="col-md-4 form-group mb-3">
                            <label class="form-label fw-600">Brochure (PDF)</label>
                            <div class="zentic-card mb-0">
                                <div class="zentic-card-body p-0">
                                    <div class="file-upload-zone" data-file-list="brochureList">
                                        <input type="file" id="brochure" name="brochure" accept="application/pdf">
                                        <div class="file-upload-icon"><i class="icon-cloud-upload"></i></div>
                                        <div class="file-upload-title">Drop brochure here or click to browse</div>
                                        <div class="file-upload-desc">Supports <span>PDF</span><br>Leave empty to
                                            keep current brochure</div>
                                    </div>
                                    <div class="file-list" id="brochureList"></div>
                                </div>
                                @if (!empty($product->brochure))
                                    <div class="current-file-preview mb-2 p-2">
                                        <a href="{{ asset($product->brochure) }}" target="_blank"
                                            class="btn-zentic btn-primary btn-sm">
                                            <i class="icon-file me-1"></i> View current brochure
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div id="brochureError" class="error text-danger-c text-xs mt-1"></div>
                        </div>

                        <!-- Description -->
                        <div class="col-12 form-group mb-3">
                            <label class="form-label fw-600">Description</label>
                            <textarea class="form-control-zentic" id="description" name="description" rows="5"
                                placeholder="Enter Product Description">{{ old('description', $product->description) }}</textarea>
                            <div id="descriptionError" class="error text-danger-c text-xs mt-1"></div>
                        </div>

                        <!-- Specifications -->
                        <div class="col-12 form-group mb-3">
                            <label class="form-label fw-600">Specifications</label>
                            <div id="specificationContainer">
                                @if (isset($existingSpecs) && $existingSpecs->count() > 0)
                                    @foreach ($existingSpecs as $item)
                                        <div class="row g-2 align-items-end mb-2 spec-row mt-3">
                                            <div class="col-md-5">
                                                <label class="form-label">Label</label>
                                                <input type="text" name="spec_label[]" class="form-control-zentic"
                                                    value="{{ $item->label }}">
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label">Value</label>
                                                <input type="text" name="spec_value[]" class="form-control-zentic"
                                                    value="{{ $item->value }}">
                                            </div>
                                            <div class="col-md-2 d-flex justify-content-end gap-2">
                                                @if ($loop->last)
                                                    <button type="button"
                                                        class="btn-zentic btn-success btn-icon addSpecBtn">
                                                        <i class="icon-plus"></i>
                                                    </button>
                                                @endif
                                                <button type="button"
                                                    class="btn-zentic btn-danger btn-icon removeSpecBtn">
                                                    <i class="icon-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="row g-2 align-items-end mb-2 spec-row mt-3">
                                        <div class="col-md-5">
                                            <label class="form-label">Label</label>
                                            <input type="text" name="spec_label[]" class="form-control-zentic"
                                                placeholder="e.g. Color">
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label">Value</label>
                                            <input type="text" name="spec_value[]" class="form-control-zentic"
                                                placeholder="e.g. Red">
                                        </div>
                                        <div class="col-md-2 d-flex justify-content-end">
                                            <button type="button" class="btn-zentic btn-success btn-icon addSpecBtn">
                                                <i class="icon-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div id="specError" class="error text-danger-c text-xs mt-1"></div>
                        </div>
                    </div>
                    <div class="form-group text-end mt-3">
                        <a href="{{ route('member.products') }}" class="btn-zentic btn-outline">Cancel</a>
                        <button type="submit" class="btn-zentic btn-primary">
                            <i class="icon-check me-1"></i> Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        $(function() {

            // ================= Select2 + CKEditor =================
            if ($.fn.select2) {
                $('.select2').each(function() {
                    $(this).select2({
                        placeholder: $(this).data('placeholder') || 'Select',
                        width: '100%'
                    });
                });
            }
            if (typeof CKEDITOR !== 'undefined') {
                CKEDITOR.replace('description');
            }

            // ================= Drag & Drop Upload Zones =================
            document.querySelectorAll('.file-upload-zone').forEach(function(zone) {
                const input = zone.querySelector('input[type="file"]');
                const listEl = document.getElementById(zone.getAttribute('data-file-list'));
                if (!input || !listEl) return;

                const maxFiles = parseInt(input.dataset.max || '0', 10);

                function renderFiles(files) {
                    listEl.innerHTML = '';
                    Array.from(files).forEach(function(file, index) {
                        const item = document.createElement('div');
                        item.className =
                            'file-list-item d-flex align-items-center justify-content-between py-1';

                        const label = document.createElement('span');
                        label.className = 'text-sm text-truncate flex-grow-1 me-2';
                        const icon = document.createElement('i');
                        icon.className = 'icon-file';
                        label.appendChild(icon);
                        label.appendChild(document.createTextNode(' ' + file.name));

                        const remove = document.createElement('i');
                        remove.className = 'icon-x remove-file';
                        remove.style.cursor = 'pointer';
                        remove.setAttribute('data-index', index);

                        item.appendChild(label);
                        item.appendChild(remove);
                        listEl.appendChild(item);
                    });
                }

                function withinLimit(files) {
                    if (maxFiles && files.length > maxFiles) {
                        toastr.warning('You can upload a maximum of ' + maxFiles + ' files.');
                        return false;
                    }
                    return true;
                }

                input.addEventListener('change', function() {
                    if (!withinLimit(input.files)) input.value = '';
                    renderFiles(input.files);
                });

                ['dragenter', 'dragover'].forEach(function(evt) {
                    zone.addEventListener(evt, function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        zone.classList.add('dragover');
                    });
                });

                ['dragleave', 'drop'].forEach(function(evt) {
                    zone.addEventListener(evt, function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        zone.classList.remove('dragover');
                    });
                });

                zone.addEventListener('drop', function(e) {
                    const dt = e.dataTransfer;
                    if (!dt || !dt.files || !dt.files.length) return;

                    if (input.multiple) {
                        if (!withinLimit(dt.files)) return;
                        input.files = dt.files;
                    } else {
                        const single = new DataTransfer();
                        single.items.add(dt.files[0]);
                        input.files = single.files;
                    }
                    renderFiles(input.files);
                });

                listEl.addEventListener('click', function(e) {
                    if (!e.target.classList.contains('remove-file')) return;
                    const index = parseInt(e.target.getAttribute('data-index'), 10);

                    if (input.multiple) {
                        const dt = new DataTransfer();
                        Array.from(input.files).forEach(function(file, i) {
                            if (i !== index) dt.items.add(file);
                        });
                        input.files = dt.files;
                    } else {
                        input.value = '';
                    }
                    renderFiles(input.files);
                });
            });

            // ================= Product Tags =================
            (function() {
                const input = document.getElementById('product_tags_input');
                const hiddenInput = document.getElementById('product_tags');
                if (!input || !hiddenInput) return;

                const container = input.parentElement;
                let tags = hiddenInput.value ? hiddenInput.value.split(',').map(t => t.trim()).filter(t => t) :
                    [];

                function updateTags() {
                    container.querySelectorAll('.tag-item').forEach(el => el.remove());

                    tags.forEach(function(tag) {
                        const tagEl = document.createElement('span');
                        tagEl.className = 'badge-zentic badge-dark tag-item gap-1';

                        const text = document.createElement('span');
                        text.textContent = tag;

                        const remove = document.createElement('i');
                        remove.className = 'icon-x remove-tag';
                        remove.style.cursor = 'pointer';
                        remove.dataset.tag = tag;

                        tagEl.appendChild(text);
                        tagEl.appendChild(remove);
                        container.insertBefore(tagEl, input);
                    });

                    hiddenInput.value = tags.join(',');
                }

                function addTagsFromText(text) {
                    const newTags = text.split(/[\n,]+/).map(t => t.trim()).filter(t => t && !tags.includes(t));
                    if (newTags.length) {
                        tags = tags.concat(newTags);
                        updateTags();
                    }
                }

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ',') {
                        e.preventDefault();
                        if (input.value.trim()) {
                            addTagsFromText(input.value);
                            input.value = '';
                        }
                    }
                });
                input.addEventListener('blur', function() {
                    if (input.value.trim()) {
                        addTagsFromText(input.value);
                        input.value = '';
                    }
                });
                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    addTagsFromText((e.clipboardData || window.clipboardData).getData('text'));
                    input.value = '';
                });
                container.addEventListener('click', function(e) {
                    if (e.target.classList.contains('remove-tag')) {
                        tags = tags.filter(tag => tag !== e.target.dataset.tag);
                        updateTags();
                        input.focus();
                    }
                });

                updateTags();
            })();

            // ================= Specifications =================
            $('#specificationContainer').on('click', '.addSpecBtn', function() {
                $('#specificationContainer').append(`
                    <div class="row g-2 align-items-end mb-2 spec-row">
                        <div class="col-md-5">
                            <label class="form-label">Label</label>
                            <input type="text" name="spec_label[]" class="form-control-zentic" placeholder="e.g. Material">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Value</label>
                            <input type="text" name="spec_value[]" class="form-control-zentic" placeholder="e.g. Cotton">
                        </div>
                        <div class="col-md-2 d-flex justify-content-end gap-2">
                            <button type="button" class="btn-zentic btn-success btn-icon addSpecBtn"><i class="icon-plus"></i></button>
                            <button type="button" class="btn-zentic btn-danger btn-icon removeSpecBtn"><i class="icon-trash"></i></button>
                        </div>
                    </div>
                `);
            });

            $('#specificationContainer').on('click', '.removeSpecBtn', function() {
                const $rows = $('#specificationContainer .spec-row');
                if ($rows.length > 1) {
                    $(this).closest('.spec-row').remove();
                } else {
                    // last row: just clear values instead of removing
                    $rows.find('input').val('');
                }
            });

            // ================= Form Submit =================
            const errorMap = {
                listing_type: 'listingTypeError',
                title: 'titleError',
                category: 'categoryError',
                product_tags: 'productTagsError',
                main_image: 'mainImageError',
                sub_images: 'subImagesError',
                brochure: 'brochureError',
                description: 'descriptionError',
                spec_label: 'specError',
                spec_value: 'specError'
            };

            $('#productForm').on('submit', function(e) {
                e.preventDefault();

                const $btn = $(this).find('button[type="submit"]');
                const originalHtml = $btn.html();

                $('.error').text('');

                const formData = new FormData(this);
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.description) {
                    formData.set('description', CKEDITOR.instances.description.getData());
                }

                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Updating...'
                    );

                $.ajax({
                    url: "{{ route('member.product.update') }}",
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.status === true) {
                            toastr.success(response.message || 'Product updated successfully.');
                            setTimeout(function() {
                                window.location.href =
                                "{{ route('member.products') }}";
                            }, 600);
                        } else {
                            toastr.error(response.message || 'Something went wrong.');
                            $btn.prop('disabled', false).html(originalHtml);
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(originalHtml);

                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(field, messages) {
                                const id = errorMap[field.split('.')[0]];
                                if (id && $('#' + id).length) {
                                    $('#' + id).text(messages[0]);
                                } else {
                                    toastr.error(messages[0]);
                                }
                            });
                            toastr.error('Please fix the highlighted fields.');
                        } else {
                            toastr.error((xhr.responseJSON && xhr.responseJSON.message) ||
                                'Something went wrong. Please try again.');
                        }
                    }
                });
            });
        });
    </script>
@endpush

@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || Profile')
@section('content')
    @php $authUser = Auth::user(); @endphp
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Profile</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">Pages</li>
                        <li class="breadcrumb-item active" aria-current="page">Profile</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- ================= PERSONAL DETAILS FORM ================= -->
        <div class="card">
            <div class="card-body">
                <div class="border-bottom mb-3 pb-3">
                    <h4>Profile</h4>
                </div>

                <form id="personalDetailsForm" enctype="multipart/form-data">
                    @csrf

                    {{-- Basic Information --}}
                    <div class="border-bottom mb-3 pb-3">
                        <h6 class="mb-3">Basic Information</h6>

                        <div class="row mb-3">
                            <div class="col-md-12 d-flex align-items-center bg-light rounded p-3">
                                <div
                                    class="avatar avatar-xxl rounded-circle border border-dashed me-3 text-dark overflow-hidden">
                                    @if ($authUser?->profile_image)
                                        <img src="{{ asset($authUser->profile_image) }}" id="profilePreview"
                                            class="rounded-circle w-100 h-100" style="object-fit: cover;">
                                    @else
                                        <i class="ti ti-user fs-16" id="profileIcon"></i>
                                        <img src="" id="profilePreview" class="rounded-circle w-100 h-100 d-none"
                                            style="object-fit: cover;">
                                    @endif
                                </div>
                                <div>
                                    <label class="form-label">Profile Photo</label>
                                    <input type="file" name="profile_image" id="profile_image" class="form-control"
                                        accept="image/jpeg,image/png,image/webp">
                                    <small class="text-muted">Recommended size: 150x150 (max 2MB)</small><br>
                                    <span class="text-danger error-profile_image"></span>
                                    @if ($authUser?->role)
                                        <span class="badge bg-primary text-uppercase">{{ $authUser->role }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Read-only info --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Unique ID</label>
                                <input type="text" value="{{ $authUser?->unique_id }}" class="form-control" readonly>
                            </div>
                            @if ($authUser?->sponsor_id)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Sponsor ID</label>
                                    <input type="text" value="{{ $authUser->sponsor_id }}" class="form-control" readonly>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Sponsor Name</label>
                                    <input type="text" value="{{ $authUser->sponsor_name }}" class="form-control"
                                        readonly>
                                </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" value="{{ $authUser?->full_name }}"
                                    class="form-control" required placeholder="Enter your name">
                                <span class="text-danger error-full_name"></span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" value="{{ $authUser?->email }}" class="form-control"
                                    required placeholder="Enter email">
                                <span class="text-danger error-email"></span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                <input type="text" name="mobile" value="{{ $authUser?->mobile }}" class="form-control"
                                    required maxlength="10" placeholder="Enter mobile number">
                                <span class="text-danger error-mobile"></span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">State</label>
                                <select class="form-select select2" id="state" name="state">
                                    <option value="">Select State</option>
                                    @foreach ($states as $st)
                                        <option value="{{ $st->name }}" data-id="{{ $st->id }}"
                                            {{ $authUser?->state == $st->name ? 'selected' : '' }}>
                                            {{ $st->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-state"></span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">City</label>
                                <select class="form-select select2" id="city" name="city"
                                    data-selected="{{ $authUser?->city }}">
                                    <option value="">Select City</option>
                                </select>
                                <span class="text-danger error-city"></span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Area</label>
                                <input type="text" name="area" value="{{ $authUser?->area }}"
                                    class="form-control" placeholder="Enter area">
                                <span class="text-danger error-area"></span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Pincode</label>
                                <input type="text" name="pincode" value="{{ $authUser?->pincode }}"
                                    class="form-control" maxlength="6" placeholder="Enter pincode">
                                <span class="text-danger error-pincode"></span>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="3" placeholder="Enter address">{{ $authUser?->address }}</textarea>
                                <span class="text-danger error-address"></span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" id="saveProfileBtn">
                            <span class="btn-text">Save</span>
                            <span class="spinner-border spinner-border-sm d-none btn-spinner"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= CHANGE PASSWORD FORM ================= -->
        <div class="card">
            <div class="card-body">
                <div class="border-bottom mb-3 pb-3">
                    <h4>Change Password</h4>
                </div>

                <form id="changePasswordForm">
                    @csrf
                    <input type="hidden" name="form_type" value="password">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Current Password <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control" required>
                            <span class="text-danger error-current_password"></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">New Password <span class="text-danger">*</span></label>
                            <input type="password" name="new_password" class="form-control" required>
                            <span class="text-danger error-new_password"></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="new_password_confirmation" class="form-control" required>
                            <span class="text-danger error-new_password_confirmation"></span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" id="savePasswordBtn">
                            <span class="btn-text">Update Password</span>
                            <span class="spinner-border spinner-border-sm d-none btn-spinner"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });

            // -------- Image preview --------
            $('#profile_image').on('change', function() {
                const file = this.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = e => {
                    $('#profileIcon').addClass('d-none');
                    $('#profilePreview').attr('src', e.target.result).removeClass('d-none');
                };
                reader.readAsDataURL(file);
            });

            // -------- State / City dependent dropdown --------
            function loadCities(stateId, selectedCity = null) {
                const $city = $('#city');
                $city.html('<option value="">Select City</option>');
                if (!stateId) {
                    $city.trigger('change');
                    return;
                }

                $.ajax({
                    url: `/get-cities/${stateId}`,
                    type: 'GET',
                    success: function(data) {
                        const cities = Array.isArray(data) ? data : (data.cities || []);
                        cities.forEach(city => {
                            const selected = selectedCity && selectedCity == city.name ?
                                'selected' : '';
                            $city.append(
                                `<option value="${city.name}" ${selected}>${city.name}</option>`
                                );
                        });
                        $city.trigger('change');
                    },
                    error: function() {
                        console.error("Failed to load cities");
                    }
                });
            }

            $('#state').on('change', function() {
                loadCities($(this).find(':selected').data('id'));
            });

            const initialStateId = $('#state').find(':selected').data('id');
            if (initialStateId) {
                loadCities(initialStateId, $('#city').data('selected'));
            }

            // -------- Common submit handler --------
            function submitForm($form, $btn, onSuccess) {
                $form.find(".text-danger[class*='error-']").text('');
                const formData = new FormData($form[0]);

                $btn.find('.btn-text').addClass('d-none');
                $btn.find('.btn-spinner').removeClass('d-none');
                $btn.prop('disabled', true);

                $.ajax({
                    url: "{{ route('admin.profile.update') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.status === true) {
                            toastr.success(response.message);
                            if (onSuccess) onSuccess(response);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $form.find('.error-' + key).text(value[0]);
                            });
                        } else {
                            toastr.error(xhr.responseJSON?.message || "Something went wrong!");
                        }
                    },
                    complete: function() {
                        $btn.find('.btn-text').removeClass('d-none');
                        $btn.find('.btn-spinner').addClass('d-none');
                        $btn.prop('disabled', false);
                    }
                });
            }

            $("#personalDetailsForm").on("submit", function(e) {
                e.preventDefault();
                submitForm($(this), $('#saveProfileBtn'));
            });

            $("#changePasswordForm").on("submit", function(e) {
                e.preventDefault();
                submitForm($(this), $('#savePasswordBtn'), () => $("#changePasswordForm")[0].reset());
            });
        });
    </script>
@endsection
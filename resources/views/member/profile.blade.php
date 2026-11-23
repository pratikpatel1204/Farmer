@extends('member.layout.main-layout')
@section('title', config('app.name') . ' || My Profile')
@section('content')
    @php
        $isMobileVerified = !empty($user->mobile_verified_at) || (int) $user->mobile_verified === 1;
        $existingImages = json_decode(optional($profile)->company_images ?? '[]', true) ?? [];
    @endphp
    <main class="zentic-content">
        <div class="page-header align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div>
                    <h1 class="page-title">My Profile</h1>
                    <p class="page-subtitle">Manage your account and verification details</p>
                </div>
            </div>
            @if ($profile)
                @if ($profile->status === 'Approved')
                    <span class="badge-zentic badge-green badge-dot fs-6">Approved</span>
                @elseif($profile->status === 'Pending')
                    <span class="badge-zentic badge-orange badge-dot fs-6">Pending</span>
                @elseif($profile->status === 'Rejected')
                    <span class="badge-zentic badge-red badge-dot fs-6">Rejected</span>
                    @if ($profile->reject_reason)
                        <div class="alert-zentic alert-danger"><i class="icon-circle-x alert-icon"></i>
                            <div class="alert-content">
                                <div class="alert-title">Reason!</div>
                                <div>{{ $profile->reject_reason }}</div>
                            </div>
                        </div>
                    @endif
                @endif
            @else
                <span class="badge-zentic badge-orange badge-dot fs-6">Not Verified</span>
            @endif
        </div>

        <div class="settings-tabs mb-4">
            <button class="settings-tab active" data-tab="tab-profile">Profile</button>
            <button class="settings-tab" data-tab="tab-about">About</button>
            <button class="settings-tab" data-tab="tab-kyc">KYC Verification</button>
            <button class="settings-tab" data-tab="tab-media">Photos & Video</button>
        </div>

        <!-- Profile Tab -->
        <div class="settings-panel active" id="tab-profile">
            <div class="row g-4">
                <div class="col-xl-3 col-lg-4">
                    <div class="zentic-card text-center">
                        <div class="zentic-card-body settings-content">
                            <div class="position-relative edit-user-avatar-wrap">
                                @if (!empty($user->profile_image))
                                    <img class="avatar-gen-img avatar-72 rounded-circle"
                                        src="{{ asset($user->profile_image) }}" data-size="72"
                                        alt="{{ $user->full_name }}" />
                                @else
                                    <img class="avatar-gen-img avatar-72 rounded-circle"
                                        src="{{ asset('member/img/avatar/avatar.jpg') }}" data-size="72"
                                        alt="{{ $user->full_name }}" />
                                @endif
                            </div>
                            <h2 class="fw-700 mb-2 h6" id="display-fullname">{{ $user->full_name }}</h2>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge-zentic badge-purple">
                                    <i class="icon-key"></i> {{ $user->unique_id ?? '' }}
                                </span>
                                @if ($user->status == 1)
                                    <span class="badge-zentic badge-green badge-dot">Active</span>
                                @else
                                    <span class="badge-zentic badge-red badge-dot">Inactive</span>
                                @endif
                            </div>
                            <hr>
                            <div class="d-flex flex-column gap-2 text-sm">
                                <div class="d-flex align-items-center gap-2">
                                    @if ($isMobileVerified)
                                        <i class="icon-circle-check text-success-c text-2xs"></i>
                                    @else
                                        <i class="icon-clock text-warning-c text-2xs" id="MobileverifyOtpButton"></i>
                                    @endif
                                    <span>{{ $user->mobile }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="icon-mail text-2xs"></i>
                                    <span class="text-dark">{{ $user->email ?? '-' }}</span>
                                </div>
                            </div>
                            @if (empty($profile))
                                <div class="zentic-card mt-2">
                                    <div class="zentic-card-body zentic-card-body-sm">
                                        <p class="text-danger-c fw-600 mb-0"><i class="icon-triangle-alert"></i> Please
                                            complete your profile below.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8">
                    <div class="zentic-card">
                        <div class="zentic-card-header">
                            <h2 class="card-title mb-0">Personal Information</h2>
                        </div>
                        <div class="zentic-card-body">
                            <form id="info">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="full_name" class="form-label">Full Name<span
                                                class="required">*</span></label>
                                        <input type="text" class="form-control-zentic" id="full_name" name="full_name"
                                            value="{{ $user->full_name }}">
                                        <div class="error text-danger-c text-xs mt-1" style="display:none;">Please
                                            enter your full name.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="mobile" class="form-label">Mobile No<span
                                                class="required">*</span></label>
                                        <input type="tel" inputmode="numeric" maxlength="10" class="form-control-zentic"
                                            id="mobile" name="mobile" value="{{ $user->mobile }}"
                                            {{ !empty($user->mobile) ? 'disabled' : '' }}>
                                        <div class="error text-danger-c text-xs mt-1" style="display:none;">Please
                                            enter a valid mobile number.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control-zentic" id="email" name="email"
                                            value="{{ $user->email }}" {{ !empty($user->email) ? 'disabled' : '' }}>
                                        <div class="error text-danger-c text-xs mt-1" style="display:none;">Please
                                            enter a valid email.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="password" class="form-label">New Password</label>
                                        <input type="password" class="form-control-zentic" id="password"
                                            name="password">
                                        <div class="error text-danger-c text-xs mt-1" style="display:none;">Password
                                            must be at least 6 characters long.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control-zentic" id="confirmPassword"
                                            name="password_confirmation">
                                        <div class="error text-danger-c text-xs mt-1" style="display:none;">Passwords
                                            do not match.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="profile_image" class="form-label">Profile Image</label>
                                        <div class="file-upload-zone" data-file-list="profileImageFileList">
                                            <input type="file" id="profile_image" name="profile_image"
                                                accept="image/*">
                                            <div class="file-upload-icon"><i class="icon-cloud-upload"></i></div>
                                            <div class="file-upload-title">Drop image here or click to browse</div>
                                            <div class="file-upload-desc">JPEG or PNG</div>
                                        </div>
                                        <div class="file-list" id="profileImageFileList"></div>
                                        <div class="error text-danger-c text-xs mt-1" style="display:none;">Please
                                            select a valid image.</div>
                                        @if (!empty($user->profile_image))
                                            <img src="{{ asset($user->profile_image) }}" class="img-thumbnail mt-2"
                                                width="100">
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="zentic-card-footer text-end">
                            <button class="btn-zentic btn-ai" id="saveChangesButton" type="button">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- About Tab -->
        <div class="settings-panel" id="tab-about">
            <div class="zentic-card">
                <div class="zentic-card-header">
                    <h2 class="card-title mb-0">About</h2>
                </div>
                <div class="zentic-card-body">
                    <form id="aboutupdate" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control-zentic" id="description" name="description" rows="3">{{ old('description', $profile->description ?? '') }}</textarea>
                                <div class="description_error text-danger-c text-xs mt-1" style="display:none;"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="establishment_year" class="form-label">Establishment Year</label>
                                <input type="number" class="form-control-zentic" id="establishment_year"
                                    name="establishment_year"
                                    value="{{ old('establishment_year', $profile->establishment_year ?? '') }}"
                                    min="1900" max="2100">
                                <div class="establishment_year_error text-danger-c text-xs mt-1" style="display:none;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="number_of_workers" class="form-label">Number Of Employees</label>
                                <input type="number" class="form-control-zentic" id="number_of_workers"
                                    name="number_of_workers"
                                    value="{{ old('number_of_workers', $profile->number_of_workers ?? '') }}">
                                <div class="number_of_workers_error text-danger-c text-xs mt-1" style="display:none;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="area_in_square_yard" class="form-label">Office Area in Sq. Yards</label>
                                <input type="number" class="form-control-zentic" id="area_in_square_yard"
                                    name="area_in_square_yard"
                                    value="{{ old('area_in_square_yard', $profile->area_in_square_yard ?? '') }}">
                                <div class="area_in_square_yard_error text-danger-c text-xs mt-1" style="display:none;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" class="form-control-zentic" id="website" name="website"
                                    value="{{ old('website', $profile->website ?? '') }}"
                                    placeholder="https://example.com">
                                <div class="website_error text-danger-c text-xs mt-1" style="display:none;"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">YouTube Link</label>
                                <input type="url" class="form-control-zentic" name="youtube"
                                    value="{{ old('youtube', $profile->youtube ?? '') }}">
                                <div class="youtube_error text-danger-c text-xs mt-1" style="display:none;"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Facebook Link</label>
                                <input type="url" class="form-control-zentic" name="facebook"
                                    value="{{ old('facebook', $profile->facebook ?? '') }}">
                                <div class="facebook_error text-danger-c text-xs mt-1" style="display:none;"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Instagram Link</label>
                                <input type="url" class="form-control-zentic" name="instagram"
                                    value="{{ old('instagram', $profile->instagram ?? '') }}">
                                <div class="instagram_error text-danger-c text-xs mt-1" style="display:none;"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">LinkedIn Link</label>
                                <input type="url" class="form-control-zentic" name="linkedin"
                                    value="{{ old('linkedin', $profile->linkedin ?? '') }}">
                                <div class="linkedin_error text-danger-c text-xs mt-1" style="display:none;"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="zentic-card-footer text-end">
                    <button class="btn-zentic btn-ai" id="saveAbout" type="button">Save changes</button>
                </div>
            </div>
        </div>

        <!-- KYC Verification -->
        <div class="settings-panel" id="tab-kyc">
            <div class="zentic-card">
                <div class="zentic-card-header">
                    <h2 class="card-title mb-0">KYC Verification</h2>
                </div>
                <div class="zentic-card-body">
                    <form id="bank" enctype="multipart/form-data">
                        @csrf
                        <h6 class="settings-section-title my-3">Bank Account Details</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="bank_name" class="form-label">Bank Name</label>
                                <input type="text" class="form-control-zentic" id="bank_name" name="bank_name"
                                    value="{{ $profile->bank_name ?? '' }}"
                                    {{ !empty($profile->bank_name) ? 'disabled' : '' }}>
                                <div class="bank_name_error text-danger-c text-xs mt-1" style="display:none;"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="account_number" class="form-label">Account Number</label>
                                <input type="number" class="form-control-zentic" id="account_number"
                                    name="account_number" value="{{ $profile->account_number ?? '' }}"
                                    {{ !empty($profile->account_number) ? 'disabled' : '' }}>
                                <div class="account_number_error text-danger-c text-xs mt-1" style="display:none;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="branch_name" class="form-label">Branch Name</label>
                                <input type="text" class="form-control-zentic" id="branch_name" name="branch_name"
                                    value="{{ $profile->branch_name ?? '' }}"
                                    {{ !empty($profile->branch_name) ? 'disabled' : '' }}>
                                <div class="branch_name_error text-danger-c text-xs mt-1" style="display:none;"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="ifsc_code" class="form-label">IFSC Code</label>
                                <input type="text" class="form-control-zentic text-uppercase" id="ifsc_code"
                                    name="ifsc_code" maxlength="11" value="{{ $profile->ifsc_code ?? '' }}"
                                    {{ !empty($profile->ifsc_code) ? 'disabled' : '' }}>
                                <div class="ifsc_code_error text-danger-c text-xs mt-1" style="display:none;"></div>
                            </div>
                        </div>

                        <h6 class="settings-section-title my-3">KYC Update</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="aadhar_number" class="form-label">Aadhar Card Number</label>
                                <input class="form-control-zentic" type="number" id="aadhar_number"
                                    name="aadhar_number" value="{{ $profile->aadhar_number ?? '' }}"
                                    {{ !empty($profile->aadhar_number) ? 'disabled' : '' }}>
                                <div class="aadhar_number_error text-danger-c text-xs mt-1" style="display:none;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="pan_number" class="form-label">PAN Card Number</label>
                                <input class="form-control-zentic text-uppercase" type="text" id="pan_number"
                                    name="pan_number" maxlength="10" value="{{ $profile->pan_number ?? '' }}"
                                    {{ !empty($profile->pan_number) ? 'disabled' : '' }}>
                                <div class="pan_number_error text-danger-c text-xs mt-1" style="display:none;"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="aadhar_image" class="form-label">Aadhar Card Image</label>
                                @if (empty($profile->aadhar_image))
                                    <div class="file-upload-zone" data-file-list="aadharImageFileList">
                                        <input type="file" id="aadhar_image" name="aadhar_image"
                                            accept="image/jpeg,image/png,image/jpg">
                                        <div class="file-upload-icon"><i class="icon-cloud-upload"></i></div>
                                        <div class="file-upload-title">Drop image here or click to browse</div>
                                        <div class="file-upload-desc">JPEG or PNG</div>
                                    </div>
                                    <div class="file-list" id="aadharImageFileList"></div>
                                @else
                                    <a href="{{ asset($profile->aadhar_image) }}" target="_blank" class="d-block mt-2">
                                        <img src="{{ asset($profile->aadhar_image) }}" alt="Aadhar Card"
                                            class="img-fluid rounded border"
                                            style="max-width: 250px; max-height: 180px; object-fit: contain;">
                                    </a>
                                @endif
                                <div class="aadhar_image_error text-danger-c text-xs mt-1" style="display:none;"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="personal_pan_image" class="form-label">Personal PAN Card Image</label>
                                @if (empty($profile->personal_pan_image))
                                    <div class="file-upload-zone" data-file-list="panImageFileList">
                                        <input type="file" id="personal_pan_image" name="personal_pan_image"
                                            accept="image/jpeg,image/png,image/jpg">
                                        <div class="file-upload-icon"><i class="icon-cloud-upload"></i></div>
                                        <div class="file-upload-title">Drop image here or click to browse</div>
                                        <div class="file-upload-desc">JPEG or PNG</div>
                                    </div>
                                    <div class="file-list" id="panImageFileList"></div>
                                @else
                                    <a href="{{ asset($profile->personal_pan_image) }}" target="_blank"
                                        class="d-block mt-2">
                                        <img src="{{ asset($profile->personal_pan_image) }}" alt="PAN Card"
                                            class="img-fluid rounded border"
                                            style="max-width: 250px; max-height: 180px; object-fit: contain;">
                                    </a>
                                @endif
                                <div class="personal_pan_image_error text-danger-c text-xs mt-1" style="display:none;">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="zentic-card-footer text-end">
                    <button class="btn-zentic btn-ai" id="savebank" type="button">Save Changes</button>
                </div>
            </div>
        </div>

        <!-- Photos & Video -->
        <div class="settings-panel" id="tab-media">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="zentic-card mb-4">
                        <div class="zentic-card-header">
                            <h2 class="card-title mb-0">Images</h2>
                            <span class="badge-zentic badge-green text-xs">{{ count($existingImages) }} uploaded</span>
                        </div>
                        <div class="zentic-card-body">
                            <form id="imagesForm" enctype="multipart/form-data">
                                @csrf
                                <div class="file-upload-zone" data-file-list="imagesFileList">
                                    <input type="file" id="imagesInput" name="images[]" multiple
                                        accept="image/jpeg,image/png,image/jpg">
                                    <div class="file-upload-icon"><i class="icon-cloud-upload"></i></div>
                                    <div class="file-upload-title">Drop images here or click to browse</div>
                                    <div class="file-upload-desc">JPEG or PNG · Max 4MB per file · Multiple allowed
                                    </div>
                                </div>
                                <div class="file-list" id="imagesFileList"></div>
                                <div class="images_error text-danger-c text-xs mt-1" style="display:none;">
                                    Please select valid image files (PNG or JPEG).</div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="button" class="btn-zentic btn-ai btn-sm" id="uploadImages"><i
                                            class="icon-upload"></i> Upload</button>
                                </div>
                            </form>

                            @if (count($existingImages) > 0)
                                <hr>
                                <div class="row g-2" id="imagesGrid">
                                    @foreach ($existingImages as $img)
                                        <div class="col-4" data-image="{{ $img }}">
                                            <div class="position-relative">
                                                <img src="{{ asset($img) }}" class="doc-preview-img"
                                                    style="height:90px;">
                                                <button type="button"
                                                    class="btn btn-outline-danger btn-sm rounded delete-media"
                                                    data-type="images" data-image="{{ $img }}"
                                                    style="position:absolute;top:4px;right:4px;padding:2px 6px;">
                                                    <i class="icon-trash-2"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="zentic-card">
                        <div class="zentic-card-header">
                            <h2 class="card-title mb-0">Video</h2>
                            <span class="badge-zentic badge-orange text-xs">Max 5MB</span>
                        </div>
                        <div class="zentic-card-body">
                            <form id="videoForm" enctype="multipart/form-data">
                                @csrf
                                <div class="file-upload-zone" data-file-list="videoFileList">
                                    <input type="file" id="videoInput" name="video" accept="video/*">
                                    <div class="file-upload-icon"><i class="icon-film"></i></div>
                                    <div class="file-upload-title">Drop video here or click to browse</div>
                                    <div class="file-upload-desc">MP4, MOV or AVI · Max 5MB</div>
                                </div>
                                <div class="file-list" id="videoFileList"></div>
                                <div class="video_error text-danger-c text-xs mt-1" style="display:none;">
                                    Please select a valid video file (Max 5MB).</div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="button" class="btn-zentic btn-ai btn-sm" id="uploadVideo"><i
                                            class="icon-upload"></i> Upload</button>
                                </div>
                            </form>

                            @if (!empty($profile->company_video))
                                <hr>
                                <video src="{{ asset($profile->company_video) }}" controls
                                    style="width:100%;max-height:220px;border-radius:8px;"></video>
                                <div class="d-flex justify-content-end mt-2">
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded delete-media"
                                        data-type="video" data-image="{{ $profile->company_video }}">
                                        <i class="icon-trash-2"></i> Delete Video
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Mobile OTP Verification Modal --}}
    <div class="modal fade" id="otpVerificationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">OTP Verification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-3">
                    <form id="verifyOtpForm">
                        @csrf
                        <label for="mobileotpInput" class="form-label">Enter OTP</label>
                        <input type="number" id="mobileotpInput" name="mobile_otp" class="form-control-zentic"
                            placeholder="Enter OTP" maxlength="6" minlength="6">
                        <div class="otp_error text-danger-c text-xs mt-1" style="display:none;">Please enter a valid
                            6-digit OTP.</div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="verifyOtp" class="btn-zentic btn-ai" type="button">Verify</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            // ===== Tab switching =====
            $('.settings-tab').on('click', function() {
                $('.settings-tab').removeClass('active');
                $(this).addClass('active');
                $('.settings-panel').removeClass('active');
                $('#' + $(this).data('tab')).addClass('active');
            });

            // ===== Button loader =====
            function setBtnLoading($btn, isLoading, loadingText) {
                if (isLoading) {
                    if (!$btn.data('original-html')) {
                        $btn.data('original-html', $btn.html());
                    }
                    $btn.prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> ' +
                        (loadingText || 'Saving...')
                    );
                } else {
                    $btn.prop('disabled', false).html($btn.data('original-html') || $btn.html());
                }
            }

            function clearErrors($form) {
                $form.find('.error, [class$="_error"]').hide().text('');
            }

            // ===== 422 validation errors from server =====
            function showServerErrors($form, errors) {
                $.each(errors, function(field, messages) {
                    var $specific = $form.find('.' + field + '_error');
                    if ($specific.length) {
                        $specific.text(messages[0]).show();
                        return;
                    }
                    var $input = $form.find('#' + field + ', [name="' + field + '"]').first();
                    var $inline = $input.next('.error');
                    if ($inline.length) {
                        $inline.text(messages[0]).show();
                    } else {
                        toastr.error(messages[0]);
                    }
                });
            }

            // ================= PROFILE TAB =================
            $('#mobile').on('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 10);
            });

            function validateProfileInfo() {
                var $form = $('#info');
                clearErrors($form);
                var isValid = true;

                if ($('#full_name').val().trim() === "") {
                    isValid = false;
                    $('#full_name').next('.error').text('Please enter your full name.').show();
                }

                if (!$('#mobile').prop('disabled')) {
                    var mobile = $('#mobile').val().trim();
                    if (!/^[6-9]\d{9}$/.test(mobile)) {
                        isValid = false;
                        $('#mobile').next('.error').text('Please enter a valid 10-digit mobile number.').show();
                    }
                }

                var email = $('#email').val().trim();
                if (!$('#email').prop('disabled') && email !== "" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    isValid = false;
                    $('#email').next('.error').text('Please enter a valid email.').show();
                }

                var password = $('#password').val();
                if (password.trim() !== "") {
                    if (password.length < 6) {
                        isValid = false;
                        $('#password').next('.error').text('Password must be at least 6 characters long.').show();
                    }
                    if ($('#confirmPassword').val() !== password) {
                        isValid = false;
                        $('#confirmPassword').next('.error').text('Passwords do not match.').show();
                    }
                }

                return isValid;
            }

            $('#saveChangesButton').on('click', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var $form = $('#info');

                if (!validateProfileInfo()) {
                    toastr.warning("Please fix the errors before submitting.");
                    return;
                }

                setBtnLoading($btn, true);

                $.ajax({
                    type: "POST",
                    url: "{{ route('member.personal.info.update') }}",
                    data: new FormData($form[0]),
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            location.reload();
                        } else {
                            toastr.error(response.message ||
                            "Update failed. Please try again.");
                        }
                    },
                    error: function(xhr) {
                        clearErrors($form);
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            showServerErrors($form, xhr.responseJSON.errors);
                        } else {
                            toastr.error("An error occurred while submitting the form.");
                        }
                    },
                    complete: function() {
                        setBtnLoading($btn, false);
                    }
                });
            });

            // ================= ABOUT TAB =================
            $('#saveAbout').on('click', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var $form = $('#aboutupdate');
                clearErrors($form);

                var formData = new FormData($form[0]);
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.description) {
                    formData.set("description", CKEDITOR.instances.description.getData());
                }

                setBtnLoading($btn, true);

                $.ajax({
                    type: "POST",
                    url: "{{ route('member.about.update') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message || "About updated successfully!");
                            location.reload();
                        } else {
                            toastr.error(response.message || "Update failed. Try again.");
                        }
                    },
                    error: function(xhr) {
                        clearErrors($form);
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            showServerErrors($form, xhr.responseJSON.errors);
                        } else {
                            toastr.error("Something went wrong.");
                        }
                    },
                    complete: function() {
                        setBtnLoading($btn, false);
                    }
                });
            });

            // ================= KYC TAB =================
            $('#ifsc_code, #pan_number').on('input', function() {
                this.value = this.value.toUpperCase();
            });

            $('#savebank').on('click', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var $form = $('#bank');
                clearErrors($form);

                setBtnLoading($btn, true);

                $.ajax({
                    type: 'POST',
                    url: "{{ route('member.kyc.update') }}",
                    data: new FormData($form[0]),
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message ||
                                "KYC details updated successfully!");
                            location.reload();
                        } else {
                            toastr.error(response.message || "Failed to update KYC details.");
                        }
                    },
                    error: function(xhr) {
                        clearErrors($form);
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            showServerErrors($form, xhr.responseJSON.errors);
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            toastr.error(xhr.responseJSON.message);
                        } else {
                            toastr.error("Something went wrong.");
                        }
                    },
                    complete: function() {
                        setBtnLoading($btn, false);
                    }
                });
            });

            // ================= MOBILE OTP =================
            $('#MobileverifyOtpButton').on('click', function(event) {
                event.preventDefault();
                $.ajax({
                    url: '#',
                    method: 'POST',
                    data: {
                        user_id: {{ $user->id }},
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#otpVerificationModal').modal('show');
                        toastr.success(response.message);
                    },
                    error: function(response) {
                        toastr.error(response.responseJSON.message);
                    }
                });
            });

            $('#verifyOtp').on('click', function() {
                var $btn = $(this);
                var otp = $('#mobileotpInput').val();
                $('.otp_error').hide();
                if (!otp || otp.length !== 6 || isNaN(otp)) {
                    $('.otp_error').text('Please enter a valid 6-digit OTP.').show();
                    return;
                }
                setBtnLoading($btn, true, 'Verifying...');
                $.ajax({
                    url: '#',
                    method: 'POST',
                    data: {
                        otp: otp,
                        user_id: {{ $user->id }},
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#otpVerificationModal').modal('hide');
                        toastr.success(response.message);
                        location.reload();
                    },
                    error: function(response) {
                        toastr.error(response.responseJSON.message);
                    },
                    complete: function() {
                        setBtnLoading($btn, false, 'Verifying...');
                    }
                });
            });

            // ===== Drag-and-drop upload zones =====
            function initUploadZone(zoneSelector, inputEl, listEl) {
                const $zone = $(zoneSelector);
                const $input = $(inputEl);
                const $list = $(listEl);

                if (!$zone.length || !$input.length) return;

                function renderFiles() {
                    $list.empty();
                    const files = $input[0].files;
                    if (!files || files.length === 0) return;
                    $.each(files, function(i, file) {
                        const sizeKb = (file.size / 1024).toFixed(0);
                        const $item = $(
                            '<div class="file-list-item d-flex align-items-center justify-content-between py-1">' +
                            '<span class="text-sm"><i class="icon-file"></i> <span class="fname"></span></span>' +
                            '<span class="text-xs text-muted">' + sizeKb + ' KB</span></div>'
                        );
                        $item.find('.fname').text(file.name); // text() => file name XSS-safe
                        $list.append($item);
                    });
                }

                $input.on('change', renderFiles);

                $zone.on('dragover', function(e) {
                    e.preventDefault();
                    $zone.addClass('dragover');
                });
                $zone.on('dragleave', function() {
                    $zone.removeClass('dragover');
                });
                $zone.on('drop', function(e) {
                    e.preventDefault();
                    $zone.removeClass('dragover');
                    const dt = e.originalEvent.dataTransfer;
                    if (dt && dt.files && dt.files.length) {
                        $input[0].files = dt.files;
                        renderFiles();
                    }
                });
                $zone.on('click', function(e) {
                    if (e.target !== $input[0]) {
                        $input.trigger('click');
                    }
                });
            }

            initUploadZone('#info .file-upload-zone', '#profile_image', '#profileImageFileList');
            initUploadZone('#bank .file-upload-zone[data-file-list="aadharImageFileList"]', '#aadhar_image',
                '#aadharImageFileList');
            initUploadZone('#bank .file-upload-zone[data-file-list="panImageFileList"]', '#personal_pan_image',
                '#panImageFileList');
            initUploadZone('#imagesForm .file-upload-zone', '#imagesInput', '#imagesFileList');
            initUploadZone('#videoForm .file-upload-zone', '#videoInput', '#videoFileList');

            // ================= IMAGES =================
            $('#uploadImages').on('click', function() {
                var $btn = $(this);
                $('.images_error').hide();
                const files = $('#imagesInput')[0].files;
                if (!files || files.length === 0) {
                    $('.images_error').text('Please select at least one image.').show();
                    return;
                }
                setBtnLoading($btn, true, 'Uploading...');
                $.ajax({
                    type: 'POST',
                    url: "{{ route('member.images.update') }}",
                    data: new FormData($('#imagesForm')[0]),
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message || 'Images uploaded successfully!');
                            location.reload();
                        } else {
                            toastr.error('Failed to upload images.');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $('.images_error').text(
                                    'Please select valid image files (PNG or JPEG, max 4MB).')
                                .show();
                        } else {
                            toastr.error('An unexpected error occurred.');
                        }
                    },
                    complete: function() {
                        setBtnLoading($btn, false, 'Uploading...');
                    }
                });
            });

            // ================= VIDEO =================
            $('#uploadVideo').on('click', function(e) {
                e.preventDefault();
                var $btn = $(this);
                $('.video_error').hide();

                let file = $('#videoInput')[0].files[0];
                if (!file) {
                    $('.video_error').text("Please select a video file to upload.").show();
                    return;
                }
                if (!file.type.startsWith('video/')) {
                    $('.video_error').text("Invalid file type. Please select a valid video file.").show();
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    $('.video_error').text("File size should not exceed 5MB.").show();
                    return;
                }

                setBtnLoading($btn, true, 'Uploading...');
                $.ajax({
                    type: "POST",
                    url: "{{ route('member.video.update') }}",
                    data: new FormData($('#videoForm')[0]),
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message || "Video uploaded successfully!");
                            location.reload();
                        } else {
                            toastr.error("Failed to upload video. Please try again.");
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        } else {
                            toastr.error("An unexpected error occurred. Please try again.");
                        }
                    },
                    complete: function() {
                        setBtnLoading($btn, false, 'Uploading...');
                    }
                });
            });

            // ================= DELETE (image / video) =================
            $(document).on('click', '.delete-media', function() {
                const type = $(this).data('type');
                const image = $(this).data('image');
                if (!confirm('This file will be permanently deleted. Continue?')) return;

                $.ajax({
                    url: "{{ route('member.delete.image') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        image: image,
                        type: type
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            location.reload();
                        } else {
                            toastr.error(response.message || 'Failed to delete.');
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message ||
                            'An error occurred while deleting.');
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof CKEDITOR !== "undefined") {
                CKEDITOR.replace('description');
            }
        });
    </script>
@endpush

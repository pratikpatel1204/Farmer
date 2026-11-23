<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') . ' || Sign Up' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('member/img/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="{{ asset('member/css/bootstrap.min.css') }}" id="bootstrapLtr">
    <link rel="stylesheet" href="{{ asset('member/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('member/css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('member/css/icons.css') }}">
    <link rel="stylesheet" href="{{ asset('member/css/toastr.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-left-orb auth-left-orb-1"></div>
        <div class="auth-left-orb auth-left-orb-2"></div>
        <div class="auth-left-orb auth-left-orb-3"></div>

        <div class="auth-right">
            <div class="auth-form-container" style="max-width: 720px;">
                <div class="auth-form-header">
                    <div class="auth-form-logo">
                        <a href="{{ route('home') }}">
                            <img src="{{ asset('member/img/logo.png') }}" alt="{{ config('app.name') }}">
                        </a>
                    </div>
                    <h2 class="auth-form-title">Create your account</h2>
                    <p class="auth-form-subtitle">First tell us who you are</p>
                </div>

                <form id="registerForm" novalidate>
                    @csrf
                    <input type="hidden" name="address_source" id="address_source" value="google">

                    {{-- ===== Farmer / Non Farmer (compact) ===== --}}
                    <span class="type-label">Register as <span class="text-danger">*</span></span>
                    <div class="type-grid" id="userTypeGrid">
                        <label class="type-option">
                            <input type="radio" name="user_type" value="farmer">
                            <span class="type-card">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M7 20h10" />
                                    <path d="M10 20c5.5-2.5.8-6.4 3-10" />
                                    <path
                                        d="M9.5 9.4c1.1.8 1.8 2.2 2.3 3.7-2 .4-3.5.4-4.8-.3-1.2-.6-2.3-1.9-3-4.2 2.8-.5 4.4 0 5.5.8z" />
                                    <path
                                        d="M14.1 6a7 7 0 0 0-1.1 4c1.9-.1 3.3-.6 4.3-1.4 1-1 1.6-2.3 1.7-4.6-2.7.1-4 1-4.9 2z" />
                                </svg>
                                Farmer
                            </span>
                        </label>

                        <label class="type-option">
                            <input type="radio" name="user_type" value="non_farmer">
                            <span class="type-card">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                                Non Farmer
                            </span>
                        </label>
                    </div>
                    <div class="field-error text-danger" id="user_typeError"></div>

                    <div id="formBody">
                        <div class="row g-3 mt-1">

                            {{-- ===== Account details (all together) ===== --}}
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="full_name" class="form-label">Full Name <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group-zentic">
                                        <span class="input-group-addon"><i class="icon-user"></i></span>
                                        <input type="text" class="form-control-zentic" id="full_name"
                                            name="full_name" autocomplete="off">
                                    </div>
                                    <div class="field-error text-danger" id="full_nameError"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="mobile" class="form-label">Mobile No <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group-zentic">
                                        <span class="input-group-addon"><i class="icon-phone"></i></span>
                                        <input type="text" class="form-control-zentic" id="mobile"
                                            name="mobile" maxlength="10" inputmode="numeric" autocomplete="off">
                                    </div>
                                    <div class="field-error text-danger" id="mobileError"></div>
                                    <div class="field-status" id="mobileStatus"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="email" class="form-label">Email <small
                                            class="text-muted">(optional)</small></label>
                                    <div class="input-group-zentic">
                                        <span class="input-group-addon"><i class="icon-mail"></i></span>
                                        <input type="email" class="form-control-zentic" id="email"
                                            name="email" autocomplete="off">
                                    </div>
                                    <div class="field-error text-danger" id="emailError"></div>
                                    <div class="field-status" id="emailStatus"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="sponsor_id" class="form-label">Sponsor ID <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group-zentic">
                                        <span class="input-group-addon"><i class="icon-users"></i></span>
                                        <input type="text" class="form-control-zentic" id="sponsor_id"
                                            name="sponsor_id" autocomplete="off"
                                            value="{{ $prefillSponsor ?? '' }}">
                                    </div>
                                    <div class="field-error text-danger" id="sponsor_idError"></div>
                                    <div class="field-status" id="sponsorStatus"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="password" class="form-label">Password <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group-zentic">
                                        <span class="input-group-addon"><i class="icon-lock"></i></span>
                                        <input type="password" class="form-control-zentic" id="password"
                                            name="password" placeholder="Create a password">
                                        <span class="input-group-addon input-right" style="cursor: pointer;"
                                            onclick="togglePassword('password', 'togglePasswordIcon')">
                                            <i class="icon-eye" id="togglePasswordIcon"></i>
                                        </span>
                                    </div>
                                    <div class="field-error text-danger" id="passwordError"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="password_confirmation" class="form-label">Confirm Password <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group-zentic">
                                        <span class="input-group-addon"><i class="icon-lock"></i></span>
                                        <input type="password" class="form-control-zentic" id="password_confirmation"
                                            name="password_confirmation" placeholder="Re-enter your password">
                                        <span class="input-group-addon input-right" style="cursor: pointer;"
                                            onclick="togglePassword('password_confirmation', 'toggleConfirmPasswordIcon')">
                                            <i class="icon-eye" id="toggleConfirmPasswordIcon"></i>
                                        </span>
                                    </div>
                                    <div class="field-error text-danger" id="password_confirmationError"></div>
                                </div>
                            </div>

                            {{-- ===== Address ===== --}}
                            <div class="col-12">
                                <h6 class="reg-section-title">Address <span class="text-danger">*</span></h6>
                            </div>

                            <div class="col-12">
                                <div class="addr-toggle" role="group" aria-label="Address mode">
                                    <button type="button" class="addr-tab active" data-mode="google">Search on
                                        Google</button>
                                    <button type="button" class="addr-tab" data-mode="manual">Enter
                                        manually</button>
                                </div>

                                {{-- GOOGLE MODE: only search field + one-line result --}}
                                <div id="googleBox" class="mt-3">
                                    <div id="placeSearch"></div>
                                    <div class="place-summary" id="placeSummary">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                            <circle cx="12" cy="10" r="3" />
                                        </svg>
                                        <span id="placeSummaryText"></span>
                                    </div>
                                    <div class="field-error text-danger" id="placeError"></div>
                                </div>
                            </div>

                            {{-- MANUAL MODE: all inputs --}}
                            <div class="col-12" id="manualFields" style="display:none;">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label for="state_id" class="form-label">State <span
                                                    class="text-danger">*</span></label>
                                            <select id="state_id" name="state_id">
                                                <option value="">Select State</option>
                                                @foreach ($states as $state)
                                                    <option value="{{ $state->id }}">{{ $state->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="field-error text-danger" id="state_idError"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label for="city_id" class="form-label">City <span
                                                    class="text-danger">*</span></label>
                                            <select id="city_id" name="city_id" disabled>
                                                <option value="">Select State first</option>
                                            </select>
                                            <div class="field-error text-danger" id="city_idError"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label for="area" class="form-label">Area <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="plain-input" id="area" name="area"
                                                autocomplete="off" placeholder="Area / locality">
                                            <div class="field-error text-danger" id="areaError"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label for="pincode" class="form-label">Pincode <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="plain-input" id="pincode" name="pincode"
                                                maxlength="6" inputmode="numeric" autocomplete="off"
                                                placeholder="6-digit pincode">
                                            <div class="field-error text-danger" id="pincodeError"></div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group mb-0">
                                            <label for="address" class="form-label">Full Address <span
                                                    class="text-danger">*</span></label>
                                            <textarea class="plain-input" id="address" name="address" rows="2"
                                                placeholder="House no, street, landmark..."></textarea>
                                            <div class="field-error text-danger" id="addressError"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-0">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="terms"
                                            name="terms">
                                        <label class="form-check-label text-sm" for="terms">
                                            I accept all
                                            <a href="https://quickad.qtechitacademy.com/terms-conditions"
                                                target="_blank" class="text-primary-c fw-600">Terms and
                                                Conditions</a>
                                            <span class="text-danger">*</span>
                                        </label>
                                    </div>
                                    <div class="field-error text-danger" id="termsError"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn-zentic btn-ai w-100 justify-content-center mt-2"
                                    id="signupButton">
                                    <span id="btnText"><i class="icon-user-plus"></i> Create Account</span>
                                    <div class="spinner-border spinner-border-sm text-light ms-2" id="loader"
                                        style="display: none;" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="auth-divider"></div>
                <div class="auth-form-header mb-3">
                    <h2 class="auth-form-subtitle">Already have an account?</h2>
                </div>
                <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 mb-3">
                    <a href="{{ route('home') }}" class="btn-zentic btn-ai">
                        <i class="icon-arrow-left"></i>Back
                    </a>
                    <a href="{{ route('login') }}" class="btn-zentic btn-ai">
                        <i class="icon-log-in"></i>Log in
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content success-modal">
                <div class="modal-body text-center">
                    <div class="success-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <h5 class="success-title">Registration successful!</h5>
                    <p class="success-sub">Please save your login details.</p>

                    <div class="cred-list">
                        <div class="cred-row">
                            <div class="cred-label">Unique ID</div>
                            <div class="cred-value" id="credUniqueId"></div>
                            <button type="button" class="cred-copy" data-target="credUniqueId" title="Copy">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" />
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                </svg>
                            </button>
                        </div>
                        <div class="cred-row">
                            <div class="cred-label">Mobile Number</div>
                            <div class="cred-value" id="credMobile"></div>
                            <button type="button" class="cred-copy" data-target="credMobile" title="Copy">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" />
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                </svg>
                            </button>
                        </div>
                        <div class="cred-row">
                            <div class="cred-label">Password</div>
                            <div class="cred-value" id="credPassword"></div>
                            <button type="button" class="cred-copy" data-target="credPassword" title="Copy">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" />
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="success-note">You can log in with your Mobile Number or Unique ID. This password is
                        shown only once, so please save it now.</div>

                    <button type="button" class="btn-zentic btn-ai w-100 justify-content-center" id="goLoginBtn">
                        <i class="icon-log-in"></i> Go to Login
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('member/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('member/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('member/js/toastr.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        // ===================== GOOGLE PLACES (new Places API) =====================
        // Must be defined BEFORE the Google script tag (it is the loader callback).
        window.initGooglePlaces = async function() {
            try {
                const {
                    PlaceAutocompleteElement
                } = await google.maps.importLibrary('places');

                const el = new PlaceAutocompleteElement({
                    includedRegionCodes: ['in']
                });
                document.getElementById('placeSearch').appendChild(el);

                el.addEventListener('gmp-select', async function(ev) {
                    try {
                        const place = ev.placePrediction.toPlace();
                        await place.fetchFields({
                            fields: ['addressComponents', 'formattedAddress']
                        });
                        window.fillFromPlace(place);
                    } catch (err) {
                        toastr.error('Unable to read this address. Please enter it manually.');
                    }
                });
            } catch (err) {
                (window.googleFailed || function() {
                    window._gFail = true;
                })();
            }
        };

        window.gm_authFailure = function() {
            (window.googleFailed || function() {
                window._gFail = true;
            })();
        };
    </script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&loading=async&libraries=places&callback=initGooglePlaces">
    </script>

    <script>
        $(document).ready(function() {

            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-top-right",
                timeOut: 4000,
                preventDuplicates: true
            };

            // ---------- helpers ----------
            function setFieldError($input, $errorBox, message) {
                $input.addClass('is-invalid').removeClass('is-valid');
                $errorBox.text(message).addClass('show');
            }

            function clearFieldError($input, $errorBox) {
                $input.removeClass('is-invalid');
                $errorBox.text('').removeClass('show');
            }

            function setStatus($box, type, message) {
                $box.removeClass('checking available taken').addClass(type).text(message).css('display',
                    'block');
            }

            function clearStatus($box) {
                $box.removeClass('checking available taken').text('').css('display', 'none');
            }

            function debounce(fn, delay) {
                let timer;
                return function(...args) {
                    clearTimeout(timer);
                    timer = setTimeout(() => fn.apply(this, args), delay);
                };
            }

            function norm(s) {
                return String(s || '').toLowerCase().replace(/[^a-z0-9]/g, '');
            }

            var addressFields = ['state_id', 'city_id', 'area', 'pincode', 'address'];
            var fields = ['full_name', 'mobile', 'email', 'sponsor_id', 'password', 'password_confirmation']
                .concat(addressFields);

            function clearAllErrors() {
                fields.forEach(function(f) {
                    clearFieldError($('#' + f), $('#' + f + 'Error'));
                });
                clearFieldError($('#terms'), $('#termsError'));
                $('#userTypeGrid').removeClass('is-invalid');
                $('#user_typeError').text('');
                $('#placeError').text('');
            }

            function setLoading(isLoading) {
                $('#signupButton').prop('disabled', isLoading);
                $('#btnText').toggle(!isLoading);
                $('#loader').toggle(isLoading);
            }

            fields.forEach(function(f) {
                $('#' + f).on('input change', function() {
                    clearFieldError($(this), $('#' + f + 'Error'));
                });
            });
            $('#terms').on('change', function() {
                clearFieldError($(this), $('#termsError'));
            });

            // ---------- radio -> reveal form ----------
            $('input[name="user_type"]').on('change', function() {
                $('#userTypeGrid').removeClass('is-invalid');
                $('#user_typeError').text('');
                if (!$('#formBody').is(':visible')) {
                    $('#formBody').slideDown(250);
                }
                $('.auth-form-subtitle').first().text(
                    this.value === 'farmer' ? 'Welcome, farmer! Fill in your details' :
                    'Welcome! Fill in your details'
                );
            });

            // only digits
            $('#mobile, #pincode').on('input', function() {
                this.value = this.value.replace(/\D/g, '');
            });

            // ---------- Select2 (state + city from master) ----------
            $('#state_id').select2({
                placeholder: 'Select State',
                width: '100%'
            });
            $('#city_id').select2({
                placeholder: 'Select City',
                width: '100%'
            });

            var pendingCityName = null; // city name coming from Google
            var afterCitiesLoaded = null; // callback after cities finish loading
            var googlePicked = false; // true once a Google result was selected

            function runAfterCities() {
                if (typeof afterCitiesLoaded === 'function') {
                    var cb = afterCitiesLoaded;
                    afterCitiesLoaded = null;
                    cb();
                }
            }

            function loadCities(stateId) {
                var $city = $('#city_id');
                $city.prop('disabled', true).html('<option value="">Loading...</option>').trigger(
                    'change.select2');

                if (!stateId) {
                    $city.html('<option value="">Select State first</option>').trigger('change.select2');
                    pendingCityName = null;
                    runAfterCities();
                    return;
                }

                $.ajax({
                    url: "{{ url('/get-cities') }}/" + stateId,
                    method: "GET",
                    success: function(res) {
                        var html = '<option value="">Select City</option>';
                        (res.cities || []).forEach(function(c) {
                            html += '<option value="' + c.id + '">' + $('<div>').text(c.name)
                                .html() + '</option>';
                        });
                        $city.html(html).prop('disabled', false);

                        if (pendingCityName) {
                            var target = norm(pendingCityName);
                            var matchVal = '';
                            $city.find('option').each(function() {
                                if (!this.value) return;
                                if (norm($(this).text()) === target) {
                                    matchVal = this.value;
                                    return false;
                                }
                            });
                            if (!matchVal) {
                                $city.find('option').each(function() {
                                    if (!this.value) return;
                                    var t = norm($(this).text());
                                    if (t.indexOf(target) !== -1 || target.indexOf(t) !== -1) {
                                        matchVal = this.value;
                                        return false;
                                    }
                                });
                            }
                            if (matchVal) {
                                $city.val(matchVal);
                            }
                            pendingCityName = null;
                        }
                        $city.trigger('change.select2');
                        runAfterCities();
                    },
                    error: function() {
                        $city.html('<option value="">Unable to load cities</option>').trigger(
                            'change.select2');
                        toastr.error('Unable to load cities. Please try again.');
                        runAfterCities();
                    }
                });
            }

            $('#state_id').on('change', function() {
                loadCities($(this).val());
            });

            // ---------- address mode (Google / manual) ----------
            function setAddressMode(mode) {
                $('.addr-tab').removeClass('active').filter('[data-mode="' + mode + '"]').addClass('active');
                $('#googleBox').toggle(mode === 'google');
                $('#manualFields').toggle(mode === 'manual');
                $('#address_source').val(mode);
                $('#placeError').text('');
            }

            $('.addr-tab').on('click', function() {
                setAddressMode($(this).data('mode'));
            });

            window.googleFailed = function() {
                setAddressMode('manual');
                $('.addr-tab[data-mode="google"]').prop('disabled', true).attr('title',
                    'Google search is unavailable');
                toastr.info('Google search is unavailable. Please fill your address manually.');
            };

            if (window._gFail) {
                window.googleFailed();
            }

            // ---------- Fill from Google place ----------
            function showSummary(text) {
                $('#placeSummaryText').text(text).attr('title', text);
                $('#placeSummary').css('display', 'flex');
            }

            function finalizePlace(formatted) {
                var missing = !$('#state_id').val() || !$('#city_id').val() ||
                    !$('#area').val().trim() ||
                    !/^[1-9][0-9]{5}$/.test($('#pincode').val().trim()) ||
                    !$('#address').val().trim();

                googlePicked = true;

                if (missing) {
                    $('#placeSummary').hide();
                    setAddressMode('manual');
                    toastr.info('Some details were not found. Please complete the address below.');
                } else {
                    $('#placeError').text('');
                    showSummary(formatted);
                }
            }

            window.fillFromPlace = function(place) {
                var comps = place.addressComponents || [];
                var formatted = place.formattedAddress || '';

                function get() {
                    for (var i = 0; i < arguments.length; i++) {
                        var t = arguments[i];
                        for (var j = 0; j < comps.length; j++) {
                            if (comps[j].types.indexOf(t) !== -1) return comps[j].longText;
                        }
                    }
                    return '';
                }

                var stateName = get('administrative_area_level_1');
                var cityName = get('locality', 'administrative_area_level_3', 'administrative_area_level_2');
                var areaName = get('sublocality_level_1', 'sublocality', 'neighborhood',
                    'sublocality_level_2');
                var pin = get('postal_code');

                $('#area').val(areaName);
                $('#pincode').val(pin);
                $('#address').val(formatted);

                // match state with master list
                var target = norm(stateName);
                var stateVal = '';
                $('#state_id option').each(function() {
                    if (!this.value) return;
                    if (norm($(this).text()) === target) {
                        stateVal = this.value;
                        return false;
                    }
                });
                if (!stateVal && target) {
                    $('#state_id option').each(function() {
                        if (!this.value) return;
                        var t = norm($(this).text());
                        if (t.indexOf(target) !== -1 || target.indexOf(t) !== -1) {
                            stateVal = this.value;
                            return false;
                        }
                    });
                }

                if (stateVal) {
                    pendingCityName = cityName || null;
                    afterCitiesLoaded = function() {
                        finalizePlace(formatted);
                    };
                    $('#state_id').val(stateVal).trigger('change');
                } else {
                    pendingCityName = null;
                    afterCitiesLoaded = null;
                    $('#state_id').val('').trigger('change');
                    finalizePlace(formatted);
                }
            };

            // ============================================================
            // LIVE CHECKS: sponsor / email / mobile
            // ============================================================
            var sponsorValid = false;
            var emailOk = true; // email optional -> OK when empty
            var mobileAvailable = false;

            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            var mobileRegex = /^[6-9][0-9]{9}$/;
            var pincodeRegex = /^[1-9][0-9]{5}$/;

            // Sponsor
            var checkSponsor = debounce(function() {
                var sponsorId = $('#sponsor_id').val().trim();
                if (!sponsorId) {
                    sponsorValid = false;
                    $('#sponsor_id').removeClass('is-valid');
                    clearStatus($('#sponsorStatus'));
                    return;
                }
                setStatus($('#sponsorStatus'), 'checking', 'Checking Sponsor ID...');
                $.ajax({
                    url: "{{ url('/check-sponsor-id') }}/" + encodeURIComponent(sponsorId),
                    method: "GET",
                    success: function(r) {
                        if (r.success) {
                            sponsorValid = true;
                            clearFieldError($('#sponsor_id'), $('#sponsor_idError'));
                            $('#sponsor_id').addClass('is-valid');
                            setStatus($('#sponsorStatus'), 'available', '✔ Sponsor: ' + r.name);
                        } else {
                            sponsorValid = false;
                            $('#sponsor_id').removeClass('is-valid');
                            setStatus($('#sponsorStatus'), 'taken', '✘ ' + (r.message ||
                                'Invalid Sponsor ID'));
                        }
                    },
                    error: function() {
                        sponsorValid = false;
                        setStatus($('#sponsorStatus'), 'taken', '✘ Unable to verify right now');
                    }
                });
            }, 400);
            $('#sponsor_id').on('input', checkSponsor);
            if ($('#sponsor_id').val().trim()) {
                checkSponsor();
            }

            // Email (optional)
            var checkEmail = debounce(function() {
                var email = $('#email').val().trim();
                if (!email) {
                    emailOk = true;
                    clearStatus($('#emailStatus'));
                    $('#email').removeClass('is-valid');
                    return;
                }
                if (!emailRegex.test(email)) {
                    emailOk = false;
                    clearStatus($('#emailStatus'));
                    return;
                }
                setStatus($('#emailStatus'), 'checking', 'Checking email...');
                $.ajax({
                    url: "{{ route('check.email') }}",
                    method: "GET",
                    data: {
                        email: email
                    },
                    success: function(r) {
                        if (r.success && r.available) {
                            emailOk = true;
                            $('#email').addClass('is-valid');
                            setStatus($('#emailStatus'), 'available', '✔ Email is available');
                        } else {
                            emailOk = false;
                            $('#email').removeClass('is-valid');
                            setStatus($('#emailStatus'), 'taken',
                                '✘ This email is already registered');
                        }
                    },
                    error: function() {
                        emailOk = false;
                        setStatus($('#emailStatus'), 'taken', '✘ Unable to verify right now');
                    }
                });
            }, 400);
            $('#email').on('input', checkEmail);

            // Mobile
            var checkMobile = debounce(function() {
                var mobile = $('#mobile').val().trim();
                if (!mobileRegex.test(mobile)) {
                    mobileAvailable = false;
                    clearStatus($('#mobileStatus'));
                    return;
                }
                setStatus($('#mobileStatus'), 'checking', 'Checking mobile number...');
                $.ajax({
                    url: "{{ route('check.phone') }}",
                    method: "GET",
                    data: {
                        phone: mobile
                    },
                    success: function(r) {
                        if (r.success && r.available) {
                            mobileAvailable = true;
                            clearFieldError($('#mobile'), $('#mobileError'));
                            $('#mobile').addClass('is-valid');
                            setStatus($('#mobileStatus'), 'available',
                                '✔ Mobile number is available');
                        } else {
                            mobileAvailable = false;
                            $('#mobile').removeClass('is-valid');
                            setStatus($('#mobileStatus'), 'taken',
                                '✘ This mobile number is already registered');
                        }
                    },
                    error: function() {
                        mobileAvailable = false;
                        setStatus($('#mobileStatus'), 'taken', '✘ Unable to verify right now');
                    }
                });
            }, 400);
            $('#mobile').on('input', checkMobile);

            // ============================================================
            // SUCCESS MODAL (unique id / mobile / password)
            // ============================================================
            function copyText(t) {
                if (navigator.clipboard && window.isSecureContext) {
                    return navigator.clipboard.writeText(t);
                }
                return new Promise(function(resolve, reject) {
                    var ta = document.createElement('textarea');
                    ta.value = t;
                    ta.style.position = 'fixed';
                    ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    try {
                        document.execCommand('copy');
                        resolve();
                    } catch (err) {
                        reject(err);
                    }
                    document.body.removeChild(ta);
                });
            }

            $(document).on('click', '.cred-copy', function() {
                var text = $('#' + $(this).data('target')).text();
                copyText(text).then(function() {
                    toastr.success('Copied');
                }).catch(function() {
                    toastr.error('Unable to copy. Please copy it manually.');
                });
            });

            var loginRedirect = "{{ route('login') }}";
            var loginCreds = null;

            function showLoginDetails(res) {
                loginRedirect = res.redirectUrl || loginRedirect;
                loginCreds = res.login || null;

                // login details na mile to purana simple flow
                if (!loginCreds) {
                    toastr.success(res.message || 'Registration successful!');
                    setTimeout(function() {
                        window.location.href = loginRedirect;
                    }, 2000);
                    return;
                }

                $('#credUniqueId').text(loginCreds.unique_id);
                $('#credMobile').text(loginCreds.mobile);
                $('#credPassword').text(loginCreds.password);

                bootstrap.Modal.getOrCreateInstance(document.getElementById('successModal')).show();
            }

            $('#goLoginBtn').on('click', function() {
                // login page ye values se fields apne aap bhar deta hai
                if (loginCreds && loginCreds.unique_id && loginCreds.password) {
                    try {
                        sessionStorage.setItem('login_unique_id', loginCreds.unique_id);
                        sessionStorage.setItem('login_password', loginCreds.password);
                    } catch (e) {}
                }
                window.location.href = loginRedirect;
            });

            // ============================================================
            // SUBMIT
            // ============================================================
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();
                clearAllErrors();

                var userType = $('input[name="user_type"]:checked').val();
                var fullName = $('#full_name').val().trim();
                var mobile = $('#mobile').val().trim();
                var email = $('#email').val().trim();
                var sponsorId = $('#sponsor_id').val().trim();
                var password = $('#password').val();
                var passwordConf = $('#password_confirmation').val();
                var mode = $('#address_source').val();

                var stateId = $('#state_id').val();
                var cityId = $('#city_id').val();
                var area = $('#area').val().trim();
                var pincode = $('#pincode').val().trim();
                var address = $('#address').val().trim();

                var ok = true;

                if (!userType) {
                    $('#userTypeGrid').addClass('is-invalid');
                    $('#user_typeError').text('Please select Farmer or Non Farmer.');
                    ok = false;
                }

                if (!fullName) {
                    setFieldError($('#full_name'), $('#full_nameError'), 'Please enter your full name.');
                    ok = false;
                }

                if (!mobile) {
                    setFieldError($('#mobile'), $('#mobileError'), 'Please enter your mobile number.');
                    ok = false;
                } else if (!mobileRegex.test(mobile)) {
                    setFieldError($('#mobile'), $('#mobileError'),
                        'Please enter a valid 10-digit mobile number.');
                    ok = false;
                } else if (!mobileAvailable) {
                    setFieldError($('#mobile'), $('#mobileError'),
                        'This mobile number is already registered or not verified yet.');
                    ok = false;
                }

                if (email) {
                    if (!emailRegex.test(email)) {
                        setFieldError($('#email'), $('#emailError'),
                            'Please enter a valid email address.');
                        ok = false;
                    } else if (!emailOk) {
                        setFieldError($('#email'), $('#emailError'),
                            'This email is already registered or not verified yet.');
                        ok = false;
                    }
                }

                if (!sponsorId) {
                    setFieldError($('#sponsor_id'), $('#sponsor_idError'), 'Please enter Sponsor ID.');
                    ok = false;
                } else if (!sponsorValid) {
                    setFieldError($('#sponsor_id'), $('#sponsor_idError'), 'Invalid Sponsor ID.');
                    ok = false;
                }

                if (!password) {
                    setFieldError($('#password'), $('#passwordError'), 'Please enter a password.');
                    ok = false;
                } else if (password.length < 6) {
                    setFieldError($('#password'), $('#passwordError'),
                        'Password must be at least 6 characters long.');
                    ok = false;
                }

                if (!passwordConf) {
                    setFieldError($('#password_confirmation'), $('#password_confirmationError'),
                        'Please confirm your password.');
                    ok = false;
                } else if (password !== passwordConf) {
                    setFieldError($('#password_confirmation'), $('#password_confirmationError'),
                        'Passwords do not match.');
                    ok = false;
                }

                // ----- address -----
                if (mode === 'google') {
                    if (!googlePicked || !stateId || !cityId || !area || !pincode || !address) {
                        $('#placeError').text(
                            'Please search and select your address, or use "Enter manually".');
                        ok = false;
                    }
                } else {
                    if (!stateId) {
                        setFieldError($('#state_id'), $('#state_idError'), 'Please select your state.');
                        ok = false;
                    }
                    if (!cityId) {
                        setFieldError($('#city_id'), $('#city_idError'), 'Please select your city.');
                        ok = false;
                    }
                    if (!area) {
                        setFieldError($('#area'), $('#areaError'), 'Please enter your area.');
                        ok = false;
                    }
                    if (!pincode) {
                        setFieldError($('#pincode'), $('#pincodeError'), 'Please enter your pincode.');
                        ok = false;
                    } else if (!pincodeRegex.test(pincode)) {
                        setFieldError($('#pincode'), $('#pincodeError'),
                            'Please enter a valid 6-digit pincode.');
                        ok = false;
                    }
                    if (!address) {
                        setFieldError($('#address'), $('#addressError'),
                            'Please enter your full address.');
                        ok = false;
                    }
                }

                if (!$('#terms').is(':checked')) {
                    setFieldError($('#terms'), $('#termsError'),
                        'You must accept the Terms and Conditions.');
                    ok = false;
                }

                if (!ok) {
                    toastr.error('Please fix the highlighted fields.');
                    return;
                }

                setLoading(true);

                $.ajax({
                    url: "{{ route('register.submit') }}",
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            // button disabled hi rahe (double submit na ho), modal me details dikhao
                            setLoading(false);
                            $('#signupButton').prop('disabled', true);
                            showLoginDetails(res);
                        } else {
                            setLoading(false);
                            toastr.error(res.message || 'Registration failed.');
                        }
                    },
                    error: function(xhr) {
                        setLoading(false);
                        if (xhr.status === 422) {
                            var r = xhr.responseJSON;
                            if (r && r.errors) {
                                $.each(r.errors, function(key, value) {
                                    if (addressFields.indexOf(key) !== -1 && $(
                                            '#address_source').val() === 'google') {
                                        $('#placeError').text(value[0]);
                                    } else if ($('#' + key + 'Error').length) {
                                        setFieldError($('#' + key), $('#' + key +
                                            'Error'), value[0]);
                                    } else {
                                        toastr.error(value[0]);
                                    }
                                });
                            } else {
                                toastr.error((r && r.message) ||
                                    'Please check the highlighted fields and try again.');
                            }
                        } else if (xhr.status === 429) {
                            toastr.error('Too many attempts. Please try again later.');
                        } else {
                            toastr.error('An unexpected error occurred. Please try again.');
                        }
                    }
                });
            });
        });

        function togglePassword(inputId, iconId) {
            const field = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (field.type === "password") {
                field.type = "text";
                icon.classList.remove("icon-eye");
                icon.classList.add("icon-eye-off");
            } else {
                field.type = "password";
                icon.classList.remove("icon-eye-off");
                icon.classList.add("icon-eye");
            }
        }
    </script>
</body>

</html>
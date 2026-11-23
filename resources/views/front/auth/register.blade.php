@extends('front.layout.app')
@section('title', 'Create account - ' . config('app.name'))
@section('content')
@include('front.auth._assets', ['select2' => true])
<div class="inner-page">
    <section class="auth-section">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-11">
                    <div class="auth-card">
                        @include('front.auth._side', [
                            'heading' => 'Join KisanMandi for free',
                            'text' => 'Sell your harvest or find the produce you need. One profile, buyers and farmers across India.',
                        ])

                        <div class="auth-form-wrap">
                            <h2 class="auth-title">Create your account</h2>
                            <p class="auth-subtitle" id="authSubtitle">First tell us who you are</p>

                            <form id="registerForm" class="farm-form" novalidate>
                                @csrf
                                <input type="hidden" name="address_source" id="address_source" value="google">

                                {{-- ===== Farmer / Non Farmer ===== --}}
                                <fieldset>
                                    <legend class="form-label">Register as <span class="req">*</span></legend>
                                    <div class="role-choose" id="userTypeGrid">
                                        <div class="role-option role-farmer">
                                            <input type="radio" name="user_type" id="typeFarmer" value="farmer">
                                            <label for="typeFarmer">
                                                <span class="role-icon"><i class="fa-solid fa-seedling"></i></span>
                                                <strong>Farmer</strong>
                                                <small>I grow and sell produce</small>
                                            </label>
                                        </div>
                                        <div class="role-option role-nonfarmer">
                                            <input type="radio" name="user_type" id="typeNonFarmer" value="non_farmer">
                                            <label for="typeNonFarmer">
                                                <span class="role-icon"><i class="fa-solid fa-store"></i></span>
                                                <strong>Non Farmer</strong>
                                                <small>I want to buy produce</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="field-error" id="user_typeError"></div>
                                    <p class="type-hint" id="typeHint"><i class="fa-solid fa-hand-pointer"></i>Choose Farmer or Non Farmer to continue.</p>
                                </fieldset>

                                <div id="formBody">
                                    <div class="row g-3 mt-1">

                                        {{-- ===== Account details ===== --}}
                                        <div class="col-md-6">
                                            <label for="full_name" class="form-label">Full Name <span class="req">*</span></label>
                                            <div class="input-icon">
                                                <i class="fa-solid fa-user"></i>
                                                <input type="text" class="form-control" id="full_name" name="full_name"
                                                    placeholder="Your full name" autocomplete="off">
                                            </div>
                                            <div class="field-error" id="full_nameError"></div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="mobile" class="form-label">Mobile No <span class="req">*</span></label>
                                            <div class="input-icon">
                                                <i class="fa-solid fa-phone"></i>
                                                <input type="text" class="form-control" id="mobile" name="mobile"
                                                    maxlength="10" inputmode="numeric" placeholder="10-digit mobile" autocomplete="off">
                                            </div>
                                            <div class="field-error" id="mobileError"></div>
                                            <div class="field-status" id="mobileStatus"></div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Email <small>(optional)</small></label>
                                            <div class="input-icon">
                                                <i class="fa-solid fa-envelope"></i>
                                                <input type="email" class="form-control" id="email" name="email"
                                                    placeholder="you@example.com" autocomplete="off">
                                            </div>
                                            <div class="field-error" id="emailError"></div>
                                            <div class="field-status" id="emailStatus"></div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="sponsor_id" class="form-label">Sponsor ID <small class="text-muted">(optional)</small></label>
                                            <div class="input-icon">
                                                <i class="fa-solid fa-users"></i>
                                                <input type="text" class="form-control" id="sponsor_id" name="sponsor_id"
                                                    placeholder="Who referred you? (optional)" autocomplete="off" value="{{ $prefillSponsor ?? '' }}">
                                            </div>
                                            <div class="field-error" id="sponsor_idError"></div>
                                            <div class="field-status" id="sponsorStatus"></div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="password" class="form-label">Password <span class="req">*</span></label>
                                            <div class="input-icon">
                                                <i class="fa-solid fa-lock"></i>
                                                <input type="password" class="form-control has-toggle" id="password" name="password"
                                                    placeholder="Create a password" autocomplete="new-password">
                                                <button type="button" class="password-toggle" data-password-toggle="#password" aria-label="Show password"><i class="fa-solid fa-eye"></i></button>
                                            </div>
                                            <div class="field-error" id="passwordError"></div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="password_confirmation" class="form-label">Confirm Password <span class="req">*</span></label>
                                            <div class="input-icon">
                                                <i class="fa-solid fa-lock"></i>
                                                <input type="password" class="form-control has-toggle" id="password_confirmation"
                                                    name="password_confirmation" placeholder="Re-enter your password" autocomplete="new-password">
                                                <button type="button" class="password-toggle" data-password-toggle="#password_confirmation" aria-label="Show password"><i class="fa-solid fa-eye"></i></button>
                                            </div>
                                            <div class="field-error" id="password_confirmationError"></div>
                                        </div>

                                        {{-- ===== Address ===== --}}
                                        <div class="col-12">
                                            <h3 class="reg-section-title"><i class="fa-solid fa-location-dot"></i>Address <span class="req">*</span></h3>
                                        </div>

                                        <div class="col-12">
                                            <div class="addr-toggle" role="group" aria-label="Address mode">
                                                <button type="button" class="addr-tab active" data-mode="google"><i class="fa-brands fa-google"></i>Search on Google</button>
                                                <button type="button" class="addr-tab" data-mode="manual"><i class="fa-solid fa-pen"></i>Enter manually</button>
                                            </div>

                                            {{-- GOOGLE MODE: sirf search + ek line result --}}
                                            <div id="googleBox" class="mt-3">
                                                <div class="place-search-box">
                                                    <div id="placeSearch"></div>
                                                </div>
                                                <div class="place-summary" id="placeSummary">
                                                    <i class="fa-solid fa-circle-check"></i>
                                                    <span id="placeSummaryText"></span>
                                                </div>
                                                <div class="field-error" id="placeError"></div>
                                            </div>
                                        </div>

                                        {{-- MANUAL MODE --}}
                                        <div class="col-12" id="manualFields" style="display:none;">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="state_id" class="form-label">State <span class="req">*</span></label>
                                                    <select id="state_id" name="state_id">
                                                        <option value="">Select State</option>
                                                        @foreach ($states as $state)
                                                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="field-error" id="state_idError"></div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="city_id" class="form-label">City <span class="req">*</span></label>
                                                    <select id="city_id" name="city_id" disabled>
                                                        <option value="">Select State first</option>
                                                    </select>
                                                    <div class="field-error" id="city_idError"></div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="area" class="form-label">Area <span class="req">*</span></label>
                                                    <input type="text" class="form-control" id="area" name="area"
                                                        autocomplete="off" placeholder="Area / locality">
                                                    <div class="field-error" id="areaError"></div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="pincode" class="form-label">Pincode <span class="req">*</span></label>
                                                    <input type="text" class="form-control" id="pincode" name="pincode"
                                                        maxlength="6" inputmode="numeric" autocomplete="off" placeholder="6-digit pincode">
                                                    <div class="field-error" id="pincodeError"></div>
                                                </div>

                                                <div class="col-12">
                                                    <label for="address" class="form-label">Full Address <span class="req">*</span></label>
                                                    <textarea class="form-control" id="address" name="address" rows="2"
                                                        placeholder="House no, street, landmark..."></textarea>
                                                    <div class="field-error" id="addressError"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="terms" name="terms">
                                                <label class="form-check-label" for="terms">
                                                    I accept all
                                                    <a href="https://quickad.qtechitacademy.com/terms-conditions" target="_blank" rel="noopener" class="auth-link">Terms and Conditions</a>
                                                    <span class="req">*</span>
                                                </label>
                                            </div>
                                            <div class="field-error" id="termsError"></div>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-farm w-100" id="signupButton">
                                                <span id="btnText"><i class="fa-solid fa-user-plus me-1"></i>Create Account</span>
                                                <span class="spinner-border spinner-border-sm" id="loader" style="display: none;" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <p class="auth-switch">Already have an account? <a href="{{ route('login') }}" class="auth-link">Log in</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- Registration ke baad login details --}}
<div class="modal fade farm-modal" id="successModal" tabindex="-1" aria-hidden="true" aria-labelledby="successTitle"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center pt-4">
                <div class="success-icon"><i class="fa-solid fa-check"></i></div>
                <h5 class="success-title" id="successTitle">Registration successful!</h5>
                <p class="success-sub mb-0">Please save your login details.</p>

                <div class="cred-list">
                    <div class="cred-row">
                        <div class="cred-label">Unique ID</div>
                        <div class="cred-value" id="credUniqueId"></div>
                        <button type="button" class="cred-copy" data-target="credUniqueId" title="Copy" aria-label="Copy Unique ID"><i class="fa-regular fa-copy"></i></button>
                    </div>
                    <div class="cred-row">
                        <div class="cred-label">Mobile Number</div>
                        <div class="cred-value" id="credMobile"></div>
                        <button type="button" class="cred-copy" data-target="credMobile" title="Copy" aria-label="Copy mobile number"><i class="fa-regular fa-copy"></i></button>
                    </div>
                    <div class="cred-row">
                        <div class="cred-label">Password</div>
                        <div class="cred-value" id="credPassword"></div>
                        <button type="button" class="cred-copy" data-target="credPassword" title="Copy" aria-label="Copy password"><i class="fa-regular fa-copy"></i></button>
                    </div>
                </div>

                <div class="success-note"><i class="fa-solid fa-triangle-exclamation"></i><span>You can log in with your Mobile Number or Unique ID. This password is shown only once, so please save it now.</span></div>

                <button type="button" class="btn btn-farm w-100" id="goLoginBtn">
                    <i class="fa-solid fa-right-to-bracket me-1"></i>Go to Login
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
    @if (config('services.google.maps_key'))
        <script async defer
            src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&loading=async&libraries=places&callback=initGooglePlaces">
        </script>
    @else
        {{-- Google key nahi hai to seedha "Enter manually" khulega --}}
        <script>window._gFail = true;</script>
    @endif

    <script>
        $(document).ready(function() {

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
                    $('#typeHint').slideUp(200);
                }
                $('#authSubtitle').text(
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
                    // Sponsor ID optional hai: khali ho to koi error nahi
                    sponsorValid = false;
                    clearFieldError($('#sponsor_id'), $('#sponsor_idError'));
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

                // Sponsor ID optional: sirf bhara ho tab check karo
                if (sponsorId && !sponsorValid) {
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
    </script>
@endpush
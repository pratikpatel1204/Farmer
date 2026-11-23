@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || ' . ($member ? 'Edit Member' : 'Create Member'))

@section('content')
    @php
        $isEdit = (bool) $member;
        $addrMode = $isEdit ? (($member->address_source ?? '') === 'google' ? 'google' : 'manual') : 'google';
    @endphp
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">{{ $isEdit ? 'Edit Member' : 'Create Member' }}</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.member.details') }}">Member Details</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Create' }}</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.member.details') }}" class="btn btn-light mt-2 mt-md-0">
                <i class="ti ti-arrow-left"></i> Back
            </a>
        </div>

        <form id="memberForm" novalidate>
            @csrf
            <div class="row">
                {{-- ===== ACCOUNT ===== --}}
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0"><i class="ti ti-user-circle me-1"></i> Account Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Member Type <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3 flex-wrap">
                                    @foreach (['Farmer' => 'ti-plant-2', 'Non Farmer' => 'ti-user'] as $role => $icon)
                                        <label class="role-option">
                                            <input type="radio" name="role" value="{{ $role }}"
                                                {{ ($member->role ?? '') === $role ? 'checked' : '' }}>
                                            <span class="role-card"><i class="ti {{ $icon }}"></i>
                                                {{ $role }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="invalid-feedback d-block" data-error="role"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" class="form-control" maxlength="100"
                                        value="{{ $member->full_name ?? '' }}" placeholder="Enter full name">
                                    <div class="invalid-feedback" data-error="full_name"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mobile No <span class="text-danger">*</span></label>
                                    <input type="text" name="mobile" class="form-control" maxlength="10"
                                        inputmode="numeric" value="{{ $member->mobile ?? '' }}"
                                        placeholder="10-digit mobile">
                                    <div class="invalid-feedback" data-error="mobile"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email <small class="text-muted">(optional)</small></label>
                                    <input type="email" name="email" class="form-control" maxlength="150"
                                        value="{{ $member->email ?? '' }}" placeholder="name@example.com">
                                    <div class="invalid-feedback" data-error="email"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Sponsor ID @if (!$isEdit)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    @if ($isEdit)
                                        <input type="text" class="form-control"
                                            value="{{ $member->sponsor_id ? $member->sponsor_id . ' - ' . $member->sponsor_name : '-' }}"
                                            disabled>
                                        <small class="text-muted">Sponsor change nahi ho sakta.</small>
                                    @else
                                        <input type="text" name="sponsor_id" id="sponsor_id" class="form-control"
                                            placeholder="e.g. FRM000001">
                                        <div class="invalid-feedback" data-error="sponsor_id"></div>
                                        <small id="sponsorStatus" class="d-block mt-1"></small>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password
                                        @if ($isEdit)
                                            <small class="text-muted">(leave empty to keep current)</small>
                                        @else
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="password" class="form-control"
                                            autocomplete="new-password" placeholder="Min 6 characters">
                                        <button type="button" class="btn btn-light togglePass"><i
                                                class="ti ti-eye"></i></button>
                                    </div>
                                    <div class="invalid-feedback d-block" data-error="password"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Confirm Password @if (!$isEdit)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="password_confirmation" class="form-control"
                                            autocomplete="new-password" placeholder="Re-enter password">
                                        <button type="button" class="btn btn-light togglePass"><i
                                                class="ti ti-eye"></i></button>
                                    </div>
                                    <div class="invalid-feedback d-block" data-error="password_confirmation"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===== ADDRESS ===== --}}
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0"><i class="ti ti-map-pin me-1"></i> Address</h4>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="address_source" id="address_source"
                                value="{{ $addrMode }}">

                            <div class="addr-toggle mb-3" role="group">
                                <button type="button" class="btn btn-sm addr-tab" data-mode="google">
                                    <i class="ti ti-brand-google me-1"></i> Search on Google
                                </button>
                                <button type="button" class="btn btn-sm addr-tab" data-mode="manual">
                                    <i class="ti ti-pencil me-1"></i> Enter manually
                                </button>
                            </div>

                            {{-- GOOGLE MODE --}}
                            <div id="googleBox" class="mb-2">
                                <div id="placeSearch"></div>
                                <div class="place-summary" id="placeSummary">
                                    <i class="ti ti-map-pin-filled text-danger"></i>
                                    <span
                                        id="placeSummaryText">{{ $isEdit && $addrMode === 'google' ? $member->address : '' }}</span>
                                    <a href="#" class="ms-auto small" id="editAddrManual">Edit</a>
                                </div>
                                <div class="invalid-feedback d-block" id="placeError"></div>
                            </div>

                            {{-- MANUAL MODE --}}
                            <div class="row" id="manualFields">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">State <span class="text-danger">*</span></label>
                                    <select name="state" id="state" class="form-select">
                                        <option value="">Select State</option>
                                        @foreach ($states as $state)
                                            <option value="{{ $state->id }}"
                                                {{ (string) ($member->state ?? '') === (string) $state->id ? 'selected' : '' }}>
                                                {{ $state->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" data-error="state"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">City <span class="text-danger">*</span></label>
                                    <select name="city" id="city" class="form-select"
                                        {{ $cities->isEmpty() ? 'disabled' : '' }}>
                                        <option value="">
                                            {{ $cities->isEmpty() ? 'Select State first' : 'Select City' }}
                                        </option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}"
                                                {{ (string) ($member->city ?? '') === (string) $city->id ? 'selected' : '' }}>
                                                {{ $city->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" data-error="city"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Area <span class="text-danger">*</span></label>
                                    <input type="text" name="area" class="form-control" maxlength="150"
                                        value="{{ $member->area ?? '' }}" placeholder="Area / locality">
                                    <div class="invalid-feedback" data-error="area"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pincode <span class="text-danger">*</span></label>
                                    <input type="text" name="pincode" class="form-control" maxlength="6"
                                        inputmode="numeric" value="{{ $member->pincode ?? '' }}"
                                        placeholder="6-digit pincode">
                                    <div class="invalid-feedback" data-error="pincode"></div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Full Address <span class="text-danger">*</span></label>
                                    <textarea name="address" class="form-control" rows="2" maxlength="500"
                                        placeholder="House no, street, landmark...">{{ $member->address ?? '' }}</textarea>
                                    <div class="invalid-feedback" data-error="address"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== SIDE ===== --}}
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0"><i class="ti ti-settings me-1"></i> Settings</h4>
                        </div>
                        <div class="card-body">
                            @if ($isEdit)
                                <div class="mb-3">
                                    <label class="form-label">Member ID</label>
                                    <input type="text" class="form-control" value="{{ $member->unique_id }}"
                                        disabled>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="1" {{ (int) ($member->status ?? 1) === 1 ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ (int) ($member->status ?? 1) === 0 ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                            </div>
                            @if (!$isEdit)
                                <div class="alert alert-info mb-0 p-2">
                                    <i class="ti ti-info-circle"></i> Member ID type ke hisaab se auto generate hoga
                                    (FRM / NFR).
                                </div>
                            @endif
                        </div>
                        <div class="card-footer d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.member.details') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="saveBtn">
                                <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                                {{ $isEdit ? 'Update Member' : 'Create Member' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <style>
        .role-option input {
            display: none;
        }

        .role-card {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all .15s;
        }

        .role-card i {
            font-size: 20px;
        }

        .addr-tab {
            border: 1px solid #dee2e6;
            background: #fff;
        }

        .addr-tab.active {
            background: var(--bs-primary);
            border-color: var(--bs-primary);
            color: #fff;
        }

        #placeSearch gmp-place-autocomplete,
        #placeSearch gmp-placeautocomplete {
            width: 100%;
        }

        .place-summary {
            display: none;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            padding: 10px 12px;
            border: 1px dashed #ced4da;
            border-radius: 8px;
            background: #f8f9fa;
            font-size: 14px;
        }

        .role-option input:checked+.role-card {
            border-color: var(--bs-primary);
            background: rgba(var(--bs-primary-rgb), .08);
            color: var(--bs-primary);
        }
    </style>

    <script>
        const IS_EDIT = @json($isEdit);
        const SAVE_URL =
            "{{ $isEdit ? route('admin.member.details.update', $member->id) : route('admin.member.details.store') }}";
        const CITIES_URL = "{{ url('admin/member-details/cities') }}";

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        function setError(field, msg) {
            let $err = $('[data-error="' + field + '"]');
            $err.text(msg);
            $('[name="' + field + '"]').addClass('is-invalid');
        }

        function clearErrors() {
            $('[data-error]').text('');
            $('#placeError').text('');
            $('#memberForm .is-invalid').removeClass('is-invalid');
        }

        // numbers only
        $('[name="mobile"], [name="pincode"]').on('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });

        // show / hide password
        $(document).on('click', '.togglePass', function() {
            let $input = $(this).siblings('input');
            let show = $input.attr('type') === 'password';
            $input.attr('type', show ? 'text' : 'password');
            $(this).find('i').toggleClass('ti-eye ti-eye-off');
        });

        // ===================== ADDRESS: GOOGLE / MANUAL =====================
        const ADDR_MODE = @json($addrMode);
        let googlePicked = IS_EDIT && ADDR_MODE === 'google';
        let pendingCityName = null;
        let afterCitiesLoaded = null;

        function norm(str) {
            return String(str || '').toLowerCase().replace(/[^a-z0-9]/g, '');
        }

        function runAfterCities() {
            if (typeof afterCitiesLoaded === 'function') {
                let cb = afterCitiesLoaded;
                afterCitiesLoaded = null;
                cb();
            }
        }

        function matchOption($select, name) {
            let target = norm(name);
            if (!target) return '';
            let val = '';
            $select.find('option').each(function() {
                if (this.value && norm($(this).text()) === target) {
                    val = this.value;
                    return false;
                }
            });
            if (!val) {
                $select.find('option').each(function() {
                    if (!this.value) return;
                    let t = norm($(this).text());
                    if (t.indexOf(target) !== -1 || target.indexOf(t) !== -1) {
                        val = this.value;
                        return false;
                    }
                });
            }
            return val;
        }

        function loadCities(stateId) {
            let $city = $('#city');
            $city.html('<option value="">Loading...</option>').prop('disabled', true);
            if (!stateId) {
                $city.html('<option value="">Select State first</option>');
                pendingCityName = null;
                runAfterCities();
                return;
            }
            $.get(CITIES_URL + '/' + stateId, function(cities) {
                let html = '<option value="">Select City</option>';
                $.each(cities, function(i, c) {
                    html += '<option value="' + c.id + '">' + $('<div>').text(c.name).html() + '</option>';
                });
                $city.html(html).prop('disabled', false);
                if (pendingCityName) {
                    let v = matchOption($city, pendingCityName);
                    if (v) $city.val(v);
                    pendingCityName = null;
                }
                runAfterCities();
            }).fail(function() {
                toastr.error('Unable to load cities');
                $city.html('<option value="">Select State first</option>');
                runAfterCities();
            });
        }

        $('#state').on('change', function() {
            loadCities($(this).val());
        });

        function setAddressMode(mode) {
            $('.addr-tab').removeClass('active').filter('[data-mode="' + mode + '"]').addClass('active');
            $('#googleBox').toggle(mode === 'google');
            $('#manualFields').toggle(mode === 'manual');
            $('#address_source').val(mode);
            $('#placeError').text('');
        }

        $(document).on('click', '.addr-tab', function() {
            setAddressMode($(this).data('mode'));
        });

        $('#editAddrManual').on('click', function(e) {
            e.preventDefault();
            setAddressMode('manual');
        });

        // user ne manual fields badle to google pick ka summary ab sahi nahi
        $('#manualFields').on('input change', 'input, select, textarea', function() {
            if ($('#address_source').val() === 'manual') {
                $('#placeSummary').hide();
            }
        });

        setAddressMode(ADDR_MODE);
        if (googlePicked && $('#placeSummaryText').text().trim()) {
            $('#placeSummary').css('display', 'flex');
        }

        window.googleFailed = function() {
            setAddressMode('manual');
            $('.addr-tab[data-mode="google"]').prop('disabled', true).attr('title', 'Google search is unavailable');
        };
        if (window._gFail) {
            window.googleFailed();
        }

        function finalizePlace(formatted) {
            let missing = !$('#state').val() || !$('#city').val() || !$('[name="area"]').val().trim() ||
                !/^\d{6}$/.test($('[name="pincode"]').val().trim()) || !$('[name="address"]').val().trim();

            googlePicked = true;
            if (missing) {
                $('#placeSummary').hide();
                setAddressMode('manual');
                toastr.info('Kuch details nahi mili. Baaki address niche bhar do.');
            } else {
                $('#placeError').text('');
                $('#placeSummaryText').text(formatted);
                $('#placeSummary').css('display', 'flex');
            }
        }

        window.fillFromPlace = function(place) {
            let comps = place.addressComponents || [];
            let formatted = place.formattedAddress || '';

            function get() {
                for (let i = 0; i < arguments.length; i++) {
                    for (let j = 0; j < comps.length; j++) {
                        if (comps[j].types.indexOf(arguments[i]) !== -1) return comps[j].longText;
                    }
                }
                return '';
            }

            $('[name="area"]').val(get('sublocality_level_1', 'sublocality', 'neighborhood', 'sublocality_level_2'));
            $('[name="pincode"]').val(get('postal_code'));
            $('[name="address"]').val(formatted);

            let stateVal = matchOption($('#state'), get('administrative_area_level_1'));
            if (stateVal) {
                pendingCityName = get('locality', 'administrative_area_level_3', 'administrative_area_level_2') || null;
                afterCitiesLoaded = function() {
                    finalizePlace(formatted);
                };
                $('#state').val(stateVal);
                loadCities(stateVal);
            } else {
                $('#state').val('');
                loadCities('');
                finalizePlace(formatted);
            }
        };

        // SPONSOR CHECK
        let sponsorTimer = null;
        $('#sponsor_id').on('input', function() {
            let val = $(this).val().trim().toUpperCase();
            $(this).val(val);
            clearTimeout(sponsorTimer);
            $('#sponsorStatus').text('').removeClass('text-success text-danger');
            if (val.length < 4) return;
            sponsorTimer = setTimeout(function() {
                $.get("{{ route('admin.member.details.sponsor.check') }}", {
                    sponsor_id: val
                }, function(res) {
                    $('#sponsorStatus')
                        .text(res.success ? '✔ ' + res.name : '✖ ' + res.message)
                        .addClass(res.success ? 'text-success' : 'text-danger');
                });
            }, 400);
        });

        function validateForm() {
            clearErrors();
            let ok = true;
            let v = (n) => ($('[name="' + n + '"]').val() || '').trim();

            if (!$('[name="role"]:checked').length) {
                setError('role', 'Please select member type.');
                ok = false;
            }
            if (!v('full_name')) {
                setError('full_name', 'Please enter full name.');
                ok = false;
            }
            if (!/^[6-9]\d{9}$/.test(v('mobile'))) {
                setError('mobile', 'Please enter a valid 10-digit mobile number.');
                ok = false;
            }
            if (v('email') && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v('email'))) {
                setError('email', 'Please enter a valid email.');
                ok = false;
            }
            if (!IS_EDIT && !v('sponsor_id')) {
                setError('sponsor_id', 'Please enter sponsor ID.');
                ok = false;
            }
            let pass = $('[name="password"]').val();
            if (!IS_EDIT || pass) {
                if (pass.length < 6) {
                    setError('password', 'Password must be at least 6 characters.');
                    ok = false;
                } else if (pass !== $('[name="password_confirmation"]').val()) {
                    setError('password_confirmation', 'Passwords do not match.');
                    ok = false;
                }
            }
            if (v('address_source') === 'google') {
                if (!googlePicked || !v('state') || !v('city') || !v('area') || !/^\d{6}$/.test(v('pincode')) ||
                    !v('address')) {
                    $('#placeError').text('Please search and select the address, or use "Enter manually".');
                    ok = false;
                }
            } else {
                if (!v('state')) {
                    setError('state', 'Please select state.');
                    ok = false;
                }
                if (!v('city')) {
                    setError('city', 'Please select city.');
                    ok = false;
                }
                if (!v('area')) {
                    setError('area', 'Please enter area.');
                    ok = false;
                }
                if (!/^\d{6}$/.test(v('pincode'))) {
                    setError('pincode', 'Please enter a valid 6-digit pincode.');
                    ok = false;
                }
                if (!v('address')) {
                    setError('address', 'Please enter full address.');
                    ok = false;
                }
            }
            return ok;
        }

        $('#memberForm').on('submit', function(e) {
            e.preventDefault();
            if (!validateForm()) {
                toastr.error('Please fix the errors before submitting.');
                return;
            }

            let $btn = $('#saveBtn');
            $btn.prop('disabled', true).find('.spinner-border').removeClass('d-none');

            $.ajax({
                url: SAVE_URL,
                type: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    toastr.success(res.message);
                    setTimeout(() => window.location.href = res.redirect, 800);
                },
                error: function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const addrFields = ['state', 'city', 'area', 'pincode', 'address'];
                        $.each(xhr.responseJSON.errors, function(field, msgs) {
                            if (addrFields.includes(field) && $('#address_source').val() ===
                                'google') {
                                $('#placeError').text(msgs[0]);
                            } else {
                                setError(field, msgs[0]);
                            }
                        });
                        toastr.error(xhr.responseJSON.message || 'Please fix the errors.');
                    } else {
                        toastr.error('Something went wrong. Please try again.');
                    }
                    $btn.prop('disabled', false).find('.spinner-border').addClass('d-none');
                }
            });
        });
    </script>

    {{-- ===== Google Places (new API) ===== --}}
    <script>
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
        <script>
            window.googleFailed && window.googleFailed();
        </script>
    @endif
@endsection

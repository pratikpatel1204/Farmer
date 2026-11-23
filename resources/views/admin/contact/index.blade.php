@extends('admin.layout.main-layout')
@section('title', config('app.name') . ' || Contact Us')
@section('content')
    <div class="content">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Contact Us</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">Settings</li>
                        <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            {{-- ================= FORM ================= --}}
            <div class="col-xl-7">
                <div class="card">
                    <div class="card-body">
                        <div class="border-bottom mb-3 pb-3">
                            <h4>Contact Details</h4>
                        </div>

                        <form id="contactForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Call Us (Phone) <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" value="{{ $contact->phone }}" class="form-control"
                                        placeholder="+91 98765 43210">
                                    <span class="text-danger error-phone"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" value="{{ $contact->email }}" class="form-control"
                                        placeholder="support@kisanmandi.in">
                                    <span class="text-danger error-email"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">WhatsApp Number</label>
                                    <input type="text" name="whatsapp_number" value="{{ $contact->whatsapp_number }}"
                                        class="form-control" placeholder="919876543210">
                                    <small class="text-muted">Country code ke saath, jaise 919876543210</small><br>
                                    <span class="text-danger error-whatsapp_number"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">WhatsApp Label</label>
                                    <input type="text" name="whatsapp_message" value="{{ $contact->whatsapp_message }}"
                                        class="form-control" placeholder="Chat with our support team">
                                    <span class="text-danger error-whatsapp_message"></span>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Office Address <span class="text-danger">*</span></label>
                                    <textarea name="office_address" class="form-control" rows="2"
                                        placeholder="Agri Market Yard, Main Road, Indore, Madhya Pradesh 452001">{{ $contact->office_address }}</textarea>
                                    <span class="text-danger error-office_address"></span>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Working Hours <span class="text-danger">*</span></label>
                                    <input type="text" name="working_hours" value="{{ $contact->working_hours }}"
                                        class="form-control" placeholder="Mon - Sat, 9:00 AM - 7:00 PM">
                                    <span class="text-danger error-working_hours"></span>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Google Map (Embed)</label>
                                    <textarea name="map_embed_url" id="map_embed_url" class="form-control" rows="3"
                                        placeholder='<iframe src="https://www.google.com/maps/embed?pb=..."></iframe>'>{{ $contact->map_embed_url }}</textarea>
                                    <small class="text-muted">
                                        Google Maps &rarr; Share &rarr; Embed a map &rarr; "Copy HTML" karke yahan paste
                                        karein.
                                    </small><br>
                                    <span class="text-danger error-map_embed_url"></span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary" id="saveContactBtn">
                                    <span class="btn-text">Save</span>
                                    <span class="spinner-border spinner-border-sm d-none btn-spinner"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ================= PREVIEW ================= --}}
            <div class="col-xl-5">
                <div class="card">
                    <div class="card-body">
                        <div class="border-bottom mb-3 pb-3">
                            <h4>Preview</h4>
                        </div>

                        <ul class="list-unstyled mb-3">
                            <li class="d-flex align-items-start mb-3">
                                <span class="avatar avatar-md bg-primary-transparent rounded me-2"><i
                                        class="ti ti-phone fs-16"></i></span>
                                <div>
                                    <h6 class="mb-0">Call us</h6>
                                    <a href="{{ $contact->phone_link }}" id="pv-phone">{{ $contact->phone }}</a>
                                </div>
                            </li>
                            <li class="d-flex align-items-start mb-3">
                                <span class="avatar avatar-md bg-success-transparent rounded me-2"><i
                                        class="ti ti-brand-whatsapp fs-16"></i></span>
                                <div>
                                    <h6 class="mb-0">WhatsApp</h6>
                                    <a href="{{ $contact->whatsapp_link }}" target="_blank"
                                        id="pv-whatsapp">{{ $contact->whatsapp_message ?: $contact->whatsapp_number }}</a>
                                </div>
                            </li>
                            <li class="d-flex align-items-start mb-3">
                                <span class="avatar avatar-md bg-info-transparent rounded me-2"><i
                                        class="ti ti-mail fs-16"></i></span>
                                <div>
                                    <h6 class="mb-0">Email</h6>
                                    <a href="{{ $contact->email_link }}" id="pv-email">{{ $contact->email }}</a>
                                </div>
                            </li>
                            <li class="d-flex align-items-start mb-3">
                                <span class="avatar avatar-md bg-warning-transparent rounded me-2"><i
                                        class="ti ti-map-pin fs-16"></i></span>
                                <div>
                                    <h6 class="mb-0">Office</h6>
                                    <p class="mb-0" id="pv-address">{{ $contact->office_address }}</p>
                                </div>
                            </li>
                            <li class="d-flex align-items-start">
                                <span class="avatar avatar-md bg-secondary-transparent rounded me-2"><i
                                        class="ti ti-clock fs-16"></i></span>
                                <div>
                                    <h6 class="mb-0">Working hours</h6>
                                    <p class="mb-0" id="pv-hours">{{ $contact->working_hours }}</p>
                                </div>
                            </li>
                        </ul>

                        <div class="ratio ratio-4x3 rounded overflow-hidden border {{ $contact->map_embed_url ? '' : 'd-none' }}"
                            id="pv-map-wrap">
                            <iframe id="pv-map" src="{{ $contact->map_embed_url }}" style="border:0;" allowfullscreen
                                loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <p class="text-muted mb-0 {{ $contact->map_embed_url ? 'd-none' : '' }}" id="pv-map-empty">
                            Map abhi set nahi hai.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            const $form = $('#contactForm');

            // -------- Live preview --------
            function refreshPreview() {
                const phone = $form.find('[name=phone]').val() || '';
                const wa = ($form.find('[name=whatsapp_number]').val() || '').replace(/\D/g, '');
                const waLabel = $form.find('[name=whatsapp_message]').val() || wa;
                const email = $form.find('[name=email]').val() || '';

                $('#pv-phone').text(phone).attr('href', 'tel:' + phone.replace(/[^0-9+]/g, ''));
                $('#pv-whatsapp').text(waLabel).attr('href', wa ? 'https://wa.me/' + wa : '#');
                $('#pv-email').text(email).attr('href', 'mailto:' + email);
                $('#pv-address').text($form.find('[name=office_address]').val());
                $('#pv-hours').text($form.find('[name=working_hours]').val());
            }

            function setMap(url) {
                if (url) {
                    $('#pv-map').attr('src', url);
                    $('#pv-map-wrap').removeClass('d-none');
                    $('#pv-map-empty').addClass('d-none');
                } else {
                    $('#pv-map-wrap').addClass('d-none');
                    $('#pv-map-empty').removeClass('d-none');
                }
            }

            $form.on('input', 'input, textarea:not(#map_embed_url)', refreshPreview);

            $('#map_embed_url').on('change', function() {
                let val = $(this).val().trim();
                const match = val.match(/src=["']([^"']+)["']/i);
                if (match) val = match[1];
                setMap(val.startsWith('https://www.google.com/maps/embed') ? val : '');
            });

            // -------- Submit --------
            $form.on('submit', function(e) {
                e.preventDefault();
                $form.find(".text-danger[class*='error-']").text('');

                const $btn = $('#saveContactBtn');
                $btn.find('.btn-text').addClass('d-none');
                $btn.find('.btn-spinner').removeClass('d-none');
                $btn.prop('disabled', true);

                $.ajax({
                    url: "{{ route('admin.contact.update') }}",
                    type: "POST",
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.status === true) {
                            toastr.success(response.message);
                            $('#map_embed_url').val(response.map_url || '');
                            setMap(response.map_url);
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
            });
        });
    </script>
@endsection
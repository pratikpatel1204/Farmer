/* =========================================================
   KisanMandi: product enquiry form (modal) - AJAX, koi library nahi
   form[data-enquiry-form] -> POST action, JSON response:
     success: { success: true, message }
     error:   { message, errors: { field: [msg] } }   (422 / 401 / 404 / 429 / 500)
   ========================================================= */
(function () {
    'use strict';

    document.querySelectorAll('[data-enquiry-form]').forEach(function (form) {
        var modal = form.closest('.modal');
        var alertBox = form.querySelector('[data-enquiry-alert]');
        var btn = form.querySelector('[data-enquiry-submit]');
        var btnText = form.querySelector('[data-enquiry-text]');
        var loader = form.querySelector('[data-enquiry-loader]');
        var done = modal.querySelector('[data-enquiry-done]');
        var doneText = modal.querySelector('[data-enquiry-done-text]');
        var phone = form.querySelector('[name="phone"]');

        function fieldError(name, msg) {
            var input = form.querySelector('[name="' + name + '"]');
            var box = form.querySelector('[data-error-for="' + name + '"]');
            if (input) input.classList.toggle('is-invalid', !!msg);
            if (box) box.textContent = msg || '';
            return !msg;
        }

        function showAlert(msg) {
            alertBox.hidden = !msg;
            alertBox.querySelector('span').textContent = msg || '';
        }

        function setLoading(on) {
            btn.disabled = on;
            btnText.hidden = on;
            loader.hidden = !on;
        }

        function clearAll() {
            ['full_name', 'phone', 'email', 'message'].forEach(function (f) { fieldError(f, ''); });
            showAlert('');
        }

        phone.addEventListener('input', function () {
            phone.value = phone.value.replace(/\D/g, '').slice(0, 10);
        });

        form.querySelectorAll('.form-control').forEach(function (input) {
            input.addEventListener('input', function () { fieldError(input.name, ''); });
        });

        // Modal dobara khule to form wapas dikhe (agar pichli baar bhej diya tha)
        modal.addEventListener('show.bs.modal', function () {
            if (!done.hidden) return;
            clearAll();
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            clearAll();

            var data = new FormData(form);
            var ok = true;
            var name = String(data.get('full_name') || '').trim();
            var mobile = String(data.get('phone') || '').trim();
            var email = String(data.get('email') || '').trim();
            var message = String(data.get('message') || '').trim();

            if (!name) ok = fieldError('full_name', 'Please enter your name.') && ok;
            if (!/^[6-9][0-9]{9}$/.test(mobile)) ok = fieldError('phone', 'Please enter a valid 10-digit mobile number.') && ok;
            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) ok = fieldError('email', 'Please enter a valid email address.') && ok;
            if (message.length < 10) ok = fieldError('message', 'Message must be at least 10 characters.') && ok;
            if (!ok) return;

            setLoading(true);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': data.get('_token')
                },
                body: data,
                credentials: 'same-origin'
            }).then(function (res) {
                return res.json().catch(function () { return {}; }).then(function (json) {
                    return { status: res.status, json: json };
                });
            }).then(function (r) {
                setLoading(false);
                var json = r.json || {};

                if (r.status === 200 && json.success) {
                    form.hidden = true;
                    done.hidden = false;
                    if (json.message) doneText.textContent = json.message;
                    return;
                }

                if (r.status === 401 && json.loginUrl) {
                    window.location.href = json.loginUrl;
                    return;
                }

                if (json.errors) {
                    Object.keys(json.errors).forEach(function (key) {
                        if (form.querySelector('[data-error-for="' + key + '"]')) fieldError(key, json.errors[key][0]);
                        else showAlert(json.errors[key][0]);
                    });
                    return;
                }

                if (r.status === 419) {
                    showAlert('Your session expired. Please refresh the page and try again.');
                } else if (r.status === 429) {
                    showAlert('Too many attempts. Please wait a minute and try again.');
                } else {
                    showAlert(json.message || 'Could not send your enquiry. Please try again.');
                }
            }).catch(function () {
                setLoading(false);
                showAlert('Network error. Please check your internet and try again.');
            });
        });
    });
})();

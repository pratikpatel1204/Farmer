{{--
Shared JS (contact + product dono pages).
Params: $listRoute, $statusRoute, $currentPage
Filter inputs: #inqFilterForm ke andar har field par class "inq-filter" + name attribute
--}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        jQuery(function($) {
            const ROUTES = {
                list: @json($listRoute),
                status: @json($statusRoute)
            };
            const STATUS = {
                0: {
                    label: 'Pending',
                    variant: 'warning'
                },
                1: {
                    label: 'Contacted',
                    variant: 'success'
                },
                2: {
                    label: 'Closed',
                    variant: 'danger'
                }
            };

            const $body = $('#inqTableBody');
            const $pagination = $('#inqPagination');
            const $loader = $('#inqLoader');
            const $form = $('#inqFilterForm');

            let activeXhr = null;
            let searchTimer = null;
            let currentPage = {{ (int) $currentPage }};

            function notify(type, msg) {
                if (window.toastr) toastr[type](msg);
                else alert(msg);
            }

            /* ---------- Filters ---------- */
            function getFilters() {
                const params = {};
                $form.find('.inq-filter').each(function() {
                    const val = $.trim($(this).val() ?? '');
                    if (val !== '') params[this.name] = val;
                });
                return params;
            }

            function syncUrl(params) {
                const clean = Object.assign({}, params);
                if (String(clean.page) === '1') delete clean.page;
                const qs = $.param(clean);
                history.replaceState(null, '', ROUTES.list + (qs ? '?' + qs : ''));
            }

            function updateCounts(counts) {
                if (!counts) return;
                $.each(counts, (key, val) => $('[data-count="' + key + '"]').text(val));
            }

            function highlightActiveCard() {
                const status = $('#inqStatus').val();
                $('.inq-stat-card').removeClass('active').filter('[data-status="' + status + '"]')
                    .addClass('active');
            }

            /* ---------- AJAX load ---------- */
            function loadInquiries(page = 1) {
                currentPage = page;
                if (activeXhr) activeXhr.abort();

                const params = Object.assign(getFilters(), {
                    page: page
                });
                $loader.removeClass('d-none');

                const req = $.ajax({
                    url: ROUTES.list,
                    type: 'GET',
                    data: params,
                    dataType: 'json',
                    cache: false
                });
                activeXhr = req;

                req.done(function(res) {
                    $body.html(res.rows);
                    $pagination.html(res.pagination);
                    $('#inqResultBadge').text('Results : ' + res.total);
                    updateCounts(res.counts);
                    highlightActiveCard();
                    syncUrl(params);
                }).fail(function(xhr, textStatus) {
                    if (textStatus !== 'abort') notify('error', xhr.responseJSON?.message ||
                        'Failed to load inquiries.');
                }).always(function() {
                    if (activeXhr === req) {
                        $loader.addClass('d-none');
                        activeXhr = null;
                    }
                });
            }

            $('#inqSearch').on('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => loadInquiries(1), 400);
            });

            $form.on('change', 'select.inq-filter', () => loadInquiries(1));

            $('#inqDateFrom').on('change', function() {
                $('#inqDateTo').attr('min', this.value || null);
                loadInquiries(1);
            });
            $('#inqDateTo').on('change', function() {
                $('#inqDateFrom').attr('max', this.value || null);
                loadInquiries(1);
            });

            $('#inqResetBtn').on('click', function() {
                $form.find('.inq-filter').each(function() {
                    $(this).val(this.name === 'per_page' ? '10' : '');
                });
                $('#inqDateFrom, #inqDateTo').removeAttr('min max');
                loadInquiries(1);
            });

            $form.on('submit', e => e.preventDefault());

            // Stat card click => status filter
            $(document).on('click', '.inq-stat-card', function() {
                $('#inqStatus').val(String($(this).data('status') ?? '')).trigger('change');
            });

            // AJAX pagination
            $(document).on('click', '#inqPagination a.page-link', function(e) {
                e.preventDefault();
                const page = new URL(this.href).searchParams.get('page') || 1;
                loadInquiries(parseInt(page, 10));
                $('html, body').animate({
                    scrollTop: $('#inqTable').offset().top - 120
                }, 200);
            });

            /* ---------- Message modal ---------- */
            const modalEl = document.getElementById('inqMessageModal');
            let manualModal = false;

            function showModal() {
                if (window.bootstrap && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                } else if ($.fn.modal) {
                    $(modalEl).modal('show');
                } else {
                    // Bootstrap JS load nahi hai to khud se open
                    manualModal = true;
                    $(modalEl).addClass('show').css('display', 'block')
                        .attr({
                            'aria-modal': 'true',
                            role: 'dialog'
                        }).removeAttr('aria-hidden');
                    $('body').addClass('modal-open');
                    if (!$('#inqModalBackdrop').length) {
                        $('<div class="modal-backdrop fade show" id="inqModalBackdrop"></div>')
                            .appendTo('body');
                    }
                }
            }

            function hideManualModal() {
                if (!manualModal) return;
                manualModal = false;
                $(modalEl).removeClass('show').css('display', 'none').attr('aria-hidden', 'true')
                    .removeAttr('aria-modal');
                $('body').removeClass('modal-open');
                $('#inqModalBackdrop').remove();
            }

            $(modalEl).on('click', '[data-bs-dismiss="modal"]', hideManualModal);
            $(modalEl).on('click', function(e) {
                if (e.target === modalEl) hideManualModal();
            });
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') hideManualModal();
            });

            $(document).on('click', '.inq-view-btn', function() {
                let data = {};
                try {
                    data = JSON.parse(this.getAttribute('data-inquiry') || '{}');
                } catch (err) {
                    console.error('Invalid inquiry data', err);
                }
                const statusVal = String($(this).closest('tr').find('.inq-status-select')
            .val());
                const status = STATUS[statusVal] || STATUS[0];

                const $meta = $('#inqModalMeta').empty();
                (data.fields || []).forEach(function(f) {
                    const $value = $('<div class="value">');
                    if (f.value && f.type === 'email') {
                        $value.append($('<a>').attr('href', 'mailto:' + f.value).text(f
                            .value));
                    } else if (f.value && f.type === 'tel') {
                        $value.append($('<a>').attr('href', 'tel:' + f.value).text(f
                            .value));
                    } else {
                        $value.text(f.value || '—');
                    }
                    $meta.append($('<div>').append($('<div class="label">').text(f
                        .label), $value));
                });

                // Status (latest dropdown value)
                $meta.append(
                    $('<div>').append(
                        $('<div class="label">').text('Status'),
                        $('<div class="value">').append(
                            $('<span class="badge">').addClass('badge-soft-' + status
                                .variant).text(status.label)
                        )
                    )
                );

                // .text() => message ka HTML execute nahi hoga (XSS safe)
                $('#inqModalMessage').text(data.message || '');
                showModal();
            });

            $('#inqModalCopy').on('click', function() {
                const text = $('#inqModalMessage').text();
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(() => notify('success',
                        'Message copied.'));
                } else {
                    const $tmp = $('<textarea>').val(text).appendTo('body').select();
                    document.execCommand('copy');
                    $tmp.remove();
                    notify('success', 'Message copied.');
                }
            });

            /* ---------- Status update ---------- */
            $(document).on('change', '.inq-status-select', function() {
                const $select = $(this);
                const oldStatus = String($select.data('current'));
                const newStatus = String($select.val());

                $select.prop('disabled', true);

                $.ajax({
                    url: ROUTES.status,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: $select.data('id'),
                        status: newStatus
                    }
                }).done(function(res) {
                    if (!res.success) {
                        $select.val(oldStatus);
                        notify('error', res.message || 'Failed to update status.');
                        return;
                    }
                    notify('success', res.message);
                    $select.data('current', newStatus)
                        .removeClass('status-warning status-success status-danger')
                        .addClass('status-' + STATUS[newStatus].variant);
                    updateCounts(res.counts);

                    const filterStatus = $('#inqStatus').val();
                    if (filterStatus !== '' && filterStatus !== newStatus)
                        loadInquiries(currentPage);
                }).fail(function(xhr) {
                    $select.val(oldStatus);
                    notify('error', xhr.responseJSON?.message ||
                        'Something went wrong. Please try again.');
                }).always(function() {
                    $select.prop('disabled', false);
                });
            });
        });
    });
</script>

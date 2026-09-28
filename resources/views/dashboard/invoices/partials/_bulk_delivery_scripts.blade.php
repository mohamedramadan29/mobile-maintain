{{--
    سكربت التسليم الجماعي والتحديث السريع.
    يُستدعى داخل @section('js') بعد تحميل jQuery.
    الماركب في partials/_bulk_delivery_panel (سيكشن أعلى الجدول).
--}}
<script>
    (function() {
        'use strict';

        var QD = {
            token: '{{ csrf_token() }}',
            urls: {
                check: "{{ route('dashboard.invoices.bulk.delivery.check') }}",
                quickUpdate: "{{ route('dashboard.invoices.bulk.delivery.quick_update') }}",
                delivery: "{{ route('dashboard.invoices.bulk.delivery') }}"
            },
            readyIds: [],
            maxUploadKb: 20480,
            busy: false
        };

        function selectedIds() {
            var ids = [];
            $('input[name="invoice_select"]:checked').each(function() {
                ids.push($(this).val());
            });
            return ids;
        }

        function showError(message) {
            $('#qd_error').text(message).show();
        }

        function escapeHtml(value) {
            return $('<div>').text(value === null || value === undefined ? '' : value).html();
        }

        // بناء صف جهاز ناقص داخل النافذة
        function buildRow(device) {
            var missing = [];
            if (!device.has_status) missing.push('حالة الإصلاح');
            if (!device.has_media) missing.push('صورة/فيديو');

            return '' +
                '<div class="card mb-1 qd-row" data-id="' + device.id + '"' +
                ' data-has-status="' + (device.has_status ? 1 : 0) + '"' +
                ' data-has-media="' + (device.has_media ? 1 : 0) + '"' +
                ' data-confirmed="0">' +
                '<div class="card-body p-1">' +
                '<div class="d-flex justify-content-between flex-wrap mb-1">' +
                '<h5 class="mb-0"><i class="la la-mobile-phone"></i> ' + escapeHtml(device.device || 'بدون اسم') + '</h5>' +
                '<span class="badge badge-dark">رقم الطلب: ' + device.id + '</span>' +
                '</div>' +
                '<div class="text-muted mb-1">العميل: ' + escapeHtml(device.name) + ' — ' + escapeHtml(device.phone) +
                '<span class="badge badge-warning ml-1">الناقص: ' + missing.join(' و ') + '</span></div>' +
                '<div class="row">' +
                '<div class="col-md-6 mb-1">' +
                '<label class="font-weight-bold d-block">حالة الجهاز <span class="danger">*</span></label>' +
                '<div class="btn-group btn-group-sm qd-status-group" role="group">' +
                '<button type="button" class="btn btn-outline-success qd-status' +
                (device.status === 'تم الاصلاح' ? ' active btn-success' : '') +
                '" data-status="تم الاصلاح">تم الإصلاح</button>' +
                '<button type="button" class="btn btn-outline-danger qd-status' +
                (device.status === 'لم يتم الاصلاح' ? ' active btn-danger' : '') +
                '" data-status="لم يتم الاصلاح">لم يتم الإصلاح</button>' +
                '</div>' +
                '</div>' +
                '<div class="col-md-6 mb-1">' +
                '<label class="font-weight-bold d-block">صورة أو فيديو للجهاز' +
                (device.has_media ? ' <span class="badge badge-success">مرفقة مسبقاً</span>' : ' <small class="text-muted">(اختيارية مع التأكيد)</small>') +
                '</label>' +
                '<input type="file" class="form-control-file qd-file" accept="image/*,video/*">' +
                '<small class="qd-file-name text-muted"></small>' +
                '</div>' +
                '</div>' +
                '<div class="qd-msg"></div>' +
                '</div>' +
                '</div>';
        }

        // الخطوة 1: فحص الأجهزة المحددة
        window.submitBulkDelivery = function() {
            var ids = selectedIds();

            $('#alert_no_invoices').hide();

            if (ids.length === 0) {
                $('#alert_no_invoices').text('من فضلك اختر الأجهزة المراد تسليمها.').show();
                return;
            }

            $.ajax({
                url: QD.urls.check,
                method: 'POST',
                data: {
                    invoice_ids: ids.join(','),
                    _token: QD.token
                },
                success: function(response) {
                    if (!response.success) {
                        alert(response.message || 'تعذّر فحص الأجهزة المحددة');
                        return;
                    }

                    QD.readyIds = response.ready || [];
                    QD.maxUploadKb = response.max_upload_kb || QD.maxUploadKb;

                    var incomplete = response.incomplete || [];
                    var unavailable = response.unavailable || [];

                    // كل الأجهزة مكتملة: تسليم مباشر بدون نافذة
                    if (incomplete.length === 0) {
                        if (QD.readyIds.length === 0) {
                            alert('لا توجد أجهزة صالحة للتسليم من بين ما حددته');
                            return;
                        }
                        if (!confirm('هل أنت متأكد من تسليم ' + QD.readyIds.length + ' جهاز؟')) return;
                        finishDelivery(QD.readyIds, []);
                        return;
                    }

                    openPanel(incomplete, unavailable);
                },
                error: function(xhr) {
                    alert((xhr.responseJSON && xhr.responseJSON.message) || 'حدث خطأ أثناء فحص الأجهزة');
                }
            });
        };

        function hidePanel() {
            $('#quickDeliveryPanel').hide();
        }

        // الخطوة 2: عرض الأجهزة الناقصة في سيكشن واحد أعلى الجدول
        function openPanel(incomplete, unavailable) {
            var summary = 'الأجهزة المكتملة الجاهزة للتسليم: <strong>' + QD.readyIds.length + '</strong>' +
                ' — الأجهزة الناقصة التي تحتاج تحديثاً: <strong>' + incomplete.length + '</strong>';

            if (unavailable.length) {
                summary += '<br><span class="danger">أجهزة مُسلَّمة مسبقاً أو غير موجودة وتم تجاهلها: ' +
                    unavailable.join(', ') + '</span>';
            }

            $('#qd_summary').html(summary);
            $('#qd_error').hide().text('');
            $('#qd_progress_wrap').hide();
            $('#qd_progress').css('width', '0%').text('0%');

            var html = '';
            for (var i = 0; i < incomplete.length; i++) {
                html += buildRow(incomplete[i]);
            }
            $('#qd_rows').html(html);

            $('#qd_save').prop('disabled', false);

            var panel = $('#quickDeliveryPanel');
            panel.show();

            // التمرير إلى السيكشن حتى يراه الموظف فوراً
            if (panel[0] && panel[0].scrollIntoView) {
                panel[0].scrollIntoView({ block: 'start' });
            } else {
                $('html, body').animate({ scrollTop: panel.offset().top - 20 }, 200);
            }
        }

        // إغلاق السيكشن يدوياً
        $(document).on('click', '.qd-close', function() {
            if (QD.busy) return;
            hidePanel();
        });

        // تغيير التحديد بعد الفحص يجعل محتوى السيكشن قديماً، فنغلقه
        $(document).on('change', 'input[name="invoice_select"], #select_all', function() {
            if (!QD.busy) hidePanel();
        });

        // اختيار حالة الإصلاح
        $(document).on('click', '.qd-status', function() {
            var group = $(this).closest('.qd-status-group');
            group.find('.qd-status').removeClass('active btn-success btn-danger');
            $(this).addClass('active').addClass($(this).data('status') === 'تم الاصلاح' ? 'btn-success' : 'btn-danger');
            $(this).closest('.qd-row').find('.qd-msg').empty();
        });

        // اختيار مرفق + التحقق من الحجم قبل الرفع
        $(document).on('change', '.qd-file', function() {
            var row = $(this).closest('.qd-row');
            var file = this.files && this.files[0];

            row.find('.qd-msg').empty();

            if (!file) {
                row.find('.qd-file-name').text('');
                return;
            }

            if (file.size / 1024 > QD.maxUploadKb) {
                row.find('.qd-file-name').html('<span class="danger">حجم الملف ' +
                    (file.size / 1048576).toFixed(1) + ' ميجابايت ويتجاوز الحد المسموح ' +
                    Math.round(QD.maxUploadKb / 1024) + ' ميجابايت</span>');
                $(this).val('');
                return;
            }

            row.find('.qd-file-name').text(file.name);
            // إرفاق صورة يُلغي حاجة التأكيد بدون صورة
            row.attr('data-confirmed', '0');
        });

        // تأكيد التسليم بدون صورة لجهاز معين
        $(document).on('click', '.qd-confirm-no-image', function() {
            var row = $(this).closest('.qd-row');
            row.attr('data-confirmed', '1');
            row.find('.qd-msg').html('<div class="alert alert-warning mb-0 py-1">' +
                'تم تأكيد التسليم بدون صورة لهذا الجهاز</div>');
        });

        // الخطوة 3: الحفظ وإتمام التسليم
        $(document).on('click', '#qd_save', function() {
            if (QD.busy) return;

            $('#qd_error').hide().text('');

            var rows = $('#qd_rows .qd-row');
            var missingStatus = [];
            var needConfirm = [];
            var tasks = [];
            var confirmedWithoutImage = [];

            rows.each(function() {
                var row = $(this);
                var id = row.data('id');
                var hasStatus = row.attr('data-has-status') === '1';
                var hasMedia = row.attr('data-has-media') === '1';
                var chosenStatus = row.find('.qd-status.active').data('status') || null;
                var fileInput = row.find('.qd-file')[0];
                var file = fileInput && fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;

                // حالة الإصلاح شرط أساسي
                if (!hasStatus && !chosenStatus) {
                    missingStatus.push(id);
                    row.find('.qd-msg').html('<div class="alert alert-danger mb-0 py-1">' +
                        'من فضلك حدد حالة الجهاز (تم الإصلاح / لم يتم الإصلاح)</div>');
                    return;
                }

                // الصورة اختيارية، لكن تحتاج تأكيداً صريحاً
                if (!hasMedia && !file && row.attr('data-confirmed') !== '1') {
                    needConfirm.push(id);
                    row.find('.qd-msg').html('<div class="alert alert-warning mb-0 py-1">' +
                        'هل أنت متأكد من التسليم بدون إرفاق صورة للجهاز (' + id + ')؟ ' +
                        '<button type="button" class="btn btn-sm btn-warning qd-confirm-no-image">نعم، تأكيد</button>' +
                        '</div>');
                    return;
                }

                if (!hasMedia && !file) {
                    confirmedWithoutImage.push(id);
                }

                if (chosenStatus || file) {
                    tasks.push({
                        id: id,
                        status: chosenStatus,
                        file: file
                    });
                }
            });

            if (missingStatus.length) {
                showError('حالة الجهاز إلزامية. الأجهزة التالية بحاجة لتحديد الحالة: ' + missingStatus.join(', '));
                return;
            }

            if (needConfirm.length) {
                showError('الأجهزة التالية بدون صورة، من فضلك أكّد التسليم بدونها أو أرفق صورة: ' +
                    needConfirm.join(', '));
                return;
            }

            var allIds = QD.readyIds.slice();
            rows.each(function() {
                allIds.push($(this).data('id'));
            });

            QD.busy = true;
            $('#qd_save').prop('disabled', true);
            $('#qd_progress_wrap').show();

            // رفع المرفقات وحفظ الحالات جهازاً بعد جهاز (احتراماً لحدود الرفع في PHP)
            runTasks(tasks, 0, function(failed) {
                if (failed.length) {
                    QD.busy = false;
                    $('#qd_save').prop('disabled', false);
                    showError('تعذّر حفظ بيانات الأجهزة: ' + failed.join(', ') + '. لم يتم التسليم.');
                    return;
                }
                finishDelivery(allIds, confirmedWithoutImage);
            });
        });

        function setProgress(done, total) {
            var percent = total === 0 ? 100 : Math.round((done / total) * 100);
            $('#qd_progress').css('width', percent + '%').text(percent + '%');
        }

        function runTasks(tasks, index, done) {
            if (!runTasks.failed || index === 0) runTasks.failed = [];

            setProgress(index, tasks.length);

            if (index >= tasks.length) {
                done(runTasks.failed);
                return;
            }

            var task = tasks[index];
            var data = new FormData();
            data.append('_token', QD.token);
            data.append('invoice_id', task.id);
            if (task.status) data.append('status', task.status);
            if (task.file) data.append('file', task.file);

            $.ajax({
                url: QD.urls.quickUpdate,
                method: 'POST',
                data: data,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (!response.success) runTasks.failed.push(task.id);
                    runTasks(tasks, index + 1, done);
                },
                error: function() {
                    runTasks.failed.push(task.id);
                    runTasks(tasks, index + 1, done);
                }
            });
        }

        // الخطوة 4: تسليم كل الأجهزة دفعة واحدة
        function finishDelivery(ids, confirmedWithoutImage) {
            setProgress(1, 1);

            $.ajax({
                url: QD.urls.delivery,
                method: 'POST',
                data: {
                    invoice_ids: ids.join(','),
                    confirm_without_image: confirmedWithoutImage,
                    _token: QD.token
                },
                success: function(response) {
                    QD.busy = false;

                    var message = response.message || '';

                    if (response.rejected && response.rejected.length) {
                        message += '\n\nأجهزة لم تُسلَّم:';
                        for (var i = 0; i < response.rejected.length; i++) {
                            message += '\n- ' + response.rejected[i].id + ': ' + response.rejected[i].reason;
                        }
                    }

                    if (!response.success) {
                        $('#qd_save').prop('disabled', false);
                        alert(message || 'حدث خطأ أثناء تسليم الأجهزة');
                        return;
                    }

                    alert(message);
                    hidePanel();
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                },
                error: function(xhr) {
                    QD.busy = false;
                    $('#qd_save').prop('disabled', false);
                    alert((xhr.responseJSON && xhr.responseJSON.message) || 'حدث خطأ أثناء تسليم الأجهزة');
                }
            });
        }
    })();
</script>

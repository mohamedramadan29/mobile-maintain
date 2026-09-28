{{--
    سيكشن التحديث السريع قبل التسليم الجماعي.
    يظهر أعلى الجدول مباشرة بدل النافذة المنبثقة (modal)،
    حتى لا يعتمد على جافاسكربت بوتستراب الذي قد لا يعمل على بعض الأجهزة.
    السكربت الخاص به في partials/_bulk_delivery_scripts
--}}
<div id="quickDeliveryPanel" class="card border-primary mb-2" style="display: none;" dir="rtl">
    <div class="card-header bg-primary white p-1">
        <h4 class="card-title white mb-0 d-inline-block">التحديث السريع قبل التسليم</h4>
        <button type="button" class="btn btn-sm btn-light float-right qd-close">إغلاق</button>
    </div>
    <div class="card-body p-1">
        <div class="alert alert-info mb-1" id="qd_summary"></div>
        <div class="alert alert-danger mb-1" id="qd_error" style="display: none;"></div>

        <div id="qd_rows"></div>

        <div class="progress mb-1" id="qd_progress_wrap" style="display: none; height: 18px;">
            <div class="progress-bar progress-bar-striped bg-success" id="qd_progress" role="progressbar"
                style="width: 0%;">0%</div>
        </div>

        <button type="button" class="btn btn-success" id="qd_save">
            <i class="la la-check"></i> حفظ وإتمام التسليم
        </button>
        <button type="button" class="btn btn-secondary qd-close">إلغاء</button>
    </div>
</div>

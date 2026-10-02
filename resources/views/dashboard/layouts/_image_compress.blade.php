{{--
    ضغط الصور قبل الرفع.
    يعمل على أي حقل: <input type="file" data-compress>
    يصغّر الصورة إلى 1600px بجودة 0.8 ويحوّلها JPEG، ويمنع إرسال النموذج
    قبل اكتمال الضغط حتى لا يُرفع الملف الأصلي بالخطأ.
--}}
<script>
(function () {
    var MAX_SIDE = 1600, QUALITY = 0.8;
    // عدّاد لكل نموذج على حدة، حتى لا نعطّل نماذج أخرى في الصفحة
    var pendingByForm = new Map();
    var queuedSubmit = null;

    function bump(form, delta) {
        if (!form) return;
        var n = (pendingByForm.get(form) || 0) + delta;
        if (n > 0) pendingByForm.set(form, n); else pendingByForm.delete(form);
    }

    function compress(file) {
        return new Promise(function (resolve) {
            if (!file.type || !file.type.startsWith('image/')) return resolve(file);

            var reader = new FileReader();
            reader.onerror = function () { resolve(file); };
            reader.onload = function (e) {
                var img = new Image();
                img.onerror = function () { resolve(file); };
                img.onload = function () {
                    try {
                        var w = img.width, h = img.height;
                        if (w > h) { if (w > MAX_SIDE) { h *= MAX_SIDE / w; w = MAX_SIDE; } }
                        else { if (h > MAX_SIDE) { w *= MAX_SIDE / h; h = MAX_SIDE; } }

                        var canvas = document.createElement('canvas');
                        canvas.width = w; canvas.height = h;
                        canvas.getContext('2d').drawImage(img, 0, 0, w, h);

                        canvas.toBlob(function (blob) {
                            // لو فشل الضغط أو صار أكبر من الأصل، نبقي الأصل
                            if (!blob || blob.size >= file.size) return resolve(file);
                            resolve(new File([blob], file.name.replace(/\.[^.]+$/, '') + '.jpg',
                                { type: 'image/jpeg', lastModified: Date.now() }));
                        }, 'image/jpeg', QUALITY);
                    } catch (err) { resolve(file); }
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    function hint(input, text) {
        var el = input.parentNode.querySelector('.compress-hint');
        if (!el) {
            el = document.createElement('small');
            el.className = 'compress-hint d-block mt-1 text-muted';
            input.parentNode.appendChild(el);
        }
        el.textContent = text || '';
    }

    document.addEventListener('change', function (ev) {
        var input = ev.target;
        if (!input.matches || !input.matches('input[type="file"][data-compress]')) return;
        if (!input.files || !input.files.length) return;
        if (typeof DataTransfer === 'undefined') return; // متصفح قديم: نرفع الأصل

        var originals = Array.prototype.slice.call(input.files);
        var originalKB = originals.reduce(function (s, f) { return s + f.size; }, 0) / 1024;

        var ownerForm = input.form;
        bump(ownerForm, 1);
        hint(input, 'جاري تجهيز الصورة...');

        Promise.all(originals.map(compress)).then(function (files) {
            try {
                var dt = new DataTransfer();
                files.forEach(function (f) { dt.items.add(f); });
                input.files = dt.files;
                var newKB = files.reduce(function (s, f) { return s + f.size; }, 0) / 1024;
                hint(input, 'تم تجهيز الصورة: ' + Math.round(originalKB) + ' ك.ب ← ' + Math.round(newKB) + ' ك.ب');
            } catch (err) { hint(input, ''); }
        }).catch(function () { hint(input, ''); })
          .then(function () {
              bump(ownerForm, -1);
              if (queuedSubmit && !pendingByForm.get(queuedSubmit)) {
                  var form = queuedSubmit; queuedSubmit = null;
                  // requestSubmit يعيد إطلاق حدث submit فيعمل معالج الصفحة كالمعتاد
                  if (form.requestSubmit) form.requestSubmit(); else form.submit();
              }
          });
    }, true);

    // لا نسمح بالإرسال قبل اكتمال الضغط.
    // stopImmediatePropagation ضروري: بعض النماذج تستدعي form.submit() داخل
    // معالج submit الخاص بها، وهو استدعاء أصلي لا يُطلق الحدث فيتجاوز preventDefault.
    document.addEventListener('submit', function (ev) {
        if (pendingByForm.get(ev.target)) {
            ev.preventDefault();
            ev.stopImmediatePropagation();
            queuedSubmit = ev.target;
        }
    }, true);
})();
</script>

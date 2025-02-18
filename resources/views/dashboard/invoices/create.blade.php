@extends('dashboard.layouts.app')
@section('title', 'ا ضافة فاتورة جديدة ')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/') }}/vendors/css/forms/icheck/icheck.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/') }}/vendors/css/forms/icheck/custom.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/') }}/css-rtl/plugins/forms/checkboxes-radios.css">
@endsection
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="mb-2 content-header-left col-md-6 col-12 breadcrumb-new">
                    <h3 class="mb-0 content-header-title d-inline-block"> اضافة فاتورة جديدة </h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.invoices.index') }}"> الوافتير </a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#"> اضافة فاتورة جديدة </a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>

            </div>
            <div class="content-body">
                <!-- Basic form layout section start -->
                <section id="basic-form-layouts">
                    <div class="row match-height">
                        <div class="col-md-12">
                            <div class="card">
                                @if ($errors->any())
                                    @foreach ($errors->all() as $error)
                                        <div class="alert alert-danger">{{ $error }}</div>
                                    @endforeach
                                @endif
                                <div class="card-header">
                                    <h4 class="card-title" id="basic-layout-form"> اضافة فاتورة جديدة </h4>
                                    <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i> </a>
                                </div>
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <form class="form" method="POST" id="invoice-form"
                                            action="{{ route('dashboard.invoices.create') }}" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-body">
                                                <!--################### Start Add ChecksResults ###################-->
                                                <div class="row">
                                                    <h5> فحص الجهاز <span class="required_span"> * </span> </h5>
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th> # </th>
                                                                <th> اساسيات الفحص </th>
                                                                <th> يعمل </th>
                                                                <th> لا يعمل </th>
                                                                <th> ملاحظات </th>
                                                                <th> بعد الفحص </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($checks as $check)
                                                                <tr>
                                                                    <td> {{ $loop->iteration }}</td>
                                                                    <td>
                                                                        <input type="hidden" name="problem_id[]"
                                                                            value="{{ $check->id }}">
                                                                        <input readonly type="text"
                                                                            value="{{ $check->name }}" class="form-control"
                                                                            name="check_problem_name[]">
                                                                    </td>
                                                                    <td>
                                                                        <input required type="radio" value="1"
                                                                            class="form-control"
                                                                            name="work_{{ $check->id }}[]"
                                                                            {{ old('work_' . $check->id) == '1' ? 'checked' : '' }}>
                                                                    </td>
                                                                    <td>
                                                                        <input required type="radio" value="0"
                                                                            class="form-control"
                                                                            name="work_{{ $check->id }}[]"
                                                                            {{ old('work_' . $check->id) == '0' ? 'checked' : '' }}>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text"
                                                                            value="{{ old('notes.' . $loop->index) }}"
                                                                            class="form-control" name="notes[]">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text"
                                                                            value="{{ old('after_check.' . $loop->index) }}"
                                                                            class="form-control" name="after_check[]">
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--################### End Add ChecksResults #####################-->
                                                <!-- باقي الحقول -->
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="name"> اسم العميل <span class="required_span"> *
                                                                </span> </label>
                                                            <input required type="text" id="name"
                                                                class="form-control" placeholder="" name="name"
                                                                value="{{ old('name') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="phone"> رقم الهاتف <span class="required_span">
                                                                    *</span> </label>
                                                            <input required type="text" id="phone"
                                                                class="form-control" placeholder="مثال: 0500000000"
                                                                name="phone" value="{{ old('phone') }}" maxlength="10"
                                                                oninput="validatePhoneNumber(this)">
                                                            <small id="phone-error" class="text-danger"
                                                                style="display: none;">يجب أن يكون الرقم مكونًا من 10 أرقام
                                                                ويبدأ بـ 0</small>
                                                        </div>

                                                        <script>
                                                            function validatePhoneNumber(input) {
                                                                let phone = input.value;
                                                                let errorMsg = document.getElementById("phone-error");

                                                                // السماح فقط بالأرقام
                                                                input.value = input.value.replace(/\D/g, '');

                                                                // التأكد من أن الرقم يبدأ بـ 0
                                                                if (input.value.length > 0 && input.value.charAt(0) !== '0') {
                                                                    input.value = '0';
                                                                }

                                                                // منع تجاوز 10 أرقام
                                                                if (input.value.length > 10) {
                                                                    input.value = input.value.slice(0, 10);
                                                                }

                                                                // إظهار رسالة الخطأ إن لم يكن الرقم صحيحًا
                                                                if (!/^0\d{9}$/.test(input.value)) {
                                                                    errorMsg.style.display = "block";
                                                                } else {
                                                                    errorMsg.style.display = "none";
                                                                }
                                                            }
                                                        </script>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="title"> اسم الجهاز <span class="required_span"> *
                                                                </span> </label>
                                                            <input required type="text" id="title"
                                                                class="form-control" placeholder="" name="title"
                                                                value="{{ old('title') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="title"> حدد الاعطال <span
                                                                    class="required_span"> * </span> </label>
                                                            <div class="skin skin-square">
                                                                <div
                                                                    class="col-md-12 col-sm-12 d-flex justify-content-around">
                                                                    @foreach ($problems as $problem)
                                                                        <fieldset>
                                                                            <input type="checkbox"
                                                                                id="input-{{ $problem->id }}"
                                                                                name="problems[]"
                                                                                value="{{ $problem->name }}">
                                                                            <label
                                                                                for="input-{{ $problem->id }}">{{ $problem->name }}
                                                                            </label>
                                                                        </fieldset>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="title"> ملاحظات </label>
                                                            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="price"> السعر الاولي <span
                                                                    class="required_span"> * </span> </label>
                                                            <input required type="number" step="0.01" id="price"
                                                                class="form-control" placeholder="" name="price"
                                                                value="{{ old('price') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="price"> تاريخ ووقت التسليم <span
                                                                class="required_span"> * </span> </label>
                                                        <div class="justify-between d-flex">
                                                            <div class="form-group">
                                                                <div class="position-relative has-icon-left">
                                                                    <input required type="date" name="date_delivery"
                                                                        id="timesheetinput3" class="form-control"
                                                                        value="{{ old('date_delivery') }}">
                                                                    <div class="form-control-position">
                                                                        <i class="ft-message-square"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="position-relative has-icon-left">
                                                                    <input required type="time" name="time_delivery"
                                                                        id="timesheetinput6" class="form-control"
                                                                        value="{{ old('time_delivery') }}">
                                                                    <div class="form-control-position">
                                                                        <i class="ft-clock"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="price"> الحالة <span class="required_span"> *
                                                                </span> </label>
                                                            <select required name="status" id=""
                                                                class="form-control">
                                                                <option value="رف الاستلام"
                                                                    {{ old('status') == 'رف الاستلام' ? 'selected' : '' }}>
                                                                    رف الاستلام</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- اضافة المرفقات -->
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label for="address"> اضافة مرفقات <span
                                                                    class="required_span"> * </span> </label>
                                                            <input required type="file" name="files_images[]"
                                                                class="form-control" multiple id="imageInput">
                                                        </div>
                                                        <div id="imagePreview" class="flex-wrap mt-3 d-flex"></div>
                                                    </div>
                                                </div>

                                                <script>
                                                    let imageInput = document.getElementById('imageInput');
                                                    let imagePreview = document.getElementById('imagePreview');
                                                    let dt = new DataTransfer(); // لتخزين الملفات المرفوعة

                                                    imageInput.addEventListener('change', function(event) {
                                                        Array.from(event.target.files).forEach(file => {
                                                            let reader = new FileReader();
                                                            reader.onload = function(e) {
                                                                let imgContainer = document.createElement("div");
                                                                imgContainer.classList.add("position-relative", "m-2");

                                                                let img = document.createElement("img");
                                                                img.src = e.target.result;
                                                                img.classList.add("rounded", "shadow", "border", "p-1");
                                                                img.style.width = "120px";
                                                                img.style.height = "120px";

                                                                let removeBtn = document.createElement("span");
                                                                removeBtn.innerHTML = "&times;";
                                                                removeBtn.classList.add("position-absolute", "remove-button", "top-0", "end-0",
                                                                    "bg-danger",
                                                                    "text-white", "rounded-circle", "p-1");
                                                                removeBtn.style.cursor = "pointer";

                                                                removeBtn.onclick = function() {
                                                                    let index = Array.from(dt.files).findIndex(f => f.name === file.name);
                                                                    if (index > -1) {
                                                                        dt.items.remove(index);
                                                                        imageInput.files = dt.files;
                                                                    }
                                                                    imgContainer.remove();
                                                                };

                                                                imgContainer.appendChild(img);
                                                                imgContainer.appendChild(removeBtn);
                                                                imagePreview.appendChild(imgContainer);

                                                                dt.items.add(file);
                                                                imageInput.files = dt.files; // تحديث الملفات داخل input
                                                            };
                                                            reader.readAsDataURL(file);
                                                        });
                                                    });
                                                </script>


                                                <!-- عنصر التوقيع -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>توقيع العميل <span class="required_span"> * </span> </label>
                                                        <div id="signature-pad" class="signature-pad">
                                                            <div class="signature-pad-body">
                                                                <canvas></canvas>
                                                            </div>
                                                            <div class="signature-pad-footer">
                                                                <button type="button" id="clear-signature"
                                                                    class="mt-2 btn btn-danger">مسح التوقيع</button>
                                                            </div>
                                                        </div>
                                                        <input required type="hidden" name="signature" id="signature"
                                                            value="{{ old('signature') }}">
                                                    </div>
                                                </div>

                                                <!-- الموافقة علي الشروط -->
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-check">
                                                            <input required class="form-check-input" type="checkbox"
                                                                value="1" id="flexCheckDefault"
                                                                {{ old('flexCheckDefault') ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="flexCheckDefault">
                                                                الموافقة علي الشروط والاحكام
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-actions">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="la la-check-square-o"></i> حفظ
                                                </button>
                                                <button type="button" class="mr-1 btn btn-warning">
                                                    <i class="ft-x"></i> رجوع
                                                </button>
                                            </div>
                                        </form>


                                        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
                                            integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
                                            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
                                        <!--------------- Start SignaturePad ############ -->
                                        <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.3.4/signature_pad.min.js"
                                            integrity="sha512-Mtr2f9aMp/TVEdDWcRlcREy9NfgsvXvApdxrm3/gK8lAMWnXrFsYaoW01B5eJhrUpBT7hmIjLeaQe0hnL7Oh1w=="
                                            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
                                        <script>
                                            var canvas = document.querySelector("canvas");
                                            var signaturePad = new SignaturePad(canvas);
                                            // مسح التوقيع عند الضغط على الزر
                                            document.getElementById("clear-signature").addEventListener("click", function() {
                                                signaturePad.clear();
                                            });
                                            document.getElementById("invoice-form").addEventListener("submit", function(e) {
                                                var signatureInput = document.getElementById("signature");
                                                if (signaturePad.isEmpty()) {
                                                    e.preventDefault();
                                                    alert("الرجاء التوقيع");
                                                } else {
                                                    signatureInput.value = signaturePad.toDataURL(); // تأكد من أن التوقيع يتم تخزينه هنا
                                                }
                                            });
                                        </script>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>
                <!-- // Basic form layout section end -->
            </div>
        </div>
    </div>
    <style>
        .remove-button {
            cursor: pointer;
            width: 33px;
            height: 33px;
            line-height: 9px;
            text-align: center;
            left: -15px;
            top: -12px;
        }
    </style>
@endsection
@section('js')
    <script src="{{ asset('assets/admin/') }}/vendors/js/forms/icheck/icheck.min.js" type="text/javascript"></script>
    <script src="{{ asset('assets/admin/') }}/js/scripts/forms/checkbox-radio.js" type="text/javascript"></script>

@endsection

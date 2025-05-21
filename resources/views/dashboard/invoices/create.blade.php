@extends('dashboard.layouts.app')
@section('title', 'ا ضافة فاتورة جديدة ')
@section('css')
    <style>
        .problem_check_box {
            display: flex;
            justify-content: space-around;
        }

        .table th,
        .table td {
            padding: 0;
        }

        .table td input {
            min-width: 150px;
        }

        .table td select {
            min-width: 120px;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/') }}/vendors/css/forms/icheck/icheck.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/') }}/vendors/css/forms/icheck/custom.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/') }}/css-rtl/plugins/forms/checkboxes-radios.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/parsleyjs/src/parsley.css">
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
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.invoices.index') }}"> الفواتير </a>
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
                                @if(session()->has('Success_message'))
                                <div style="margin: auto;margin-top: 20px; text-align: center;">
                                    <p style="margin-bottom: 10px; color: green;">تم اضافة الفاتورة بنجاح</p>
                                    <a href="{{ route('dashboard.invoices.index') }}" class="btn btn-primary btn-sm">
                                        <i class="la la-list"></i> جميع الفواتير
                                    </a>
                                    <a href="{{ route('dashboard.invoices.print_barcode', session('new_invoice_id')) }}" target="_blank" class="btn btn-info btn-sm">
                                        <i class="la la-print"></i> طباعة الباركود
                                    </a>
                                </div>
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
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for=""> حدد نوع الفحص </label>
                                                            <select required name="checkout_type" id="checkout_type"
                                                                class="form-control">
                                                                <option value="" selected disabled> حدد نوع الفحص
                                                                </option>
                                                                <option
                                                                    {{ old('checkout_type') == 'فحص كامل' ? 'selected' : '' }}
                                                                    value="فحص كامل"> فحص كامل </option>
                                                                <option
                                                                    {{ old('checkout_type') == 'فحص جهاز برمجة' ? 'selected' : '' }}
                                                                    value="فحص جهاز برمجة"> فحص جهاز برمجة </option>
                                                                <option
                                                                    {{ old('checkout_type') == 'فحص جهاز سريع' ? 'selected' : '' }}
                                                                    value="فحص جهاز سريع"> فحص جهاز سريع </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--################### Start Add ChecksResults ###################-->
                                                <div class="row" id="full_check" style="display: none;">
                                                    <h5> فحص الجهاز <span class="required_span"> * </span> </h5>
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th> # </th>
                                                                    <th> اساسيات الفحص </th>
                                                                    <th> حالة العمل </th>
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
                                                                                value="{{ $check->name }}"
                                                                                class="form-control w-100"
                                                                                name="check_problem_name[]">
                                                                        </td>
                                                                        <td>
                                                                            <select name="work_{{ $check->id }}"
                                                                                class="form-control">
                                                                                <option value="">-- اختر الحالة --
                                                                                </option>
                                                                                <option value="1"
                                                                                    {{ old('work_' . $check->id) == '1' ? 'selected' : '' }}>
                                                                                    يعمل</option>
                                                                                <option value="0"
                                                                                    {{ old('work_' . $check->id) == '0' ? 'selected' : '' }}>
                                                                                    لا يعمل</option>
                                                                            </select>
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
                                                </div>
                                                <!--################### End Add ChecksResults #####################-->
                                                <!--################### Start Speed Device Check  ###################-->
                                                <div class="row" id="speed_check" style="display: none;">
                                                    <h5> جهاز سريع <span class="required_span"> * </span> </h5>
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th> # </th>
                                                                    <th> اساسيات الفحص </th>
                                                                    <th> حالة العمل </th>
                                                                    <th> ملاحظات </th>
                                                                    <th> بعد الفحص </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($speed_devices as $speed)
                                                                    <tr>
                                                                        <td> {{ $loop->iteration }}</td>
                                                                        <td>
                                                                            <input type="hidden" name="speed_id[]"
                                                                                value="{{ $speed->id }}">
                                                                            <input readonly type="text"
                                                                                value="{{ $speed->name }}"
                                                                                class="form-control"
                                                                                name="check_speed_name[]">
                                                                        </td>
                                                                        <td>
                                                                            <select name="speedwork_{{ $speed->id }}"
                                                                                class="form-control">
                                                                                <option value="">-- اختر الحالة --
                                                                                </option>
                                                                                <option value="1"
                                                                                    {{ old('speedwork_' . $speed->id) == '1' ? 'selected' : '' }}>
                                                                                    يعمل</option>
                                                                                <option value="0"
                                                                                    {{ old('speedwork_' . $speed->id) == '0' ? 'selected' : '' }}>
                                                                                    لا يعمل</option>
                                                                            </select>
                                                                        </td>

                                                                        {{-- <td>
                                                                        <input type="radio" value="1"
                                                                            class="form-control"
                                                                            name="speedwork_{{ $speed->id }}[]"
                                                                            {{ old('speedwork_' . $speed->id) == '1' ? 'checked' : '' }}>
                                                                    </td>
                                                                    <td>
                                                                        <input type="radio" value="0"
                                                                            class="form-control"
                                                                            name="speedwork_{{ $speed->id }}[]"
                                                                            {{ old('speedwork_' . $speed->id) == '0' ? 'checked' : '' }}>
                                                                    </td> --}}
                                                                        <td>
                                                                            <input type="text"
                                                                                value="{{ old('speed_notes.' . $loop->index) }}"
                                                                                class="form-control" name="speed_notes[]">
                                                                        </td>
                                                                        <td>
                                                                            <input type="text"
                                                                                value="{{ old('after_check_speed.' . $loop->index) }}"
                                                                                class="form-control"
                                                                                name="after_check_speed[]">
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <!--################### End Speed Device Check  #####################-->
                                                <!--################### Start Programe Device Check  ###################-->
                                                <div class="row" id="programe_check" style="display: none;">
                                                    <h5> جهاز برمجة <span class="required_span"> * </span> </h5>
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th> # </th>
                                                                    <th> اساسيات الفحص </th>
                                                                    <th> حالة العمل </th>
                                                                    <th> ملاحظات </th>
                                                                    <th> بعد الفحص </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($programe_devices as $programe)
                                                                    <tr>
                                                                        <td> {{ $loop->iteration }}</td>
                                                                        <td>
                                                                            <input type="hidden" name="programe_id[]"
                                                                                value="{{ $programe->id }}">
                                                                            <input readonly type="text"
                                                                                value="{{ $programe->name }}"
                                                                                class="form-control"
                                                                                name="check_programe_name[]">
                                                                        </td>
                                                                        <td>
                                                                            <select
                                                                                name="programework_{{ $programe->id }}"
                                                                                class="form-control">
                                                                                <option value="">-- اختر الحالة --
                                                                                </option>
                                                                                <option value="1"
                                                                                    {{ old('programework_' . $programe->id) == '1' ? 'selected' : '' }}>
                                                                                    يعمل</option>
                                                                                <option value="0"
                                                                                    {{ old('programework_' . $programe->id) == '0' ? 'selected' : '' }}>
                                                                                    لا يعمل</option>
                                                                            </select>
                                                                        </td>

                                                                        {{-- <td>
                                                                        <input type="radio" value="1"
                                                                            class="form-control"
                                                                            name="programework_{{ $programe->id }}[]"
                                                                            {{ old('programework_' . $programe->id) == '1' ? 'checked' : '' }}>
                                                                    </td>
                                                                    <td>
                                                                        <input type="radio" value="0"
                                                                            class="form-control"
                                                                            name="programework_{{ $programe->id }}[]"
                                                                            {{ old('programework_' . $programe->id) == '0' ? 'checked' : '' }}>
                                                                    </td> --}}
                                                                        <td>
                                                                            <input type="text"
                                                                                value="{{ old('programe_notes.' . $loop->index) }}"
                                                                                class="form-control"
                                                                                name="programe_notes[]">
                                                                        </td>
                                                                        <td>
                                                                            <input type="text"
                                                                                value="{{ old('after_check_programe.' . $loop->index) }}"
                                                                                class="form-control"
                                                                                name="after_check_programe[]">
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <!--################### End Programe Device Check  #####################-->

                                                <div class="row">
                                                    @foreach ($invoice_more_checks as $invoice_more_check)
                                                        <div class="col-6">
                                                            {{-- <div class="skin skin-square"> --}}
                                                            <fieldset>
                                                                <input type="checkbox"
                                                                    id="inputmorecheck-{{ $invoice_more_check->id }}"
                                                                    name="invoice_more_checks[]"
                                                                    value="{{ $invoice_more_check->id }}"
                                                                    @checked(in_array($invoice_more_check->id, old('invoice_more_checks', [])))>
                                                                <label
                                                                    for="inputmorecheck-{{ $invoice_more_check->id }}">{{ $invoice_more_check->name }}
                                                                </label>
                                                            </fieldset>
                                                            {{-- </div> --}}
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <br>
                                                <!-- باقي الحقول -->
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="name"> اسم العميل <span class="required_span">
                                                                    *
                                                                </span> </label>
                                                            <input required type="text" id="name"
                                                                class="form-control" placeholder="" name="name"
                                                                value="{{ old('name') }}"
                                                                data-parsley-required-message="الرجاء إدخال اسم العميل ">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="phone"> رقم الهاتف <span class="required_span">
                                                                    *</span> </label>
                                                            <input required type="text" id="phone"
                                                                class="form-control" placeholder="مثال: 0500000000"
                                                                name="phone" value="{{ old('phone') }}"
                                                                maxlength="10" oninput="validatePhoneNumber(this)">
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
                                                            <label for="title"> اسم الجهاز <span class="required_span">
                                                                    *
                                                                </span> </label>
                                                            <input required type="text" id="title"
                                                                class="form-control" placeholder="" name="title"
                                                                value="{{ old('title') }}"
                                                                data-parsley-required-message="الرجاء إدخال  اسم الجهاز">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="title"> حدد الاعطال <span
                                                                    class="required_span"> * </span> </label>
                                                            <div class="skin skin-square">
                                                                <!----------- ############## All Check ############## ------------>
                                                                <div class="flex-wrap col-md-12 col-sm-12 problem_check_box"
                                                                    style="display: none;" id="problem_all_check">
                                                                    @foreach ($problems as $problem)
                                                                        <fieldset style="min-width: 120px">
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

                                                                <!-- ################ End All Check ################## -->

                                                                <!-------############# Start Programe Check ##################-------------->
                                                                <div class="flex-wrap col-md-12 col-sm-12 problem_check_box"
                                                                    style="display: none;" id="problem_programe_check">
                                                                    @foreach ($programe_problems as $programe_problem)
                                                                        <fieldset style="min-width: 120px">
                                                                            <input type="checkbox"
                                                                                id="inputprograme-{{ $programe_problem->id }}"
                                                                                name="problems[]"
                                                                                value="{{ $programe_problem->name }}">
                                                                            <label
                                                                                for="inputprograme-{{ $programe_problem->id }}">{{ $programe_problem->name }}
                                                                            </label>
                                                                        </fieldset>
                                                                    @endforeach
                                                                </div>
                                                                <!-------############# End  Programe Check ##################-------------->

                                                                <!-------############# Start Programe Check ##################-------------->
                                                                <div class="flex-wrap col-md-12 col-sm-12 problem_check_box"
                                                                    style="display: none;" id="problem_speed_check">
                                                                    @foreach ($speed_problems as $speed_problem)
                                                                        <fieldset style="min-width: 120px">
                                                                            <input type="checkbox"
                                                                                id="inputspeed-{{ $speed_problem->id }}"
                                                                                name="problems[]"
                                                                                value="{{ $speed_problem->name }}">
                                                                            <label
                                                                                for="inputspeed-{{ $speed_problem->id }}">{{ $speed_problem->name }}
                                                                            </label>
                                                                        </fieldset>
                                                                    @endforeach
                                                                </div>
                                                                <!-------############# End  Programe Check ##################-------------->
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
                                                            <label for="price">السعر الأولي <span
                                                                    class="required_span"> * </span></label>
                                                            <input readonly type="number" step="0.01" id="price"
                                                                class="form-control"
                                                                placeholder="سيتم حساب المجموع تلقائيًا" name="price"
                                                                data-parsley-required-message="الرجاء إدخال تفاصيل السعر"
                                                                value="{{ old('price', 0) }}">
                                                        </div>
                                                        <!-- زر إضافة تفاصيل السعر -->
                                                        <div class="form-group">
                                                            <button type="button" class="btn btn-sm btn-primary"
                                                                onclick="addPriceDetail()">
                                                                <i class="la la-plus"></i> إضافة تفاصيل السعر
                                                            </button>
                                                        </div>
                                                        <!-- حاوية تفاصيل السعر -->
                                                        <div id="price-details-wrapper">
                                                            <!-- سيتم إضافة تفاصيل السعر هنا -->
                                                        </div>

                                                        <script>
                                                            let detailIndex = 0;

                                                            function addPriceDetail() {
                                                                const wrapper = document.getElementById('price-details-wrapper');

                                                                const detailDiv = document.createElement('div');
                                                                detailDiv.classList.add('form-row', 'mb-2');
                                                                detailDiv.innerHTML = `
                                                                            <div class="col-6">
                                                                                <input type="text" name="price_details[${detailIndex}][title]" class="form-control" placeholder="عنوان التفصيلة">
                                                                            </div>
                                                                            <div class="col-5">
                                                                                <input type="number" step="0.01" name="price_details[${detailIndex}][amount]" class="form-control"
                                                                                    placeholder="السعر" required oninput="updateTotalPrice()">
                                                                            </div>
                                                                            <div class="col-1">
                                                                                <button type="button" class="btn btn-danger btn-sm" onclick="removePriceDetail(this)">-</button>
                                                                            </div>
                                                                        `;
                                                                wrapper.appendChild(detailDiv);
                                                                detailIndex++;
                                                                updateTotalPrice(); // تحديث المجموع بعد إضافة تفصيلة
                                                            }

                                                            function removePriceDetail(button) {
                                                                button.parentElement.parentElement.remove();
                                                                updateTotalPrice(); // تحديث المجموع بعد إزالة تفصيلة
                                                            }

                                                            function updateTotalPrice() {
                                                                const priceInputs = document.querySelectorAll('input[name*="price_details"][name$="[amount]"]');
                                                                let total = 0;

                                                                priceInputs.forEach(input => {
                                                                    const value = parseFloat(input.value);
                                                                    if (!isNaN(value)) {
                                                                        total += value;
                                                                    }
                                                                });

                                                                const priceField = document.getElementById('price');
                                                                priceField.value = total.toFixed(2); // تحديث حقل السعر الأولي
                                                            }

                                                            // تحديث المجموع عند تحميل الصفحة إذا كانت هناك بيانات محفوظة
                                                            document.addEventListener('DOMContentLoaded', () => {
                                                                updateTotalPrice();
                                                            });
                                                        </script>
                                                    </div>
                                                    <div class="col-12">
                                                        <label for="price"> التاريخ ووقت التسليم <span
                                                                class="required_span"> * </span> </label>
                                                        <div class="justify-between d-flex flex-column">
                                                            <div class="form-group" style="width: 100%">
                                                                <input required type="date" name="date_delivery"
                                                                    data-parsley-required-message="الرجاء إدخال التاريخ "
                                                                    class="form-control"
                                                                    value="{{ old('date_delivery') }}">
                                                            </div>
                                                            <div class="form-group" style="width: 100%">
                                                                <input required type="time" name="time_delivery"
                                                                    data-parsley-required-message="الرجاء إدخال التاريخ "
                                                                    class="form-control"
                                                                    value="{{ old('time_delivery') }}">
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

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="name"> رمز الجهاز </label>
                                                            <input type="text" id="device_text_password"
                                                                class="form-control" placeholder=""
                                                                name="device_text_password"
                                                                value="{{ old('device_text_password') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class="d-flex">
                                                                <input type="number" min="1" max="12"
                                                                    name="pattern[]" value="{{ old('pattern.0') }}">
                                                                <input type="number" min="1" max="12"
                                                                    name="pattern[]" value="{{ old('pattern.1') }}">
                                                                <input type="number" min="1" max="12"
                                                                    name="pattern[]" value="{{ old('pattern.2') }}">
                                                            </div>
                                                            <div class="d-flex">
                                                                <input type="number" min="1" max="12"
                                                                    name="pattern[]" value="{{ old('pattern.3') }}">
                                                                <input type="number" min="1" max="12"
                                                                    name="pattern[]" value="{{ old('pattern.4') }}">
                                                                <input type="number" min="1" max="12"
                                                                    name="pattern[]" value="{{ old('pattern.5') }}">
                                                            </div>
                                                            <div class="d-flex">
                                                                <input type="number" min="1" max="12"
                                                                    name="pattern[]" value="{{ old('pattern.6') }}">
                                                                <input type="number" min="1" max="12"
                                                                    name="pattern[]" value="{{ old('pattern.7') }}">
                                                                <input type="number" min="1" max="12"
                                                                    name="pattern[]" value="{{ old('pattern.8') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label for="price"> حدد مصدر القطعة <span
                                                                    class="required_span"> *
                                                                </span> </label>
                                                            <select required name="piece_resource" id=""
                                                                data-parsley-required-message=" من فضلك حدد مصدر القطعة  "
                                                                class="form-control">
                                                                <option value="" selected disabled> -- حدد مصدر
                                                                    القطعة -- </option>
                                                                @foreach ($piece_resources as $resource)
                                                                    <option @selected(old('piece_resource') == $resource->id)
                                                                        value="{{ $resource->id }}">
                                                                        {{ $resource->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- اضافة المرفقات -->
                                                <!-- اضافة المرفقات -->
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label for="address"> تصوير حالة الجهاز  <span
                                                                    class="required_span"> * </span> </label>
                                                            <input required type="file" name="files_images[]"
                                                                data-parsley-required-message="الرجاء إدخال المرفقات "
                                                                class="form-control" multiple id="imageInput">
                                                        </div>
                                                        <div id="imagePreview" class="flex-wrap mt-3 d-flex"></div>
                                                    </div>
                                                </div>

                                                <script>
                                                    let imageInput = document.getElementById('imageInput');
                                                    let imagePreview = document.getElementById('imagePreview');
                                                    let dt = new DataTransfer(); // لتخزين الملفات

                                                    imageInput.addEventListener('change', function(event) {
                                                        Array.from(event.target.files).forEach(file => {
                                                            // فقط صور
                                                            if (!file.type.startsWith('image/')) return;

                                                            let reader = new FileReader();
                                                            reader.onload = function(e) {
                                                                let img = new Image();
                                                                img.onload = function() {
                                                                    const MAX_WIDTH = 800;
                                                                    const MAX_HEIGHT = 800;
                                                                    let width = img.width;
                                                                    let height = img.height;

                                                                    if (width > height) {
                                                                        if (width > MAX_WIDTH) {
                                                                            height *= MAX_WIDTH / width;
                                                                            width = MAX_WIDTH;
                                                                        }
                                                                    } else {
                                                                        if (height > MAX_HEIGHT) {
                                                                            width *= MAX_HEIGHT / height;
                                                                            height = MAX_HEIGHT;
                                                                        }
                                                                    }

                                                                    let canvas = document.createElement("canvas");
                                                                    canvas.width = width;
                                                                    canvas.height = height;
                                                                    let ctx = canvas.getContext("2d");
                                                                    ctx.drawImage(img, 0, 0, width, height);

                                                                    // تحويل canvas لصورة مضغوطة
                                                                    let compressedDataUrl = canvas.toDataURL('image/jpeg', 0.7);

                                                                    // إنشاء حاوية الصورة
                                                                    let imgContainer = document.createElement("div");
                                                                    imgContainer.classList.add("position-relative", "m-2");

                                                                    let imgElement = document.createElement("img");
                                                                    imgElement.src = compressedDataUrl;
                                                                    imgElement.classList.add("rounded", "shadow", "border", "p-1");
                                                                    imgElement.style.width = "120px";
                                                                    imgElement.style.height = "120px";

                                                                    let removeBtn = document.createElement("span");
                                                                    removeBtn.innerHTML = "&times;";
                                                                    removeBtn.classList.add("position-absolute", "remove-button", "top-0",
                                                                        "end-0", "bg-danger", "text-white", "rounded-circle", "p-1");
                                                                    removeBtn.style.cursor = "pointer";

                                                                    removeBtn.onclick = function() {
                                                                        let index = Array.from(dt.files).findIndex(f => f.name === file
                                                                            .name);
                                                                        if (index > -1) {
                                                                            dt.items.remove(index);
                                                                            imageInput.files = dt.files;
                                                                        }
                                                                        imgContainer.remove();
                                                                    };

                                                                    imgContainer.appendChild(imgElement);
                                                                    imgContainer.appendChild(removeBtn);
                                                                    imagePreview.appendChild(imgContainer);

                                                                    // إضافة الملف الأصلي إلى input
                                                                    dt.items.add(file);
                                                                    imageInput.files = dt.files;
                                                                };
                                                                img.src = e.target.result;
                                                            };
                                                            reader.readAsDataURL(file);
                                                        });
                                                    });
                                                </script>


                                                {{-- New Updtae Images  --}}


                                                {{-- <div class="form-group">
                                                    <label>📸 التقاط الصور من الكاميرا</label><br>
                                                    <button class="mb-2 btn btn-primary"
                                                        onclick="startCamera(event)">تشغيل الكاميرا</button>
                                                    <button class="mb-2 btn btn-success" onclick="takeSnapshot(event)">📷
                                                        التقاط صورة</button>
                                                    <div>
                                                        <video id="video" width="320" height="240" autoplay
                                                            style="border:1px solid #ccc;"></video>
                                                        <canvas id="canvas" style="display: none;"></canvas>
                                                    </div>
                                                    <div id="snapshots" class="flex-wrap mt-3 d-flex" style="gap: 10px;">
                                                    </div>
                                                    <div id="imageHiddenInputs"></div>
                                                </div> --}}


                                                <script>
                                                    let video = document.getElementById('video');
                                                    let canvas = document.getElementById('canvas');
                                                    let snapshotsContainer = document.getElementById('snapshots');
                                                    let imageHiddenInputs = document.getElementById('imageHiddenInputs');
                                                    let stream = null;

                                                    function startCamera(event) {
                                                        event.preventDefault(); // منع أي إرسال غير مرغوب فيه
                                                        navigator.mediaDevices.getUserMedia({
                                                                video: true
                                                            })
                                                            .then(function(mediaStream) {
                                                                stream = mediaStream;
                                                                video.srcObject = mediaStream;
                                                            })
                                                            .catch(function(err) {
                                                                alert("تعذر تشغيل الكاميرا: " + err);
                                                            });
                                                    }

                                                    function takeSnapshot(event) {
                                                        event.preventDefault(); // منع أي إرسال غير مرغوب فيه
                                                        const context = canvas.getContext('2d');
                                                        canvas.width = video.videoWidth;
                                                        canvas.height = video.videoHeight;
                                                        context.drawImage(video, 0, 0, canvas.width, canvas.height);

                                                        // ضغط الصورة
                                                        let dataUrl = canvas.toDataURL('image/jpeg', 0.7); // ضغط 70%

                                                        // عرض الصورة
                                                        let img = document.createElement('img');
                                                        img.src = dataUrl;
                                                        img.style.width = '150px';
                                                        img.style.border = '2px solid #ccc';
                                                        img.style.borderRadius = '5px';

                                                        // إضافة زر حذف
                                                        let deleteButton = document.createElement('button');
                                                        deleteButton.textContent = 'حذف';
                                                        deleteButton.classList.add('btn', 'btn-danger', 'mt-2');
                                                        deleteButton.onclick = function() {
                                                            deleteImage(img, dataUrl);
                                                        };

                                                        // إنشاء حاوية للصورة وزر الحذف
                                                        let imgContainer = document.createElement('div');
                                                        imgContainer.style.position = 'relative';
                                                        imgContainer.appendChild(img);
                                                        imgContainer.appendChild(deleteButton);

                                                        // إضافة الصورة والحاوية إلى الحاوية الرئيسية
                                                        snapshotsContainer.appendChild(imgContainer);

                                                        // إضافة input مخفي للإرسال
                                                        let input = document.createElement('input');
                                                        input.type = 'hidden';
                                                        input.name = 'captured_images[]';
                                                        input.value = dataUrl;
                                                        imageHiddenInputs.appendChild(input);
                                                    }

                                                    // دالة حذف الصورة
                                                    function deleteImage(imageElement, dataUrl) {
                                                        // إزالة الصورة من العرض
                                                        imageElement.parentNode.remove();

                                                        // إزالة الـ input المخفي الذي يحتوي على البيانات
                                                        let inputs = imageHiddenInputs.getElementsByTagName('input');
                                                        for (let i = 0; i < inputs.length; i++) {
                                                            if (inputs[i].value === dataUrl) {
                                                                inputs[i].remove();
                                                                break;
                                                            }
                                                        }
                                                    }
                                                </script>


                                                <!-- عنصر التوقيع -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>توقيع العميل <span class="required_span"> * </span> </label>
                                                        <div id="signature-pad" class="signature-pad">
                                                            <div class="signature-pad-body">
                                                                <canvas id="signatureCanvas"></canvas>
                                                            </div>
                                                            <div class="signature-pad-footer">
                                                                <button type="button" id="clear-signature"
                                                                    class="mt-2 btn btn-danger">مسح التوقيع</button>
                                                            </div>
                                                        </div>
                                                        <input required type="text" readonly style="opacity: 0"
                                                            name="signature" id="signature"
                                                            data-parsley-required-message=" الرجاء التوقيع  "
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
                                                <p id="loadingMessage" class="mt-2 text-info" style="display: none;">⏳
                                                    جاري رفع البيانات، الرجاء الانتظار...</p>
                                                <p id="signatureError" class="mt-2 text-danger" style="display: none;">
                                                    الرجاء التوقيع على الفاتورة
                                                </p>
                                                <p id="ImagesError" class="mt-2 text-danger" style="display: none;">
                                                    الرجاء رفع صورة من الفاتورة
                                                </p>
                                                <p id="ChecksError" class="mt-2 text-danger" style="display: none;">
                                                    من فضلك حدد الاعطال المناسبة حسب نوع الفحص
                                                </p>
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
                                            var signcanvas = document.getElementById("signatureCanvas");
                                            var signaturePad = new SignaturePad(signcanvas);

                                            // مسح التوقيع عند الضغط على الزر
                                            document.getElementById("clear-signature").addEventListener("click", function() {
                                                signaturePad.clear();
                                            });

                                            document.getElementById("invoice-form").addEventListener("submit", function(e) {
                                                var signatureInput = document.getElementById("signature");
                                                let checkoutType = document.getElementById("checkout_type").value;

                                                let problem_all_check = document.getElementById('problem_all_check');
                                                let problem_programe_check = document.getElementById('problem_programe_check');
                                                let problem_speed_check = document.getElementById('problem_speed_check');

                                                // تحديد العنصر الذي يجب التحقق منه حسب نوع الفحص
                                                let activeProblemContainer;
                                                if (checkoutType === "فحص كامل") {
                                                    activeProblemContainer = problem_all_check;
                                                } else if (checkoutType === "فحص جهاز برمجة") {
                                                    activeProblemContainer = problem_programe_check;
                                                } else if (checkoutType === "فحص جهاز سريع") {
                                                    activeProblemContainer = problem_speed_check;
                                                }

                                                if (activeProblemContainer) {
                                                    let checkedCount = activeProblemContainer.querySelectorAll('input[type="checkbox"]:checked').length;
                                                    if (checkedCount === 0) {
                                                        e.preventDefault();
                                                        ChecksError.style.display = 'block';
                                                        // ChecksError.textContent = 'من فضلك حدد الاعطال المناسبة حسب نوع الفحص';
                                                        return;
                                                    }
                                                }
                                                if (signaturePad.isEmpty()) {
                                                    e.preventDefault();
                                                    alert('الرجاء التوقيع على الفاتورة');
                                                    // signatureError.style.display = 'block';
                                                    //  signatureError.textContent = 'الرجاء التوقيع على الفاتورة';
                                                } else {
                                                    signatureInput.value = signaturePad.toDataURL();

                                                    let submitBtn = this.querySelector('button[type="submit"]');
                                                    let loadingMessage = document.getElementById('loadingMessage');

                                                    submitBtn.disabled = true;
                                                    submitBtn.innerHTML = '<i class="la la-spinner la-spin"></i> جاري الحفظ...';

                                                    loadingMessage.style.display = 'block';

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
    <script src="https://cdn.jsdelivr.net/npm/eruda"></script>
    <script>
        eruda.init();
    </script>
    <script src="{{ asset('assets/admin/') }}/vendors/js/forms/icheck/icheck.min.js" type="text/javascript"></script>
    <script src="{{ asset('assets/admin/') }}/js/scripts/forms/checkbox-radio.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/parsleyjs"></script>
    <script>
        $('#invoice-form').parsley();
    </script>
    <script>
        $(document).ready(function() {
            function toggleCheckoutSections(type) {
                if (type == 'فحص كامل') {
                    $('#full_check').show();
                    $('#problem_all_check').show();
                    $('#programe_check').hide();
                    $('#speed_check').hide();
                    $("#problem_programe_check").hide();
                    $("#problem_speed_check").hide();
                } else if (type == 'فحص جهاز برمجة') {
                    $('#programe_check').show();
                    $('#full_check').hide();
                    $('#speed_check').hide();
                    $("#problem_programe_check").show();
                    $("#problem_speed_check").hide();
                    $('#problem_all_check').hide();
                } else if (type == 'فحص جهاز سريع') {
                    $('#speed_check').show();
                    $('#full_check').hide();
                    $('#programe_check').hide();
                    $("#problem_programe_check").hide();
                    $("#problem_speed_check").show();
                    $('#problem_all_check').hide();
                } else {
                    // إخفاء الكل عند الاختيار الافتراضي أو لا شيء
                    $('#full_check, #programe_check, #speed_check, #problem_all_check, #problem_programe_check, #problem_speed_check')
                        .hide();
                }
            }

            // عند التغيير
            $('#checkout_type').change(function() {
                toggleCheckoutSections($(this).val());
            });

            // عند تحميل الصفحة إذا كان هناك old value
            let oldValue = "{{ old('checkout_type') }}";
            if (oldValue) {
                toggleCheckoutSections(oldValue);
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const disableCheckIds = [1, 3]; // IDs الخاصة بالخيار "خلل يعوق الفحص" أو أي خيار يمنع الفحص

            // دالة تتحقق إن كان أحد الـ checkboxes المختارة مفعلة
            function isAnyDisableChecked() {
                return disableCheckIds.some(id => {
                    const checkbox = document.getElementById("inputmorecheck-" + id);
                    return checkbox && checkbox.checked;
                });
            }

            // دالة لتعطيل كل الـ selectات وتعيينها على "لا يعمل"
            function disableAllChecks() {
                document.querySelectorAll(
                    'select[name^="work_"], select[name^="speedwork_"], select[name^="programework_"]'
                ).forEach(select => {
                    select.value = "0";
                    // select.setAttribute("disabled", "disabled");
                });
            }

            // إضافة مستمع لكل الـ checkboxes المحددة
            disableCheckIds.forEach(id => {
                const checkbox = document.getElementById("inputmorecheck-" + id);
                if (checkbox) {
                    checkbox.addEventListener("change", function() {
                        if (isAnyDisableChecked()) {
                            disableAllChecks();
                        }
                    });

                    // تحقق إذا كانت الصفحة أعادت تحميلها والـ checkbox مفعلة
                    if (checkbox.checked) {
                        checkbox.dispatchEvent(new Event('change'));
                    }
                }
            });
        });
    </script>


@endsection

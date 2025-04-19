@extends('dashboard.layouts.app')

@section('title', ' تعديل فاتورة ')
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
                    <h3 class="mb-0 content-header-title d-inline-block"> تعديل الفاتورة </h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.invoices.index') }}"> الوافتير </a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#"> تعديل الفاتورة </a>
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
                                <div class="card-header">
                                    <h4 class="card-title" id="basic-layout-form"> تعديل الفاتورة </h4>
                                    <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                </div>
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <form class="form" method="POST"
                                            action="{{ route('dashboard.invoices.update', $invoice->id) }}') }}"
                                            enctype="multipart/form-data">
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
                                                                    {{ $invoice->checkout_type == 'فحص كامل' ? 'selected' : '' }}
                                                                    value="فحص كامل"> فحص كامل </option>
                                                                <option
                                                                    {{ $invoice->checkout_type == 'فحص جهاز برمجة' ? 'selected' : '' }}
                                                                    value="فحص جهاز برمجة"> فحص جهاز برمجة </option>
                                                                <option
                                                                    {{ $invoice->checkout_type == 'فحص جهاز سريع' ? 'selected' : '' }}
                                                                    value="فحص جهاز سريع"> فحص جهاز سريع </option>
                                                            </select>

                                                        </div>
                                                    </div>
                                                </div>
                                                <!--################### Start Add ChecksResults ###################-->
                                                <div class="row" id="full_check"
                                                    style="{{ $invoice->checkout_type === 'فحص كامل' ? 'display: block' : 'display: none' }}">
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
                                                                @php
                                                                    $checkResult = $invoice->checkResults
                                                                        ->where('problem_id', $check->id)
                                                                        ->where('invoice_id', $invoice->id)
                                                                        ->first();
                                                                @endphp
                                                                <tr>
                                                                    <td> {{ $loop->iteration }}</td>
                                                                    <td>
                                                                        <input type="hidden"
                                                                            name="work_{{ $check->id }}" value="">
                                                                        <input type="hidden" name="problem_id[]"
                                                                            value="{{ $check->id }}">
                                                                        <input readonly type="text"
                                                                            value="{{ $check->name }}"
                                                                            class="form-control"
                                                                            name="check_problem_name[]">
                                                                    </td>
                                                                    <td>
                                                                        <input type="radio" value="1"
                                                                            class="form-control"
                                                                            name="work_{{ $check->id }}"
                                                                            {{ isset($checkResult) && $checkResult->work == 1 ? 'checked' : '' }}>
                                                                    </td>
                                                                    <td>
                                                                        <input type="radio" value="0"
                                                                            class="form-control"
                                                                            name="work_{{ $check->id }}"
                                                                            {{ isset($checkResult) && $checkResult->work == 0 ? 'checked' : '' }}>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text"
                                                                            value="{{ $checkResult->notes ?? '' }}"
                                                                            class="form-control" name="notes[]">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text"
                                                                            value="{{ $checkResult->after_check ?? '' }}"
                                                                            class="form-control" name="after_check[]">
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--################### End Add ChecksResults #####################-->
                                                <!--################### Start Speed Device Check  ###################-->
                                                <div class="row" id="speed_check"
                                                    style="{{ $invoice->checkout_type === 'فحص جهاز سريع' ? 'display: block' : 'display: none' }}">
                                                    <h5> جهاز سريع <span class="required_span"> * </span> </h5>
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
                                                            @foreach ($speed_devices as $speed)
                                                                @php
                                                                    $speedResult = $invoice
                                                                        ->speedResults()
                                                                        ->where('speed_id', $speed->id)
                                                                        ->where('invoice_id', $invoice->id)
                                                                        ->first();
                                                                @endphp
                                                                <tr>
                                                                    <td> {{ $loop->iteration }}</td>
                                                                    <td>
                                                                        <input type="hidden" name="speed_id[]"
                                                                            value="{{ $speed->id }}">
                                                                        <input readonly type="text"
                                                                            value="{{ $speed->name }}"
                                                                            class="form-control" name="check_speed_name[]">
                                                                    </td>

                                                                    <td>
                                                                        <input type="radio" value="1"
                                                                            class="form-control"
                                                                            name="speedwork_{{ $speed->id }}"
                                                                            {{ isset($speedResult) && $speedResult->work == 1 ? 'checked' : '' }}
                                                                            </td>
                                                                    <td>
                                                                        <input type="radio" value="0"
                                                                            class="form-control"
                                                                            name="speedwork_{{ $speed->id }}"
                                                                            {{ isset($speedResult) && $speedResult->work == 0 ? 'checked' : '' }}>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text"
                                                                            value="{{ $speedResult->notes ?? '' }}"
                                                                            class="form-control" name="speed_notes[]">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text"
                                                                            value="{{ $speedResult->after_check ?? '' }}"
                                                                            class="form-control"
                                                                            name="after_check_speed[]">
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--################### End Speed Device Check  #####################-->

                                                <!--################### Start Programe Device Check  ###################-->
                                                <div class="row" id="programe_check"
                                                    style="{{ $invoice->checkout_type === 'فحص جهاز برمجة' ? 'display: block' : 'display: none' }}">
                                                    <h5> جهاز برمجة <span class="required_span"> * </span> </h5>
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
                                                            @foreach ($programe_devices as $programe)
                                                                @php
                                                                    $programeResult = $invoice
                                                                        ->programeResults()
                                                                        ->where('programe_id', $programe->id)
                                                                        ->where('invoice_id', $invoice->id)
                                                                        ->first();
                                                                @endphp
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
                                                                        <input type="radio" value="1"
                                                                            class="form-control"
                                                                            name="programework_{{ $programe->id }}[]"
                                                                            {{ isset($programeResult) && $programeResult->work == 1 ? 'checked' : '' }}>
                                                                    </td>
                                                                    <td>
                                                                        <input type="radio" value="0"
                                                                            class="form-control"
                                                                            name="programework_{{ $programe->id }}[]"
                                                                            {{ isset($programeResult) && $programeResult->work == 0 ? 'checked' : '' }}>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text"
                                                                            value="{{ $programeResult->notes ?? '' }}"
                                                                            class="form-control" name="programe_notes[]">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text"
                                                                            value="{{ $programeResult->after_check ?? '' }}"
                                                                            class="form-control"
                                                                            name="after_check_programe[]">
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--################### End Programe Device Check  #####################-->
                                                @php
                                                    $selectedChecks =
                                                        json_decode($invoice->invoice_more_checks, true) ?? [];
                                                @endphp
                                                <div class="row">
                                                    @foreach ($invoice_more_checks as $invoice_more_check)
                                                        <div class="col-2">
                                                            <div class="skin skin-square">
                                                                <fieldset>
                                                                    <input type="checkbox"
                                                                        {{ in_array($invoice_more_check->id, $selectedChecks) ? 'checked' : '' }}
                                                                        id="inputmorecheck-{{ $invoice_more_check->id }}"
                                                                        name="invoice_more_checks[]"
                                                                        value="{{ $invoice_more_check->id }}">
                                                                    <label
                                                                        for="inputmorecheck-{{ $invoice_more_check->id }}">
                                                                        {{ $invoice_more_check->name }}
                                                                    </label>
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <br>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="name"> اسم العميل <span class="required_span">
                                                                    *
                                                                </span> </label>
                                                            <input required type="text" id="name"
                                                                class="form-control" placeholder="" name="name"
                                                                value="{{ $invoice->name }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="phone"> رقم الهاتف <span class="required_span">
                                                                    *
                                                                </span> </label>
                                                            <input required type="text" id="phone"
                                                                class="form-control" placeholder="" name="phone"
                                                                value="{{ $invoice->phone }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="title"> اسم الجهاز <span class="required_span">
                                                                    *
                                                                </span> </label>
                                                            <input required type="text" id="title"
                                                                class="form-control" placeholder="" name="title"
                                                                value="{{ $invoice->title }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="title"> حدد الاعطال <span
                                                                    class="required_span">
                                                                    * </span> </label>
                                                            <div class="skin skin-square">
                                                                <!-- ########## Start All Check ####################### -->
                                                                <div class="col-md-12 col-sm-12 problem_check_box"
                                                                    style="{{ $invoice->checkout_type === 'فحص كامل' ? 'display: block' : 'display: none' }}"
                                                                    id="problem_all_check">
                                                                    @foreach ($problems as $problem)
                                                                        <fieldset>
                                                                            <input
                                                                                {{ in_array($problem->name, json_decode($invoice->problems)) ? 'checked' : '' }}
                                                                                type="checkbox"
                                                                                id="input-{{ $problem->id }}"
                                                                                name="problems[]"
                                                                                value="{{ $problem->name }}">
                                                                            <label for="input-{{ $problem->id }}">
                                                                                {{ $problem->name }} </label>
                                                                        </fieldset>
                                                                    @endforeach
                                                                </div>
                                                                <!-- ############# End All Check ################# -->

                                                                <!-------############# Start Programe Check ##################-------------->
                                                                <div class="col-md-12 col-sm-12 problem_check_box"
                                                                    style="{{ $invoice->checkout_type === 'فحص جهاز برمجة' ? 'display: block' : 'display: none' }}"
                                                                    id="problem_programe_check">
                                                                    @foreach ($programe_problems as $programe_problem)
                                                                        <fieldset>
                                                                            <input
                                                                                {{ in_array($programe_problem->name, json_decode($invoice->problems)) ? 'checked' : '' }}
                                                                                type="checkbox"
                                                                                id="inputprograme-{{ $programe_problem->id }}"
                                                                                name="problems[]"
                                                                                value="{{ $programe_problem->name }}">
                                                                            <label
                                                                                for="inputprograme-{{ $programe_problem->id }}">
                                                                                {{ $programe_problem->name }} </label>
                                                                        </fieldset>
                                                                    @endforeach
                                                                </div>
                                                                <!-------############# End  Programe Check ##################-------------->

                                                                <!-------############# Start Programe Check ##################-------------->
                                                                <div class="col-md-12 col-sm-12 problem_check_box"
                                                                    style="{{ $invoice->checkout_type === 'فحص جهاز سريع' ? 'display: block' : 'display: none' }}"
                                                                    id="problem_speed_check">
                                                                    @foreach ($speed_problems as $speed_problem)
                                                                        <fieldset>
                                                                            <input
                                                                                {{ in_array($speed_problem->name, json_decode($invoice->problems)) ? 'checked' : '' }}
                                                                                type="checkbox"
                                                                                id="inputspeed-{{ $speed_problem->id }}"
                                                                                name="problems[]"
                                                                                value="{{ $speed_problem->name }}">
                                                                            <label
                                                                                for="inputspeed-{{ $speed_problem->id }}">
                                                                                {{ $speed_problem->name }} </label>
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
                                                            <textarea name="description" id="" class="form-control">{{ $invoice->description }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="price"> السعر الاولي <span
                                                                    class="required_span"> * </span> </label>
                                                            <input required type="number" step="0.01" id="price"
                                                                class="form-control" placeholder="" name="price"
                                                                value="{{ $invoice->price }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="price"> تاريخ ووقت التسليم <span
                                                                class="required_span"> * </span> </label>
                                                        <div class="justify-between d-flex">
                                                            <div class="form-group">
                                                                <div class="position-relative has-icon-left">
                                                                    <input type="date" name="date_delivery"
                                                                        value="{{ $invoice->date_delivery }}"
                                                                        id="timesheetinput3" class="form-control">
                                                                    <div class="form-control-position">
                                                                        <i class="ft-message-square"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="position-relative has-icon-left">
                                                                    <input type="time" name="time_delivery"
                                                                        value="{{ $invoice->time_delivery }}"
                                                                        id="timesheetinput6" class="form-control">
                                                                    <div class="form-control-position">
                                                                        <i class="ft-clock"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="price"> الحالة </label>
                                                            <select name="status" id="" class="form-control">
                                                                <option selected value="رف الاستلام"> رف الاستلام</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <img width="100" height="100"
                                                        src="{{ asset('assets/uploads/invoices_files/' . $invoice->signature) }}"
                                                        alt="">

                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="name"> رمز الجهاز </label>
                                                            <input required type="text" id="device_text_password"
                                                                class="form-control" placeholder=""
                                                                name="device_text_password"
                                                                value="{{ $invoice->device_password_text ?? old('device_text_password') }}">
                                                        </div>
                                                    </div>
                                                    @php
                                                        $storedPattern = json_decode($invoice->device_pattern, true);
                                                    @endphp
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class="d-flex">
                                                                <input type="number" min="1" max="12"
                                                                    name="pattern[]"
                                                                    value="{{ $storedPattern[0] ?? '' }}">
                                                                <input type="number" min="1" max="12"
                                                                    name="pattern[]"
                                                                    value="{{ $storedPattern[1] ?? '' }}">
                                                                <input type="number" min="1" max="12"
                                                                    name="pattern[]"
                                                                    value="{{ $storedPattern[2] ?? '' }}">
                                                            </div>
                                                            <div class="d-flex">
                                                                <input type="number" min="1" max="12"
                                                                    name="pattern[]"
                                                                    value="{{ $storedPattern[3] ?? '' }}">
                                                                <input type="number" min="1" max="12"
                                                                    name="pattern[]"
                                                                    value="{{ $storedPattern[4] ?? '' }}">
                                                                <input type="number" min="1" max="12"
                                                                    name="pattern[]"
                                                                    value="{{ $storedPattern[5] ?? '' }}">
                                                            </div>
                                                            <div class="d-flex">
                                                                <input type="number" min="1" max="12"
                                                                    name="pattern[]"
                                                                    value="{{ $storedPattern[6] ?? '' }}">
                                                                <input type="number" min="1" max="12"
                                                                    name="pattern[]"
                                                                    value="{{ $storedPattern[7] ?? '' }}">
                                                                <input type="number" min="1" max="12"
                                                                    name="pattern[]"
                                                                    value="{{ $storedPattern[8] ?? '' }}">
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
                                                                class="form-control">
                                                                <option value="" selected disabled> -- حدد مصدر
                                                                    القطعة -- </option>
                                                                @foreach ($piece_resources as $resource)
                                                                    <option @selected($invoice->piece_resource == $resource->id)
                                                                        value="{{ $resource->id }}">
                                                                        {{ $resource->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label for="address"> اضافة مرفقات <span
                                                                    class="required_span"> * </span> </label>
                                                            <input type="file" name="files_images[]"
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
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-check">
                                                            <input checked required class="form-check-input"
                                                                type="checkbox" value="" id="flexCheckDefault">
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
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"> حالة التواصل مع العميل </h4>
                                    <br>
                                    <form action="{{ route('dashboard.tech_invoices.client-connect', $invoice->id) }}"
                                        method="post">
                                        @csrf
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <select disabled name="client_connect" id=""
                                                    class="form-control">
                                                    <option value="" selected disabled> -- حدد -- </option>
                                                    <option value="1" @selected($invoice->client_connect == 1)> تم التواصل
                                                    </option>
                                                    <option value="0" @selected($invoice->client_connect == 0)> لم يتم التواصل
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="title"> ملاحظات التواصل </label>
                                                <textarea disabled readonly name="client_connect_notes" id="" class="form-control">{{ $invoice->client_connect_notes }}</textarea>
                                            </div>
                                        </div>
                                        {{-- <div class="form-actions">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="la la-check-square-o"></i> حفظ
                                            </button>
                                        </div> --}}
                                    </form>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title" id="basic-layout-form"> المرفقات </h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            @forelse ($invoice->files as $file)
                                                <div class="col-md-3">
                                                    <div class="card">
                                                        <form
                                                            action="{{ route('dashboard.invoices.delete_file', $file['id']) }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="filess">
                                                                <a target="_blank" href="{{ asset('assets/uploads/invoices_files/' . $file['image']) }}">
                                                                    <img class="file_image"
                                                                    src="{{ asset('assets/uploads/invoices_files/' . $file['image']) }}"
                                                                    alt="Card image cap">
                                                                </a>

                                                                <button onclick="return confirm('هل تريد حذف هذا المرفق؟')"
                                                                    type="submit" class="btn btn-danger btn-sm">
                                                                    <i class="la la-trash"></i> حذف </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @empty
                                                لا يوجد مرفقات
                                            @endforelse
                                        </div>
                                        <style>
                                            .filess {
                                                display: flex;
                                                flex-direction: column;
                                                justify-content: center;
                                                align-items: center;
                                            }

                                            .file_image {
                                                width: 150px;
                                                height: 150px;
                                                border: 2px #f1f1f1 solid;
                                                border-radius: 10px;
                                                padding: 2px;
                                            }

                                            .filess button {
                                                margin-top: 10px;
                                            }
                                        </style>
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

    <script>
        $(document).ready(function() {
            $('#checkout_type').change(function() {
                if ($(this).val() == 'فحص كامل') {
                    $('#full_check').show();
                    $('#problem_all_check').show();
                    $('#programe_check').hide();
                    $('#speed_check').hide();
                    $("#problem_programe_check").hide();
                    $("#problem_speed_check").hide();
                } else if ($(this).val() == 'فحص جهاز برمجة') {
                    $('#programe_check').show();
                    $('#full_check').hide();
                    $('#speed_check').hide();
                    $("#problem_programe_check").show();
                    $("#problem_speed_check").hide();
                    $('#problem_all_check').hide();
                } else if ($(this).val() == 'فحص جهاز سريع') {
                    $('#speed_check').show();
                    $('#full_check').hide();
                    $('#programe_check').hide();
                    $("#problem_programe_check").hide();
                    $("#problem_speed_check").show();
                    $('#problem_all_check').hide();
                }
            });
        });
    </script>

@endsection

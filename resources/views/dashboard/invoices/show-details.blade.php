@extends('dashboard.layouts.app')

@section('title', ' تفاصيل الفاتورة كاملة ')
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
                    <h3 class="mb-0 content-header-title d-inline-block"> تفاصيل الفاتورة كاملة </h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.invoices.index') }}"> الفواتير </a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#"> تفاصيل الفاتورة كاملة </a>
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
                                    <h4 class="card-title" id="basic-layout-form"> تفاصيل الفاتورة كاملة </h4>
                                    <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                </div>
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <form class="form" method="POST" id='invoice-form'
                                            action="{{ route('dashboard.tech_invoices.update', $invoice->id) }}') }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-body">
                                                <!--################### Start Add ChecksResults ###################-->
                                                <div class="row" id="full_check"
                                                    style="{{ $invoice->checkout_type === 'فحص كامل' ? 'display: block' : 'display: none' }}">

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
                                                                        <input readonly disabled type="hidden"
                                                                            name="problem_id[]" value="{{ $check->id }}">
                                                                        <input readonly type="text"
                                                                            value="{{ $check->name }}" class="form-control"
                                                                            name="check_problem_name[]">
                                                                    </td>
                                                                    <td>
                                                                        <input readonly disabled type="radio"
                                                                            value="1" class="form-control"
                                                                            name="work_{{ $check->id }}[]"
                                                                            {{ isset($checkResult) && $checkResult->work == 1 ? 'checked' : '' }}>
                                                                    </td>
                                                                    <td>
                                                                        <input readonly disabled type="radio"
                                                                            value="0" class="form-control"
                                                                            name="work_{{ $check->id }}[]"
                                                                            {{ isset($checkResult) && $checkResult->work == 0 ? 'checked' : '' }}>
                                                                    </td>
                                                                    <td>
                                                                        <input readonly disabled type="text"
                                                                            value="{{ $checkResult->notes ?? '' }}"
                                                                            class="form-control" name="notes[]">
                                                                    </td>
                                                                    <td>
                                                                        <input readonly disabled type="text"
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
                                                                        <input readonly disabled type="text"
                                                                            value="{{ $speed->name }}"
                                                                            class="form-control" name="check_speed_name[]">
                                                                    </td>

                                                                    <td>
                                                                        <input readonly disabled type="radio"
                                                                            value="1" class="form-control"
                                                                            name="speedwork_{{ $speed->id }}"
                                                                            {{ isset($speedResult) && $speedResult->work == 1 ? 'checked' : '' }}
                                                                            </td>
                                                                    <td>
                                                                        <input readonly disabled type="radio"
                                                                            value="0" class="form-control"
                                                                            name="speedwork_{{ $speed->id }}"
                                                                            {{ isset($speedResult) && $speedResult->work == 0 ? 'checked' : '' }}>
                                                                    </td>
                                                                    <td>
                                                                        <input readonly disabled type="text"
                                                                            value="{{ $speedResult->notes ?? '' }}"
                                                                            class="form-control" name="speed_notes[]">
                                                                    </td>
                                                                    <td>
                                                                        <input readonly disabled type="text"
                                                                            value="{{ $speedResult->after_check ?? '' }}"
                                                                            class="form-control" name="after_check_speed[]">
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
                                                                        <input readonly disabled type="text"
                                                                            value="{{ $programe->name }}"
                                                                            class="form-control"
                                                                            name="check_programe_name[]">
                                                                    </td>
                                                                    <td>
                                                                        <input readonly disabled type="radio"
                                                                            value="1" class="form-control"
                                                                            name="programework_{{ $programe->id }}[]"
                                                                            {{ isset($programeResult) && $programeResult->work == 1 ? 'checked' : '' }}>
                                                                    </td>
                                                                    <td>
                                                                        <input readonly disabled type="radio"
                                                                            value="0" class="form-control"
                                                                            name="programework_{{ $programe->id }}[]"
                                                                            {{ isset($programeResult) && $programeResult->work == 0 ? 'checked' : '' }}>
                                                                    </td>
                                                                    <td>
                                                                        <input readonly disabled type="text"
                                                                            value="{{ $programeResult->notes ?? '' }}"
                                                                            class="form-control" name="programe_notes[]">
                                                                    </td>
                                                                    <td>
                                                                        <input readonly disabled type="text"
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
                                                        <div class="col-6">
                                                            <div class="skin skin-square">
                                                                <fieldset>
                                                                    <input type="checkbox" disabled
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


                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="name"> الاستقبال <span class="required_span">
                                                                    *
                                                                </span> </label>
                                                            <input disabled type="text" id="name"
                                                                class="form-control" placeholder="" name="name"
                                                                value="{{ $invoice->Recieved->name }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="name"> الفني <span class="required_span">
                                                                    *
                                                                </span> </label>
                                                            <input disabled type="text" id="name"
                                                                class="form-control" placeholder="" name="name"
                                                                value="{{ $invoice->Technical->name ?? 'لا يوجد' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="name"> اسم العميل <span class="required_span">
                                                                    *
                                                                </span> </label>
                                                            <input disabled type="text" id="name"
                                                                class="form-control" placeholder="" name="name"
                                                                value="{{ $invoice->name }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="phone"> رقم الهاتف <span class="required_span">
                                                                    *
                                                                </span> </label>
                                                            <input disabled type="text" id="phone"
                                                                class="form-control" placeholder="" name="phone"
                                                                value="{{ $invoice->phone }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="title"> اسم الجهاز <span class="required_span">
                                                                    *
                                                                </span> </label>
                                                            <input disabled type="text" id="title"
                                                                class="form-control" placeholder="" name="title"
                                                                value="{{ $invoice->title }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="title"> الاعطال <span class="required_span">
                                                                    * </span> </label>
                                                            <div class="skin skin-square">
                                                                <!-- ########## Start All Check ####################### -->
                                                                <div class="col-md-12 col-sm-12 problem_check_box"
                                                                    style="{{ $invoice->checkout_type === 'فحص كامل' ? 'display: flex; flex-wrap: wrap; word-wrap: break-word;' : 'display: none' }}"
                                                                    id="problem_all_check">
                                                                    @foreach ($problems as $problem)
                                                                        <fieldset style="min-width: 120px">
                                                                            <input disabled
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
                                                                    style="{{ $invoice->checkout_type === 'فحص جهاز برمجة' ? 'display: flex; flex-wrap: wrap; word-wrap: break-word;' : 'display: none' }}"
                                                                    id="problem_programe_check">
                                                                    @foreach ($programe_problems as $programe_problem)
                                                                        <fieldset style="min-width: 120px">
                                                                            <input disabled
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
                                                                    style="{{ $invoice->checkout_type === 'فحص جهاز سريع' ? 'display: flex; flex-wrap: wrap; word-wrap: break-word;' : 'display: none' }}"
                                                                    id="problem_speed_check">
                                                                    @foreach ($speed_problems as $speed_problem)
                                                                        <fieldset style="min-width: 120px">
                                                                            <input disabled
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
                                                            <textarea disabled name="description" id="" class="form-control">{{ $invoice->description }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="price">السعر <span class="required_span"> *
                                                                </span></label>
                                                            <input readonly type="number" step="0.01" id="price"
                                                                class="form-control" name="price"
                                                                value="{{ $invoice->price }}">
                                                        </div>

                                                        <!-- زر إضافة تفاصيل السعر -->
                                                        {{-- <div class="mt-2 form-group">
                                                            <button type="button" class="btn btn-sm btn-primary"
                                                                onclick="addPriceDetail()">
                                                                إضافة تفاصيل السعر
                                                            </button>
                                                        </div> --}}

                                                        <!-- تفاصيل السعر القديمة -->
                                                        <div id="price-details-wrapper">
                                                            @php $detailIndex = 0; @endphp
                                                            @foreach ($invoice->priceDetails as $detail)
                                                                <div class="mb-2 form-row">
                                                                    <input type="hidden"
                                                                        name="price_details[{{ $detailIndex }}][id]"
                                                                        value="{{ $detail->id }}">
                                                                    <div class="col-4">
                                                                        <input readonly type="text"
                                                                            name="price_details[{{ $detailIndex }}][title]"
                                                                            class="form-control"
                                                                            placeholder="عنوان التفصيلة"
                                                                            value="{{ $detail->title }}">
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <input readonly type="number" step="0.01"
                                                                            name="price_details[{{ $detailIndex }}][amount]"
                                                                            class="form-control" placeholder="السعر"
                                                                            value="{{ $detail->amount }}" required
                                                                            oninput="updateTotalPrice()">
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <select readonly
                                                                            name="price_details[{{ $detailIndex }}][piece_resource]"
                                                                            class="form-control">
                                                                            @foreach ($piece_resources as $resource)
                                                                                <option @selected($detail->piece_resource == $resource->id)
                                                                                    value="{{ $resource->id }}">
                                                                                    {{ $resource->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    {{-- <div class="col-1">
                                                                        <button type="button"
                                                                            class="btn btn-danger btn-sm"
                                                                            onclick="removePriceDetail(this)">-</button>
                                                                    </div> --}}
                                                                </div>
                                                                @php $detailIndex++; @endphp
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <script>
                                                        let detailIndex = {{ $detailIndex ?? 0 }};

                                                        function addPriceDetail() {
                                                            const wrapper = document.getElementById('price-details-wrapper');
                                                            const detailDiv = document.createElement('div');
                                                            detailDiv.classList.add('form-row', 'mb-2');
                                                            detailDiv.innerHTML = `
                                                                <div class="col-4">
                                                                    <input type="text" name="price_details[${detailIndex}][title]" class="form-control" placeholder="عنوان التفصيلة">
                                                                </div>
                                                                <div class="col-4">
                                                                    <input type="number" step="0.01" name="price_details[${detailIndex}][amount]" class="form-control"
                                                                           placeholder="السعر" required oninput="updateTotalPrice()">
                                                                </div>
                                                                <div class="col-3">
                                                                    <select name="price_details[${detailIndex}][piece_resource]" class="form-control">
                                                                        @foreach ($piece_resources as $resource)
                                                                            <option @selected(old('piece_resource') == $resource->id)
                                                                                value="{{ $resource->id }}">
                                                                                {{ $resource->name }}</option>
                                                                        @endforeach
                                                                    </select>
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
                                                    <div class="col-md-6">
                                                        <label for="price"> تاريخ ووقت التسليم <span
                                                                class="required_span"> * </span> </label>
                                                        <div class="justify-between d-flex flex-column">
                                                            <div class="form-group" style="min-width: 100%">

                                                                <input disabled type="date" name="date_delivery"
                                                                    class="form-control"
                                                                    value="{{ $invoice->date_delivery }}"
                                                                    class="form-control">
                                                            </div>
                                                            <div class="form-group" style="min-width: 100%">
                                                                <input disabled type="time" name="time_delivery"
                                                                    value="{{ $invoice->time_delivery }}"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="price"> التوقيع </label>
                                                        <div class="form-group">
                                                            <img width="100" height="100"
                                                                src="{{ asset('assets/uploads/invoices_files/' . $invoice->signature) }}"
                                                                alt="">
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="name"> رمز الجهاز </label>
                                                            <input disabled type="text" id="device_text_password"
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
                                                                <input disabled type="number" min="1"
                                                                    max="12" name="pattern[]"
                                                                    value="{{ $storedPattern[0] ?? '' }}">
                                                                <input disabled type="number" min="1"
                                                                    max="12" name="pattern[]"
                                                                    value="{{ $storedPattern[1] ?? '' }}">
                                                                <input disabled type="number" min="1"
                                                                    max="12" name="pattern[]"
                                                                    value="{{ $storedPattern[2] ?? '' }}">
                                                            </div>
                                                            <div class="d-flex">
                                                                <input disabled type="number" min="1"
                                                                    max="12" name="pattern[]"
                                                                    value="{{ $storedPattern[3] ?? '' }}">
                                                                <input disabled type="number" min="1"
                                                                    max="12" name="pattern[]"
                                                                    value="{{ $storedPattern[4] ?? '' }}">
                                                                <input disabled type="number" min="1"
                                                                    max="12" name="pattern[]"
                                                                    value="{{ $storedPattern[5] ?? '' }}">
                                                            </div>
                                                            <div class="d-flex">
                                                                <input disabled type="number" min="1"
                                                                    max="12" name="pattern[]"
                                                                    value="{{ $storedPattern[6] ?? '' }}">
                                                                <input disabled type="number" min="1"
                                                                    max="12" name="pattern[]"
                                                                    value="{{ $storedPattern[7] ?? '' }}">
                                                                <input disabled type="number" min="1"
                                                                    max="12" name="pattern[]"
                                                                    value="{{ $storedPattern[8] ?? '' }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label for="price"> مصدر القطعة <span
                                                                    class="required_span"> *
                                                                </span> </label>
                                                            <select name="piece_resource" id=""
                                                                class="form-control">
                                                                <option value="" selected> -- حدد مصدر
                                                                    القطعة -- </option>
                                                                @foreach ($piece_resources as $resource)
                                                                    <option @selected($invoice->piece_resource == $resource->id)
                                                                        value="{{ $resource->id }}">
                                                                        {{ $resource->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div> --}}
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="title"> ملاحظات فني الصيانة </label>
                                                            <textarea readonly name="tech_notes" id="" class="form-control">{{ $invoice->tech_notes }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="price"> حالة الجهاز </label>
                                                            <select readonly name="status" id=""
                                                                class="form-control">
                                                                <option
                                                                    {{ $invoice->status == 'تحت الصيانة' ? 'selected' : '' }}
                                                                    value="تحت الصيانة">تحت الصيانة</option>
                                                                <option
                                                                    {{ $invoice->status == 'تم الاصلاح' ? 'selected' : '' }}
                                                                    value="تم الاصلاح"> تم الاصلاح </option>
                                                                <option
                                                                    {{ $invoice->status == 'لم يتم الاصلاح' ? 'selected' : '' }}
                                                                    value="لم يتم الاصلاح">لم يتم الاصلاح</option>
                                                                <option {{ $invoice->status == 'معلق' ? 'selected' : '' }}
                                                                    value="معلق">معلق</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            {{-- <div class="row">
                                                <div class="col-12">
                                                    <h5
                                                        style="font-weight: bold;color: #000;border-bottom: 1px dashed #ccc;margin-bottom: 10px;padding-bottom: 10px;">
                                                        اضافة مرفق </h5>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="address"> صورة المرفق </label>
                                                        <input type="file" name="file" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="address"> اضف عنوان للمرفق </label>
                                                        <input type="text" id="title" class="form-control"
                                                            placeholder="" name="file_title"
                                                            value="{{ old('file_title') }}">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="price"> سعر المرفق </label>
                                                        <input type="number" step="0.01" id="price"
                                                            class="form-control" placeholder="" name="file_price"
                                                            value="{{ old('file_price') }}">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="address"> تفاصيل اضافية عن المرفق * </span> </label>
                                                        <textarea name="file_description" id="" class="form-control">{{ old('file_description') }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <!------------------------------ Add More Photo Status ------------------------------------------>
                                            <div class="row">
                                                <div class="col-12">
                                                    <h5
                                                        style="font-weight: bold;color: #000;border-bottom: 1px dashed #ccc;margin-bottom: 10px;padding-bottom: 10px;">
                                                        اضافة صور لحالة الجهاز </h5>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="address"> صورة </label>
                                                        <input type="file" name="file_status" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="address"> عنوان الصورة </label>
                                                        <input type="text" id="title" class="form-control"
                                                            placeholder="" name="file_status_title"
                                                            value="{{ old('file_status_title') }}">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="address"> تفاصيل اضافية عن الصورة </span> </label>
                                                        <textarea name="file_status_description" id="" class="form-control">{{ old('file_status_description') }}</textarea>
                                                    </div>
                                                </div>
                                            </div> --}}
                                            <!------------------------------- And Add More Photos Status -------------------------------------->
                                            {{-- <div class="form-actions">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="la la-check-square-o"></i> حفظ
                                                </button>
                                                <p id="loadingMessage" class="mt-2 text-info" style="display: none;">⏳
                                                    جاري رفع البيانات، الرجاء الانتظار...</p>
                                            </div>
                                            --}}
                                        </form>
                                        <script>
                                            document.getElementById("invoice-form").addEventListener("submit", function(e) {
                                                e.preventDefault();

                                                let submitBtn = this.querySelector('button[type="submit"]');
                                                let loadingMessage = document.getElementById('loadingMessage');

                                                submitBtn.disabled = true;
                                                submitBtn.innerHTML = '<i class="la la-spinner la-spin"></i> جاري الحفظ...';

                                                loadingMessage.style.display = 'block';
                                                this.submit();

                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"> حالة التواصل مع العميل </h4>
                                    <br>
                                    <form id="clientConnectForm"
                                        action="{{ route('dashboard.tech_invoices.client-connect', $invoice->id) }}"
                                        method="post">
                                        @csrf
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <select name="client_connect" id="" class="form-control">
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
                                                <textarea name="client_connect_notes" id="" class="form-control">{{ $invoice->client_connect_notes }}</textarea>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="la la-check-square-o"></i> حفظ
                                            </button>
                                            <br>
                                            <p id="loadingMessage" class="mt-2 text-info" style="display: none;">⏳
                                                جاري رفع البيانات، الرجاء الانتظار...</p>
                                        </div>
                                    </form>
                                    <script>
                                        document.getElementById("clientConnectForm").addEventListener("submit", function(e) {
                                            e.preventDefault();

                                            let submitBtn = this.querySelector('button[type="submit"]');
                                            let loadingMessage = document.getElementById('loadingMessage');

                                            submitBtn.disabled = true;
                                            submitBtn.innerHTML = '<i class="la la-spinner la-spin"></i> جاري الحفظ...';

                                            loadingMessage.style.display = 'block';
                                            this.submit();

                                        });
                                    </script>
                                </div>
                            </div> --}}


                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title" id="basic-layout-form"> المرفقات </h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        {{-- <h5> اضافة مرفق </h5>
                                        <form id="addattachmentForm"
                                            action="{{ route('dashboard.tech_invoices.addfile', $invoice->id) }}"
                                            method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">

                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="address"> صورة المرفق <span class="required_span"> *
                                                            </span> </label>
                                                        <input required type="file" name="file"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="address"> اضف عنوان للمرفق <span
                                                                class="required_span">
                                                                * </span> </label>
                                                        <input required type="text" id="title"
                                                            class="form-control" placeholder="" name="title"
                                                            value="{{ old('title') }}">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="price"> سعر المرفق <span class="required_span"> *
                                                            </span> </label>
                                                        <input required type="number" step="0.01" id="price"
                                                            class="form-control" placeholder="" name="price"
                                                            value="{{ old('price') }}">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="address"> تفاصيل اضافية عن المرفق <span
                                                                class="required_span"> * </span> </label>
                                                        <textarea required name="description" id="" class="form-control">{{ old('description') }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-actions">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    اضافة المرفق <i class="la la-plus"></i>
                                                </button>
                                                <p id="loadingMessage" class="mt-2 text-info" style="display: none;">⏳
                                                    جاري رفع البيانات، الرجاء الانتظار...</p>
                                            </div>
                                        </form> --}}
                                        {{-- <script>
                                            document.getElementById("addattachmentForm").addEventListener("submit", function(e) {
                                                e.preventDefault();

                                                let submitBtn = this.querySelector('button[type="submit"]');
                                                let loadingMessage = document.getElementById('loadingMessage');

                                                submitBtn.disabled = true;
                                                submitBtn.innerHTML = '<i class="la la-spinner la-spin"></i> جاري الحفظ...';

                                                loadingMessage.style.display = 'block';
                                                this.submit();

                                            });
                                        </script> --}}
                                        <div class="row">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th>
                                                        المرفق
                                                    </th>
                                                    <th>
                                                        عنوان المرفق
                                                    </th>
                                                    <th>
                                                        السعر
                                                    </th>
                                                    <th>
                                                        تفاصيل اضافية
                                                    </th>
                                                </tr>
                                                @forelse ($invoice->files as $file)
                                                    @if ($file->price > 0)
                                                        <tr>
                                                            <td>
                                                                <a target="_blank"
                                                                    href="{{ asset('assets/uploads/invoices_files/' . $file['image']) }}">
                                                                    <img width="100" height="100" class="file_image"
                                                                        src="{{ asset('assets/uploads/invoices_files/' . $file['image']) }}"
                                                                        alt="Card image cap">
                                                                </a>
                                                            </td>
                                                            <td>
                                                                {{ $file->title }}
                                                            </td>
                                                            <td>
                                                                {{ number_format($file->price, 2) }} ريال
                                                            </td>
                                                            <td>
                                                                {{ $file->description }}
                                                            </td>
                                                            <td>
                                                                <form
                                                                    action="{{ route('dashboard.invoices.delete_file', $file['id']) }}"
                                                                    method="POST">
                                                                    @csrf

                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @empty
                                                    لا يوجد مرفقات
                                                @endforelse
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title" id="basic-layout-form"> صور حالة الجهاز </h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">

                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th> الصورة </th>
                                                        <th> العنوان </th>
                                                        <th> التفاصيل </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($invoice->files as $file)
                                                        @if ($file->price == 0)
                                                            <tr>
                                                                <td>
                                                                    <a target="_blank"
                                                                        href="{{ asset('assets/uploads/invoices_files/' . $file['image']) }}">
                                                                        <img width="100" height="100"
                                                                            class="file_image"
                                                                            src="{{ asset('assets/uploads/invoices_files/' . $file['image']) }}"
                                                                            alt="Card image cap">
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    {{ $file->title }}
                                                                </td>
                                                                <td>
                                                                    {{ $file->description }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @empty
                                                        لا يوجد صور لحالة الجهاز
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

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
@endsection
@section('js')
    <script src="{{ asset('assets/admin/') }}/vendors/js/forms/icheck/icheck.min.js" type="text/javascript"></script>
    <script src="{{ asset('assets/admin/') }}/js/scripts/forms/checkbox-radio.js" type="text/javascript"></script>

@endsection

@extends('dashboard.layouts.public_app')
@section('title', ' طباعة الفاتورة ')
@section('css')
    <style>
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

        .table td input[type='radio'] {
            min-width: 50px;
        }
    </style>
@endsection
@section('content')
    <div class="app-content content" style="margin-right:0px">
        <div class="content-wrapper public_invoice">
            <div class="content-body">
                <input type="hidden" value="{{ $invoice->name }}" id="customername">
                <section class="card">
                    <div id="invoice-template" class="card-body">
                        <!-- Invoice Company Details -->
                        <div id="invoice-company-details d-flex" class="row invoice-company-details">
                            <div class="text-left col-md-6 col-6">
                                <div class="media">
                                    <img width="" src="{{ asset('assets/admin/') }}/images/logo.png"
                                        alt="company logo" class="" />
                                </div>
                            </div>
                            <div class="text-right col-md-6 col-6">
                                <h2> رقم الفاتورة </h2>
                                <p class="pb-1"> INV-{{ $invoice->id }}</p>
                            </div>
                        </div>
                        <!--/ Invoice Company Details -->
                        <!-- Invoice Customer Details -->
                        <div id="invoice-customer-details" class="pt-2 row">
                            <div class="text-left col-md-6 col-6">
                                <p class="text-muted"> الي السيد / ة </p>
                                <ul class="px-0 list-unstyled">
                                    <li class="text-bold-800"> {{ $invoice->name }}</li>
                                    <li> {{ $invoice->phone }} </li>
                                </ul>
                            </div>
                            <div class="text-right col-md-6 col-6">
                                <p>
                                    <span class="text-muted"> تاريخ الفاتورة :</span> {{ $invoice->created_at }}
                                </p>
                                <p>
                                    <span class="text-muted"> تاريخ ووقت التسليم :</span> {{ $invoice->date_delivery }} -
                                    {{ $invoice->time_delivery }}
                                </p>
                            </div>
                        </div>
                        <!--/ Invoice Customer Details -->

                        <!---------------  Start Invoice Checks ---------------->
                        <div class="">
                            <h4 class="card-title" id="basic-layout-form"> <strong> نتائج فحص الجهاز </strong> </h4>
                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                        </div>
                        <!--################### Start Add ChecksResults ###################-->
                        <div class="row" id="full_check"
                            style="{{ $invoice->checkout_type === 'فحص كامل' ? 'display: block' : 'display: none' }}">
                            <h5> فحص الجهاز <span class="required_span"> * </span> </h5>
                            <div class="table-responsive">
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
                                                    <input type="hidden" name="work_{{ $check->id }}" value="">
                                                    <input type="hidden" name="problem_id[]" value="{{ $check->id }}">
                                                    <input readonly type="text" value="{{ $check->name }}"
                                                        class="form-control" name="check_problem_name[]">
                                                </td>
                                                <td>
                                                    <input disabled readonly type="radio" value="1"
                                                        class="form-control" name="work_{{ $check->id }}"
                                                        {{ isset($checkResult) && $checkResult->work == 1 ? 'checked' : '' }}>
                                                </td>
                                                <td>
                                                    <input disabled readonly type="radio" value="0"
                                                        class="form-control" name="work_{{ $check->id }}"
                                                        {{ isset($checkResult) && $checkResult->work == 0 ? 'checked' : '' }}>
                                                </td>
                                                <td>
                                                    <input disabled readonly type="text"
                                                        value="{{ $checkResult->notes ?? '' }}" class="form-control"
                                                        name="notes[]">
                                                </td>
                                                <td>
                                                    <input disabled readonly type="text"
                                                        value="{{ $checkResult->after_check ?? '' }}" class="form-control"
                                                        name="after_check[]">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!--################### End Add ChecksResults #####################-->
                        <!--################### Start Speed Device Check  ###################-->
                        <div class="row" id="speed_check"
                            style="{{ $invoice->checkout_type === 'فحص جهاز سريع' ? 'display: block' : 'display: none' }}">
                            <h5> جهاز سريع <span class="required_span"> * </span> </h5>
                            <div class="table-responsive">
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
                                                    <input type="hidden" name="speed_id[]" value="{{ $speed->id }}">
                                                    <input readonly type="text" value="{{ $speed->name }}"
                                                        class="form-control" name="check_speed_name[]">
                                                </td>

                                                <td>
                                                    <input disabled readonly type="radio" value="1"
                                                        class="form-control" name="speedwork_{{ $speed->id }}"
                                                        {{ isset($speedResult) && $speedResult->work == 1 ? 'checked' : '' }}
                                                        </td>
                                                <td>
                                                    <input disabled readonly type="radio" value="0"
                                                        class="form-control" name="speedwork_{{ $speed->id }}"
                                                        {{ isset($speedResult) && $speedResult->work == 0 ? 'checked' : '' }}>
                                                </td>
                                                <td>
                                                    <input disabled readonly type="text"
                                                        value="{{ $speedResult->notes ?? '' }}" class="form-control"
                                                        name="speed_notes[]">
                                                </td>
                                                <td>
                                                    <input disabled readonly type="text"
                                                        value="{{ $speedResult->after_check ?? '' }}" class="form-control"
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
                        <div class="row" id="programe_check"
                            style="{{ $invoice->checkout_type === 'فحص جهاز برمجة' ? 'display: block' : 'display: none' }}">
                            <h5> جهاز برمجة <span class="required_span"> * </span> </h5>
                            <div class="table-responsive">
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
                                                    <input readonly type="text" value="{{ $programe->name }}"
                                                        class="form-control" name="check_programe_name[]">
                                                </td>
                                                <td>
                                                    <input disabled readonly type="radio" value="1"
                                                        class="form-control" name="programework_{{ $programe->id }}[]"
                                                        {{ isset($programeResult) && $programeResult->work == 1 ? 'checked' : '' }}>
                                                </td>
                                                <td>
                                                    <input disabled readonly type="radio" value="0"
                                                        class="form-control" name="programework_{{ $programe->id }}[]"
                                                        {{ isset($programeResult) && $programeResult->work == 0 ? 'checked' : '' }}>
                                                </td>
                                                <td>
                                                    <input disabled readonly type="text"
                                                        value="{{ $programeResult->notes ?? '' }}" class="form-control"
                                                        name="programe_notes[]">
                                                </td>
                                                <td>
                                                    <input disabled readonly type="text"
                                                        value="{{ $programeResult->after_check ?? '' }}"
                                                        class="form-control" name="after_check_programe[]">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        @php
                            $selectedChecks = json_decode($invoice->invoice_more_checks, true) ?? [];
                        @endphp
                        <div class="row">
                            @foreach ($invoice_more_checks as $invoice_more_check)
                                <div class="col-6">
                                    <div class="skin skin-square">
                                        <fieldset>
                                            <input type="checkbox" disabled
                                                {{ in_array($invoice_more_check->id, $selectedChecks) ? 'checked' : '' }}
                                                id="inputmorecheck-{{ $invoice_more_check->id }}"
                                                name="invoice_more_checks[]" value="{{ $invoice_more_check->id }}">
                                            <label for="inputmorecheck-{{ $invoice_more_check->id }}">
                                                {{ $invoice_more_check->name }}
                                            </label>
                                        </fieldset>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="title"> حدد الاعطال <span class="required_span">
                                        * </span> </label>
                                <div class="skin skin-square">
                                    <!-- ########## Start All Check ####################### -->
                                    <div class="col-md-12 col-sm-12 problem_check_box"
                                        style="{{ $invoice->checkout_type === 'فحص كامل' ? 'display: flex; flex-wrap: wrap; word-wrap: break-word;' : 'display: none' }}"
                                        id="problem_all_check">
                                        @foreach ($problems as $problem)
                                            <fieldset style="min-width:120px">
                                                <input
                                                    {{ in_array($problem->name, json_decode($invoice->problems)) ? 'checked' : '' }}
                                                    type="checkbox" id="input-{{ $problem->id }}" name="problems[]"
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
                                            <fieldset style="min-width:120px">
                                                <input
                                                    {{ in_array($programe_problem->name, json_decode($invoice->problems)) ? 'checked' : '' }}
                                                    type="checkbox" id="inputprograme-{{ $programe_problem->id }}"
                                                    name="problems[]" value="{{ $programe_problem->name }}">
                                                <label for="inputprograme-{{ $programe_problem->id }}">
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
                                            <fieldset style="min-width:120px">
                                                <input
                                                    {{ in_array($speed_problem->name, json_decode($invoice->problems)) ? 'checked' : '' }}
                                                    type="checkbox" id="inputspeed-{{ $speed_problem->id }}"
                                                    name="problems[]" value="{{ $speed_problem->name }}">
                                                <label for="inputspeed-{{ $speed_problem->id }}">
                                                    {{ $speed_problem->name }} </label>
                                            </fieldset>
                                        @endforeach
                                    </div>
                                    <!-------############# End  Programe Check ##################-------------->


                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="price"> مصدر القطعة <span class="required_span"> *
                                        </span> </label>
                                    <select disabled name="piece_resource" id="" class="form-control">
                                        <option value="" selected disabled> -- حدد مصدر
                                            القطعة -- </option>
                                        @foreach ($piece_resources as $resource)
                                            <option @selected($invoice->piece_resource == $resource->id) value="{{ $resource->id }}">
                                                {{ $resource->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <br>

                        <!-------------- Signutre And Images Files -------------->
                        <div class="row">
                            <div class="table-responsive col-sm-12">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th> توقيع العميل </th>
                                            <th class="text-right"> صور حالة الجهاز </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <img width="100" height="100"
                                                    src="{{ asset('assets/uploads/invoices_files/' . $invoice->signature) }}"
                                                    alt="">
                                            </td>
                                            <td class="text-right">
                                                <div class="">
                                                    <table class="table table-borderd">
                                                        <thead>
                                                            <tr>
                                                                <th>
                                                                    المرفق
                                                                </th>
                                                                {{-- <th>
                                                                    عنوان المرفق
                                                                </th>
                                                                <th>
                                                                    تفاصيل اضافية
                                                                </th> --}}
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($invoice->files as $file)
                                                                @if ($file->price == 0)
                                                                    <tr>
                                                                        <td>
                                                                            <a href="{{ asset('assets/uploads/invoices_files/' . $file['image']) }}"
                                                                                target="_blank">
                                                                                <img width="100" height="100"
                                                                                    src="{{ asset('assets/uploads/invoices_files/' . $file['image']) }}"
                                                                                    alt="">
                                                                            </a>
                                                                        </td>
                                                                        {{-- <td>
                                                                            {{ $file['title'] }}
                                                                        </td>
                                                                        <td>
                                                                            {{ $file['details'] }}
                                                                        </td> --}}
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-------------- End Signute ---------->

                        @php
                            $settings = App\Models\dashboard\Setting::first();
                        @endphp

                        <!--################### End Add ChecksResults #####################-->
                        <!---------------- End Invoice Checks ------------>
                        <!-- Invoice Items Details -->
                        <div id="invoice-items-details" class="pt-2">
                            <div class="row">
                                <div class="table-responsive col-sm-12">
                                    <table class="table">
                                        <thead>
                                            <tr>

                                                <th> الجهاز </th>
                                                <th class="text-right">العطل </th>
                                                <th class="text-right">ملاحظات </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>

                                                <td>
                                                    <p>{{ $invoice->title }}</p>
                                                </td>
                                                <td class="text-right">
                                                    @foreach (json_decode($invoice->problems) as $problem)
                                                        <span class=""> {{ $problem }}
                                                        </span> -
                                                    @endforeach
                                                </td>
                                                <td class="text-right">
                                                    {{ $invoice->description }}
                                                    <hr>
                                                    @if ($invoice->tech_notes)
                                                        <b> ملاحظات فنية الصيانة </b>
                                                        {{ $invoice->tech_notes }}
                                                    @else
                                                        <b> ملاحظات فنية الصيانة </b>
                                                        لا يوجد
                                                    @endif
                                                </td>

                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="text-center col-md-7 col-sm-12 text-md-left">
                                    <p class="lead"> للاستفسارات :</p>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <table class="table table-borderless table-sm">
                                                <tbody>
                                                    <tr>
                                                        <td> قسم الصيانة :</td>
                                                        <td class="text-right"> {{ $settings->phone1 }} </td>
                                                    </tr>
                                                    <tr>
                                                        <td> الإدارة :</td>
                                                        <td class="text-right"> {{ $settings->phone2 }} </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 col-12">
                                    <p class="lead">المبلغ الكلي المستحق</p>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    {{-- <td>المبلغ المدخل (شامل الضريبة)</td> --}}
                                                    <td> المبلغ الاولي </td>
                                                    <td class="text-right">{{ number_format($invoice->price, 2) }} ريال
                                                    </td>
                                                </tr>

                                                @php
                                                    $sub_total = 0;
                                                @endphp
                                                @if ($invoice->files->count() > 0)
                                                    @foreach ($invoice->files as $file)
                                                        @php
                                                            $sub_total += $file->price;
                                                        @endphp
                                                        @if ($file->price != 0)
                                                            <tr>
                                                                <td>{{ $file->title }}</td>
                                                                <td class="text-right">
                                                                    {{ number_format($file->price, 2) }}
                                                                    ريال</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @endif

                                                @php
                                                    $total_price = $invoice->price + $sub_total;
                                                    $base_price = $total_price / 1.15; // استخراج المبلغ الأساسي قبل الضريبة
                                                    $vat = $total_price - $base_price; // حساب قيمة الضريبة المضافة
                                                @endphp

                                                {{-- <tr>
                                                    <td class="text-bold-800">المبلغ الأساسي (قبل الضريبة)</td>
                                                    <td class="text-right text-bold-800">
                                                        {{ number_format($base_price, 2) }} ريال</td>
                                                </tr>

                                                <tr>
                                                    <td>ضريبة القيمة المضافة (15%)</td>
                                                    <td class="text-right text-danger">{{ number_format($vat, 2) }} ريال
                                                    </td>
                                                </tr> --}}

                                                <tr>
                                                    <td class="text-bold-800">الإجمالي (شامل الضريبة)</td>
                                                    <td class="text-right text-bold-800">
                                                        {{ number_format($total_price, 2) }} ريال</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- Invoice Footer -->
                        <div id="invoice-footer">
                            <div class="row">
                                <div class="col-md-7 col-sm-12">
                                    <h6> الشروط والاحكام </h6>
                                    <p> يجب إحضار الفاتورة عند استلام الجهاز. </p>
                                    <p> <a target="_blank" href="{{ url('/dashboard/terms') }}"> قراءة الشروط والاحكام
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

@endsection


<style>
    @media print {
        footer {
            display: none;
        }

        .header-navbar .navbar-wrapper,
        body.vertical-layout.vertical-menu.menu-expanded .main-menu,
        .content-wrapper .content-header {
            display: none;
            width: 0
        }

        body.vertical-layout.vertical-menu.menu-expanded .content {
            margin-right: 0 !important;
        }

        @page {
            margin: 0;
            padding: 0;
            background-color: #fff
        }

        html body .content .content-wrapper {
            background-color: #fff;
        }

        .print_button {
            display: none !important;
        }
    }
</style>

<script>
    function setPrintTitle() {
        // تعيين عنوان مخصص للصفحة ليتم طباعته
        document.title = document.getElementById('customername').value;

        // التأكد من أن العنوان الجديد قد تم تعيينه بشكل صحيح
        console.log("تم تعيين عنوان مخصص للطباعة: " + document.title);

        // إضافة استماع لحدث اكتمال الطباعة لاستعادة العنوان الأصلي بعد الطباعة
        window.onafterprint = function() {
            document.title = document.getElementById('customername').value;
            console.log("استعادة عنوان الصفحة الأصلي: " + document.title);
        };
    }
</script>

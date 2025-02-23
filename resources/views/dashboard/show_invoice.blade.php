@extends('dashboard.layouts.public_app')
@section('title', ' طباعة الفاتورة ')
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
                            <h4 class="card-title" id="basic-layout-form"> <strong> نتائج فحص الجهاز </strong> </h4>
                            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                        </div>
                        <!--################### Start Add ChecksResults ###################-->
                        <div class="row table-responsive">
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
                                                <p>
                                                    {{ $check->name }}
                                                </p>
                                            </td>
                                            <td>
                                                <input readonly disabled type="radio" value="1" class="form-control"
                                                    name="work_{{ $check->id }}[]"
                                                    {{ isset($checkResult) && $checkResult->work == 1 ? 'checked' : '' }}>
                                            </td>
                                            <td>
                                                <input readonly disabled type="radio" value="0" class="form-control"
                                                    name="work_{{ $check->id }}[]"
                                                    {{ isset($checkResult) && $checkResult->work == 0 ? 'checked' : '' }}>
                                            </td>
                                            <td>
                                                <p>
                                                    {{ $checkResult->notes ?? '' }}
                                                </p>
                                            </td>
                                            <td>
                                                <p>
                                                    {{ $checkResult->after_check ?? '' }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-------------- Signutre And Images Files -------------->
                        <div class="row">
                            <div class="table-responsive col-sm-12">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th> توقيع العميل </th>
                                            <th class="text-right"> مرفقات الجهاز </th>

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
                                                <div class="flex-row d-flex justify-content-center">
                                                    @foreach ($invoice->files as $file)
                                                        <img style="border: 1px solid #ccc;border-radius: 10px;padding: 2px;margin-left: 5px"
                                                            width="100px" height="100px" class="img-border"
                                                            src="{{ asset('assets/uploads/invoices_files/' . $file['image']) }}"
                                                            alt="">
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-------------- End Signute ---------->



                        <!--################### End Add ChecksResults #####################-->
                        <!---------------- End Invoice Checks ------------>
                        <!-- Invoice Items Details -->
                        <div id="invoice-items-details" class="pt-2">
                            <div class="row">
                                <div class="table-responsive col-sm-12">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th> الجهاز </th>
                                                <th class="text-right">العطل </th>
                                                <th class="text-right">ملاحظات </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th scope="row">1</th>
                                                <td>
                                                    <p>{{ $invoice->title }}</p>
                                                </td>
                                                <td class="text-right">
                                                    @foreach (json_decode($invoice->problems) as $problem)
                                                        <span class=""> {{ $problem }}
                                                        </span> -
                                                    @endforeach
                                                </td>
                                                <td class="text-right">{{ $invoice->description }}</td>

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
                                                        <td class="text-right"> 0507170175 </td>
                                                    </tr>
                                                    <tr>
                                                        <td> الإدارة :</td>
                                                        <td class="text-right"> 0577894369 </td>
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
                                                        <tr>
                                                            <td>{{ $file->title }}</td>
                                                            <td class="text-right">{{ number_format($file->price, 2) }}
                                                                ريال</td>
                                                        </tr>
                                                    @endforeach
                                                @endif

                                                @php
                                                    $total_price = $invoice->price + $sub_total;
                                                    $base_price = $total_price / 1.15; // استخراج المبلغ الأساسي قبل الضريبة
                                                    $vat = $total_price - $base_price; // حساب قيمة الضريبة المضافة
                                                @endphp

                                                <tr>
                                                    <td class="text-bold-800">المبلغ الأساسي (قبل الضريبة)</td>
                                                    <td class="text-right text-bold-800">
                                                        {{ number_format($base_price, 2) }} ريال</td>
                                                </tr>

                                                <tr>
                                                    <td>ضريبة القيمة المضافة (15%)</td>
                                                    <td class="text-right text-danger">{{ number_format($vat, 2) }} ريال
                                                    </td>
                                                </tr>

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

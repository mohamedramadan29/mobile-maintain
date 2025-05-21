@extends('dashboard.layouts.app')
@section('title', ' طباعة الفاتورة ')
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="mb-2 content-header-left col-md-6 col-12 breadcrumb-new">
                    <h3 class="mb-0 content-header-title d-inline-block"> طباعة الفاتورة </h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.invoices.index') }}">الفواتير</a>
                                </li>
                                <li class="breadcrumb-item active"> طباعة الفاتورة
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <input type="hidden" value="{{ $invoice->name }}" id="customername">
                <section class="card">
                    <div id="invoice-template" class="card-body">
                        <!-- Invoice Company Details -->
                        <div id="invoice-company-details d-flex" class="row">
                            <div class="text-left col-md-6 col-sm-6">
                                <div class="media">
                                    <img width="200px" src="{{ asset('assets/admin/') }}/images/logo.png"
                                        alt="company logo" class="" />
                                </div>
                            </div>
                            <div class="text-right col-md-6 col-sm-6">
                                <h2> رقم الفاتورة </h2>
                                <p class="pb-3"> INV-{{ $invoice->id }}</p>

                            </div>
                        </div>
                        <!--/ Invoice Company Details -->
                        <!-- Invoice Customer Details -->
                        <div id="invoice-customer-details" class="pt-2 row">
                            <div class="text-left col-md-6 col-sm-6">
                                <p class="text-muted"> الي السيد / ة </p>
                                <ul class="px-0 list-unstyled">
                                    <li class="text-bold-800"> {{ $invoice->name }}</li>
                                    <li> {{ $invoice->phone }} </li>
                                </ul>
                            </div>
                            <div class="text-right col-md-6 col-sm-6">
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

                            <!-------------- Signutre And Images Files -------------->
                            <div class="row">
                                <div class="table-responsive col-sm-12">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th> توقيع العميل </th>
                                                <th class="text-right"> صور حالة الجهاز والمرفقات </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <img width="100" height="100"
                                                        src="{{ asset('assets/uploads/invoices_files/' . $invoice->signature) }}"
                                                        alt="">
                                                </td>
                                                <td>
                                                    <div class="">
                                                        <p>المرفقات</p>
                                                        <table class="table table-borderd">
                                                            <thead>
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
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($invoice->files as $file)
                                                                    @if ($file->price > 0)
                                                                        <tr>
                                                                            <td>
                                                                                <a href="{{ asset('assets/uploads/invoices_files/' . $file['image']) }}"
                                                                                    target="_blank">
                                                                                    <img width="100" height="100"
                                                                                        src="{{ asset('assets/uploads/invoices_files/' . $file['image']) }}"
                                                                                        alt="">
                                                                                </a>
                                                                            </td>
                                                                            <td>
                                                                                {{ $file['title'] }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $file['price'] }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $file['details'] }}
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                        <p> صور حالة الجهاز </p>
                                                        <table class="table table-borderd">
                                                            <thead>
                                                                <tr>
                                                                    <th>
                                                                        المرفق
                                                                    </th>
                                                                    <th>
                                                                        عنوان المرفق
                                                                    </th>
                                                                    <th>
                                                                        تفاصيل اضافية
                                                                    </th>
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
                                                                            <td>
                                                                                {{ $file['title'] }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $file['details'] }}
                                                                            </td>
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
                                                @php
                                                    $total_details = 0;
                                                @endphp
                                                @foreach ($invoice->priceDetails as $detail)
                                                    @php
                                                        $total_details += $detail->amount;
                                                    @endphp
                                                    <tr>
                                                        <td> {{ $detail->title }} </td>
                                                        <td class="text-right">{{ number_format($detail->amount, 2) }} ريال
                                                        </td>
                                                        <td> {{ $detail->pieceResourse->name ?? 'غير محدد' }} </td>
                                                    </tr>
                                                @endforeach
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
                                                                <td class="text-right">{{ number_format($file->price, 2) }}
                                                                    ريال</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @endif
                                                @php
                                                    $total_price = $sub_total + $total_details;
                                                    //  $total_price = $invoice->price + $sub_total;
                                                    // $base_price = $total_price / 1.15; // استخراج المبلغ الأساسي قبل الضريبة
                                                    //  $vat = $total_price - $base_price; // حساب قيمة الضريبة المضافة
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
                                    <p> <a target="_blank" href="{{ url('/dashboard/terms') }}"> قراءة الشروط والاحكام </a>
                                    </p>
                                </div>
                                <div class="text-center col-md-5 col-sm-12">
                                    <button onclick="setPrintTitle(); window.print();" type="button"
                                        class="my-1 btn btn-info btn-lg print_button"><i class="la la-paper-plane-o"></i>
                                        طباعة </button>
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

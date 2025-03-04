@extends('dashboard.layouts.app')

@section('title', ' تفاصيل الفاتورة ')
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
                    <h3 class="mb-0 content-header-title d-inline-block"> تفاصيل الفاتورة </h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.tech_invoices.available') }}">
                                        الفواتير المتاحة </a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#"> تفاصيل الفاتورة </a>
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
                                    <h4 class="card-title" id="basic-layout-form"> تفاصيل الفاتورة </h4>

                                    <h4 class="card-title" id="basic-layout-form"> -- نتائج فحص الجهاز -- </h4>
                                    <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                </div>
                                <!--################### Start Add ChecksResults ###################-->
                                <div class="row">

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
                                                        <input readonly disabled type="hidden" name="problem_id[]"
                                                            value="{{ $check->id }}">
                                                        <input readonly type="text" value="{{ $check->name }}"
                                                            class="form-control" name="check_problem_name[]">
                                                    </td>
                                                    <td>
                                                        <input readonly disabled type="radio" value="1"
                                                            class="form-control" name="work_{{ $check->id }}[]"
                                                            {{ isset($checkResult) && $checkResult->work == 1 ? 'checked' : '' }}>
                                                    </td>
                                                    <td>
                                                        <input readonly disabled type="radio" value="0"
                                                            class="form-control" name="work_{{ $check->id }}[]"
                                                            {{ isset($checkResult) && $checkResult->work == 0 ? 'checked' : '' }}>
                                                    </td>
                                                    <td>
                                                        <input readonly disabled type="text"
                                                            value="{{ $checkResult->notes ?? '' }}" class="form-control"
                                                            name="notes[]">
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
                                <!--################### Start Speed Device Check  ###################-->
                                <div class="row">
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
                                                        <input readonly disabled type="text" value="{{ $speed->name }}"
                                                            class="form-control" name="check_speed_name[]">
                                                    </td>

                                                    <td>
                                                        <input readonly disabled type="radio" value="1"
                                                            class="form-control" name="speedwork_{{ $speed->id }}"
                                                            {{ isset($speedResult) && $speedResult->work == 1 ? 'checked' : '' }}
                                                            </td>
                                                    <td>
                                                        <input readonly disabled type="radio" value="0"
                                                            class="form-control" name="speedwork_{{ $speed->id }}"
                                                            {{ isset($speedResult) && $speedResult->work == 0 ? 'checked' : '' }}>
                                                    </td>
                                                    <td>
                                                        <input readonly disabled type="text"
                                                            value="{{ $speedResult->notes ?? '' }}" class="form-control"
                                                            name="speed_notes[]">
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
                                <div class="row">
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
                                                            value="{{ $programe->name }}" class="form-control"
                                                            name="check_programe_name[]">
                                                    </td>
                                                    <td>
                                                        <input readonly disabled type="radio" value="1"
                                                            class="form-control" name="programework_{{ $programe->id }}[]"
                                                            {{ isset($programeResult) && $programeResult->work == 1 ? 'checked' : '' }}>
                                                    </td>
                                                    <td>
                                                        <input readonly disabled type="radio" value="0"
                                                            class="form-control"
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
                                                            class="form-control" name="after_check_programe[]">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!--################### End Programe Device Check  #####################-->

                                <!--################### End Add ChecksResults #####################-->
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <form class="form" method="POST" action="#"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-body">
                                                <div class="row">
                                                    {{-- <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="name"> اسم العميل </label>
                                                            <input disabled required type="text" id="name"
                                                                class="form-control" placeholder="" name="name"
                                                                value="{{ $invoice->name }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="phone"> رقم الهاتف </label>
                                                            <input required type="text" id="phone"
                                                                class="form-control" placeholder="" name="phone"
                                                                value="{{ $invoice->phone }}">
                                                        </div>
                                                    </div> --}}
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="title"> اسم الجهاز </label>
                                                            <input disabled required type="text" id="title"
                                                                class="form-control" placeholder="" name="title"
                                                                value="{{ $invoice->title }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="title"> الاعطال </label>
                                                            <div class="skin skin-square">
                                                                <div
                                                                    class="col-md-12 col-sm-12 d-flex justify-content-around">
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
                                                            <label for="price"> السعر الاولي </label>
                                                            <input disabled required type="number" step="0.01"
                                                                id="price" class="form-control" placeholder=""
                                                                name="price" value="{{ $invoice->price }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="price"> تاريخ ووقت التسليم </label>
                                                        <div class="justify-between d-flex">
                                                            <div class="form-group">
                                                                <div class="position-relative has-icon-left">
                                                                    <input disabled type="date" name="date_delivery"
                                                                        value="{{ $invoice->date_delivery }}"
                                                                        id="timesheetinput3" class="form-control">
                                                                    <div class="form-control-position">
                                                                        <i class="ft-message-square"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="position-relative has-icon-left">
                                                                    <input disabled type="time" name="time_delivery"
                                                                        value="{{ $invoice->time_delivery }}"
                                                                        id="timesheetinput6" class="form-control">
                                                                    <div class="form-control-position">
                                                                        <i class="ft-clock"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
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
                                                                <img class="file_image"
                                                                    src="{{ asset('assets/uploads/invoices_files/' . $file['image']) }}"
                                                                    alt="Card image cap">
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
@endsection
@section('js')
    <script src="{{ asset('assets/admin/') }}/vendors/js/forms/icheck/icheck.min.js" type="text/javascript"></script>
    <script src="{{ asset('assets/admin/') }}/js/scripts/forms/checkbox-radio.js" type="text/javascript"></script>

@endsection

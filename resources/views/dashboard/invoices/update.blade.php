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
                                                                @php
                                                                    $checkResult = $invoice->checkResults
                                                                        ->where('problem_id', $check->id)
                                                                        ->where('invoice_id', $invoice->id)
                                                                        ->first();
                                                                @endphp
                                                                <tr>
                                                                    <td> {{ $loop->iteration }}</td>
                                                                    <td>
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
                                                                            name="work_{{ $check->id }}[]"
                                                                            {{ isset($checkResult) && $checkResult->work == 1 ? 'checked' : '' }}>
                                                                    </td>
                                                                    <td>
                                                                        <input type="radio" value="0"
                                                                            class="form-control"
                                                                            name="work_{{ $check->id }}[]"
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
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="name"> اسم العميل <span class="required_span"> * </span> </label>
                                                            <input required type="text" id="name"
                                                                class="form-control" placeholder="" name="name"
                                                                value="{{ $invoice->name }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="phone"> رقم الهاتف <span class="required_span"> * </span> </label>
                                                            <input required type="text" id="phone"
                                                                class="form-control" placeholder="" name="phone"
                                                                value="{{ $invoice->phone }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="title"> اسم الجهاز <span class="required_span"> * </span> </label>
                                                            <input required type="text" id="title"
                                                                class="form-control" placeholder="" name="title"
                                                                value="{{ $invoice->title }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="title"> حدد الاعطال <span class="required_span"> * </span> </label>
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
                                                            <textarea name="description" id="" class="form-control">{{ $invoice->description }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="price"> السعر الاولي <span class="required_span"> * </span> </label>
                                                            <input required type="number" step="0.01" id="price"
                                                                class="form-control" placeholder="" name="price"
                                                                value="{{ $invoice->price }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="price"> تاريخ ووقت التسليم <span class="required_span"> * </span> </label>
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
                                                        src="{{ asset('assets/uploads/invoices_files/'.$invoice->signature) }}" alt="">

                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label for="address"> اضافة مرفقات <span class="required_span"> * </span> </label>
                                                            <input type="file" name="files[]" class="form-control"
                                                                multiple>
                                                        </div>
                                                    </div>
                                                </div>
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
@endsection
@section('js')
    <script src="{{ asset('assets/admin/') }}/vendors/js/forms/icheck/icheck.min.js" type="text/javascript"></script>
    <script src="{{ asset('assets/admin/') }}/js/scripts/forms/checkbox-radio.js" type="text/javascript"></script>

@endsection

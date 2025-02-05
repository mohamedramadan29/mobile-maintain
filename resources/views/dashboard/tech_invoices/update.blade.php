@extends('dashboard.layouts.app')

@section('title', ' صيانة الجهاز ')
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
                    <h3 class="mb-0 content-header-title d-inline-block"> صيانة الجهاز </h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.tech_invoices.index') }}"> فواتيري
                                    </a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#"> صيانة الجهاز </a>
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
                                    <h4 class="card-title" id="basic-layout-form"> صيانة الجهاز </h4>
                                    <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                </div>
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <form class="form" method="POST"
                                            action="{{ route('dashboard.tech_invoices.update', $invoice->id) }}') }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-body">
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
                                                        <input readonly disabled type="text" value="{{ $checkResult->notes ?? '' }}"
                                                            class="form-control" name="notes[]">
                                                    </td>
                                                    <td>
                                                        <input readonly disabled type="text" value="{{ $checkResult->after_check ?? '' }}"
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
                                                            <label for="title"> اسم الجهاز </label>
                                                            <input disabled type="text" id="title"
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
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="title"> ملاحظات فني الصيانة </label>
                                                            <textarea name="tech_notes" id="" class="form-control">{{ $invoice->tech_notes }}</textarea>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="price"> حالة الجهاز </label>
                                                            <select name="status" id="" class="form-control">
                                                                <option
                                                                    {{ $invoice->status == 'تحت الصيانة' ? 'selected' : '' }}
                                                                    value="تحت الصيانة">تحت الصيانة</option>
                                                                <option
                                                                    {{ $invoice->status == 'تم الاصلاح' ? 'selected' : '' }}
                                                                    value="تم الاصلاح"> تم الاصلاح </option>
                                                                <option
                                                                    {{ $invoice->status == 'لم يتم الاصلاح' ? 'selected' : '' }}
                                                                    value="لم يتم الاصلاح">لم يتم الاصلاح</option>
                                                                <option
                                                                    {{ $invoice->status == 'معلق' ? 'selected' : '' }}
                                                                    value="معلق">معلق</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-actions">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="la la-check-square-o"></i> حفظ
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
                                        <h5> اضافة مرفق </h5>
                                        <form action="{{ route('dashboard.tech_invoices.addfile', $invoice->id) }}"
                                            method="POST" enctype="multipart/form-data">
                                            @csrf

                                            <div class="row">

                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="address"> صورة المرفق <span class="required_span"> *
                                                            </span> </label>
                                                        <input required type="file" name="file" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="address"> اضف عنوان للمرفق <span class="required_span">
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
                                            </div>
                                        </form>
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
                                                    <th>
                                                        العمليات
                                                    </th>
                                                </tr>

                                                @forelse ($invoice->files as $file)
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
                                                            {{ number_format($file->price,2) }} ريال
                                                        </td>
                                                        <td>
                                                            {{ $file->description }}
                                                        </td>
                                                        <td>
                                                            <form
                                                                action="{{ route('dashboard.invoices.delete_file', $file['id']) }}"
                                                                method="POST">
                                                                @csrf
                                                                <div class="">
                                                                    <button
                                                                        onclick="return confirm('هل تريد حذف هذا المرفق؟')"
                                                                        type="submit" class="btn btn-danger btn-sm">
                                                                        <i class="la la-trash"></i> </button>
                                                                </div>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    لا يوجد مرفقات
                                                @endforelse
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

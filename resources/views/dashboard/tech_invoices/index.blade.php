@extends('dashboard.layouts.app')
@section('title', ' فواتيري ')
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="mb-2 content-header-left col-md-6 col-12 breadcrumb-new">
                    <h3 class="mb-0 content-header-title d-inline-block"> ادارة فواتيري </h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية </a>
                                </li>

                                @if (request('invoice_status'))
                                    <li class="breadcrumb-item"> <a href="{{ route('dashboard.tech_invoices.index') }}">
                                            ادارة فواتيري
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item active"> بحث
                                    </li>
                                @else
                                    <li class="breadcrumb-item active"> ادارة فواتيري
                                    </li>
                                @endif
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="content-header-right col-md-6 col-12">

                </div>
            </div>
            <div class="content-body">
                <form action="{{ route('dashboard.tech_invoices.search') }}" method="get">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="form-group" style="margin-left: 20px">
                            <label> حالة الفاتورة </label>
                            <select name="invoice_status" class="form-control">
                                <option value="" selected disabled> -- حدد حالة الفاتورة -- </option>
                                <option value="رف الاستلام"
                                    {{ request('invoice_status') == 'رف الاستلام' ? 'selected' : '' }}>رف
                                    الاستلام</option>
                                <option value="تحت الصيانة"
                                    {{ request('invoice_status') == 'تحت الصيانة' ? 'selected' : '' }}> تحت
                                    الصيانة </option>
                                <option value="تم الاصلاح"
                                    {{ request('invoice_status') == 'تم الاصلاح' ? 'selected' : '' }}>تم
                                    الاصلاح</option>
                                <option value="لم يتم الاصلاح"
                                    {{ request('invoice_status') == 'لم يتم الاصلاح' ? 'selected' : '' }}>لم
                                    يتم الاصلاح</option>
                                <option value="معلق" {{ request('invoice_status') == 'معلق' ? 'selected' : '' }}>معلق
                                </option>
                                <option value="تم تسليم الجهاز"
                                    {{ request('invoice_status') == 'تم تسليم الجهاز' ? 'selected' : '' }}>
                                    تم تسليم الجهاز
                                </option>
                                <option value="لم يتم التسليم"
                                    {{ request('invoice_status') == 'لم يتم التسليم' ? 'selected' : '' }}>
                                    لم يتم تسليم الجهاز
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <button type="submit" style="margin-top: 25px" class="btn btn-primary btn-sm">بحث <i
                                    class="la la-search"></i></button>
                        </div>
                    </div>

                </form>
                <!-- Bordered striped start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th> رقم الفاتورة </th>
                                                    <th> اسم العميل </th>
                                                    <th> رقم الهاتف </th>
                                                    <th> العنوان </th>
                                                    <th> المشاكل </th>
                                                    <th> الحالة </th>
                                                    <th> استلام الجهاز </th>
                                                    <th> تاريخ ووقت البدء </th>
                                                    <th> تاريخ ووقت التسليم </th>
                                                    <th> العمليات </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($invoices as $invoice)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td> {{ $invoice->id }} </td>
                                                        <td> {{ $invoice->name }} </td>
                                                        <td> {{ $invoice->phone }} </td>

                                                        <td>
                                                            {{ $invoice->title }}
                                                        </td>
                                                        <td>
                                                            @foreach (json_decode($invoice->problems) as $problem)
                                                                <span class="badge badge-danger"> {{ $problem }}
                                                                </span>
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-info">
                                                                {{ $invoice->status }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if ($invoice->delivery_status == 1)
                                                                <span class="badge badge-success">
                                                                    تم التسليم
                                                                </span>
                                                            @else
                                                                <span class="mb-1 badge badge-danger">
                                                                    لم يتم التسليم
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td> {{ $invoice->checkout_time }} </td>
                                                        <td> {{ $invoice->date_delivery }} //
                                                            {{ $invoice->time_delivery }}
                                                        </td>
                                                        <td>
                                                            @if ($invoice->delivery_status == 1)
                                                                <a class="btn btn-warning btn-sm"
                                                                    href="{{ route('dashboard.tech_invoices.show-compelete-invoice', $invoice->id) }}"><i
                                                                        class="la la-eye"></i> تفاصيل الفاتورة </a>
                                                            @else
                                                                <a class="btn btn-warning btn-sm"
                                                                    href="{{ route('dashboard.tech_invoices.update', $invoice->id) }}"><i
                                                                        class="la la-edit"></i> تعديل الصيانة </a>
                                                            @endif

                                                        </td>
                                                    </tr>
                                                    <div class="form-group">
                                                    </div>
                                                @empty
                                                    <td colspan="4"> لا يوجد بيانات </td>
                                                @endforelse
                                            </tbody>
                                        </table>
                                        {{ $invoices->links() }}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- Bordered striped end -->
            </div>
        </div>
    </div>


@endsection

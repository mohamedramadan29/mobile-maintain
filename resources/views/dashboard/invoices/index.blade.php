@extends('dashboard.layouts.app')
@section('title', ' الفواتير ')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/') }}/vendors/css/tables/datatable/datatables.min.css">
    <style>

    </style>
@endsection
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="mb-2 content-header-left col-md-6 col-12 breadcrumb-new">
                    <h3 class="mb-0 content-header-title d-inline-block"> ادارة الفواتير </h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item active"> ادارة الفواتير
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="content-header-right col-md-6 col-12">

                </div>
            </div>
            <div class="content-body">

                <!-- Bordered striped start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between flex-column">
                                <a href="{{ route('dashboard.invoices.create') }}" class="btn btn-primary"> اضافة فاتورة
                                </a>
                                <form action="{{ route('dashboard.invoices.index') }}" method="get">
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
                                                <option value="معلق"
                                                    {{ request('invoice_status') == 'معلق' ? 'selected' : '' }}>معلق
                                                </option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" style="margin-top: 25px"
                                                class="btn btn-primary btn-sm">بحث <i class="la la-search"></i></button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered zero-configuration dataTable"
                                            id="DataTables_Table_0">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th> رقم الفاتورة </th>
                                                    <th> الاسم </th>
                                                    <th> رقم الهاتف </th>
                                                    <th> العنوان </th>
                                                    <th> المشاكل </th>
                                                    <th> الحالة </th>
                                                    <th> الاستقبال </th>
                                                    <th> الفني </th>
                                                    <th> تاريخ الاستلام  </th>
                                                    <th> تاريخ ووقت التسليم  </th>
                                                    <th> العمليات </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($invoices as $invoice)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td> {{ $invoice->id }} </td>
                                                        <td> {{ $invoice->name }} </td>
                                                        <td>
                                                            {{ $invoice->phone }}
                                                        </td>
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
                                                            @if ($invoice->status == 'تم الاصلاح')
                                                                <span class="badge badge-success">
                                                                    {{ $invoice->status }}
                                                                </span>
                                                            @elseif($invoice->status == 'لم يتم الاصلاح')
                                                                <span class="badge badge-danger">
                                                                    {{ $invoice->status }}
                                                                </span>
                                                            @elseif($invoice->status == 'تحت الصيانة')
                                                                <span class="badge badge-warning">
                                                                    {{ $invoice->status }}
                                                                </span>
                                                            @else
                                                                <span class="badge badge-info">
                                                                    {{ $invoice->status }}
                                                                </span>
                                                            @endif

                                                        </td>
                                                        <td>
                                                            {{ $invoice->Recieved->name }}
                                                        </td>
                                                        <td>
                                                            @if (!$invoice->admin_repair_id)
                                                                لا يوجد
                                                                <button class="btn btn-warning btn-sm" type="button"
                                                                    data-toggle="modal"
                                                                    data-target="#add_tech_invoice_{{ $invoice->id }}">
                                                                    تعين فني </button>
                                                            @else
                                                                {{ $invoice->Technical->name }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ date('Y-m-d h:i A', strtotime($invoice->created_at)) }}
                                                        </td>
                                                        <td>
                                                            {{ $invoice->date_delivery }} {{ date('h:i A', strtotime($invoice->time_delivery)) }}
                                                        </td>
                                                        <td>
                                                            <div class="mb-1 mr-1 btn-group">
                                                                <button type="button"
                                                                    class="btn btn-primary btn-block dropdown-toggle btn-sm"
                                                                    data-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false">
                                                                    العمليات
                                                                </button>
                                                                <div class="dropdown-menu open-left arrow"
                                                                    x-placement="bottom-start"
                                                                    style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                                    <a href="{{ route('dashboard.invoices.update', $invoice->id) }}"
                                                                        class="dropdown-item" type="button"> تعديل </a>
                                                                    <a href="{{ route('dashboard.invoices.print', $invoice->id) }}"
                                                                        class="dropdown-item" type="button"> طباعة </a>
                                                                    <a href="{{ route('dashboard.invoices.print_barcode', $invoice->id) }}"
                                                                        class="dropdown-item" type="button"> طباعة باركود
                                                                    </a>
                                                                    <a href="{{ route('dashboard.invoices.steps', $invoice->id) }}"
                                                                        class="dropdown-item" type="button"> حركة حساب
                                                                        الفاتورة </a>
                                                                    <button class="dropdown-item" type="button"
                                                                        data-toggle="modal"
                                                                        data-target="#delete_invoice_{{ $invoice->id }}">
                                                                        حذف </button>

                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <div class="form-group">
                                                    </div>
                                                    @include('dashboard.invoices.delete')
                                                    @include('dashboard.invoices.add_tech_invoice')
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



@section('js')
    <script src="{{ asset('assets/admin/') }}/vendors/js/tables/datatable/datatables.min.js" type="text/javascript">
    </script>
    <script src="{{ asset('assets/admin/') }}/js/scripts/tables/datatables/datatable-basic.js" type="text/javascript">
    </script>
    <script>
        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#DataTables_Table_0')) {
                $('#DataTables_Table_0').DataTable({
                    language: lang === 'ar' ? {
                        url: '//cdn.datatables.net/plug-ins/2.2.2/i18n/ar.json',
                    } : {},
                });
            }
        });
    </script>
@endsection

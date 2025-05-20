@extends('dashboard.layouts.app')
@section('title', ' الفواتير ')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/') }}/vendors/css/tables/datatable/datatables.min.css">
    <style>
        div.dataTables_wrapper div.dataTables_paginate {
            display: none;
        }
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
                                @can('add_invoice')
                                    <a href="{{ route('dashboard.invoices.create') }}" class="btn btn-primary"> اضافة فاتورة
                                    </a>
                                @endcan
                                <form action="{{ route('dashboard.invoices.index') }}" method="get">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="form-group" style="margin-left: 20px">
                                            <label> حالة الفاتورة </label>
                                            <select name="invoice_status" class="form-control">

                                                <option value=""
                                                    {{ request('invoice_status') === null ? 'selected' : '' }}> -- كل
                                                    الفواتير -- </option>
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
                                            <button type="submit" style="margin-top: 25px"
                                                class="btn btn-primary btn-sm">بحث <i class="la la-search"></i></button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <form id="bulkDeleteForm" action="{{ route('dashboard.invoices.bulk_delete') }}"
                                            method="POST" style="display: none;">
                                            @csrf
                                            <input type="hidden" name="invoice_ids" id="invoice_ids">
                                        </form>
                                        <table class="table table-striped table-bordered zero-configuration dataTable"
                                            id="DataTables_Table_0">
                                            <thead>
                                                <tr>
                                                    <th style="width: 10px;"><input type="checkbox" id="select_all"></th>
                                                    <th>#</th>
                                                    <th> رقم الفاتورة </th>
                                                    <th> الاسم </th>
                                                    <th> رقم الهاتف </th>
                                                    <th> العنوان </th>
                                                    <th> المشاكل </th>
                                                    <th> الحالة </th>
                                                    <th> استلام الجهاز </th>
                                                    <th> الاستقبال </th>
                                                    <th> الفني </th>
                                                    <th> تاريخ الاستلام </th>
                                                    <th> تاريخ ووقت التسليم </th>
                                                    <th> العمليات </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($invoices as $invoice)
                                                    <tr>
                                                        {{-- <td style="width: 10px; padding: 10px">
                                                            <input type="checkbox" class="select_item"
                                                                value="{{ $invoice->id }}">
                                                        </td> --}}
                                                        <td style="width: 10px; padding: 10px"><input type="checkbox"
                                                                name="invoice_select" value="{{ $invoice->id }}"></td>
                                                        <td scope="row">{{ $loop->iteration }}</td>
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
                                                        <td>
                                                            {{ $invoice->Recieved->name }}
                                                        </td>
                                                        <td>
                                                            @if (!$invoice->admin_repair_id)
                                                                لا يوجد
                                                                <a href="{{ route('dashboard.invoices.add_tech', $invoice->id) }}"
                                                                    class="btn btn-warning btn-sm"> تعين فني </a>
                                                            @else
                                                                {{ $invoice->Technical->name }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ date('Y-m-d h:i A', strtotime($invoice->created_at)) }}
                                                        </td>
                                                        <td>
                                                            {{ $invoice->date_delivery }}
                                                            {{ date('h:i A', strtotime($invoice->time_delivery)) }}
                                                        </td>
                                                        <td>
                                                            <div class="mr-1 mb-1 btn-group">
                                                                <button type="button"
                                                                    class="btn btn-primary btn-block dropdown-toggle btn-sm"
                                                                    data-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false">
                                                                    العمليات
                                                                </button>
                                                                <div class="dropdown-menu open-left arrow"
                                                                    x-placement="bottom-start"
                                                                    style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                                    @can('edit_invoice')
                                                                        <a href="{{ route('dashboard.invoices.update', $invoice->id) }}"
                                                                            class="dropdown-item" type="button"> تعديل </a>
                                                                    @endcan
                                                                    <a href="{{ route('dashboard.invoices.print', $invoice->id) }}"
                                                                        class="dropdown-item" type="button"> طباعة </a>
                                                                    <a href="{{ route('dashboard.invoices.print_barcode', $invoice->id) }}"
                                                                        class="dropdown-item" type="button"> طباعة باركود
                                                                    </a>
                                                                    <a href="{{ route('dashboard.invoices.steps', $invoice->id) }}"
                                                                        class="dropdown-item" type="button"> حركة حساب
                                                                        الفاتورة </a>
                                                                    @can('delete_invoice')
                                                                        <a href="{{ route('dashboard.invoices.destroy', $invoice->id) }}"
                                                                            class="dropdown-item" type="button"> حذف </a>
                                                                    @endcan
                                                                </div>
                                                                @if ($invoice->message_send == 0)
                                                                    <form id="send_message_form"
                                                                        action="{{ route('dashboard.invoices.send_message', $invoice->id) }}"
                                                                        method="post">
                                                                        @csrf
                                                                        <button type="submit" id="send_message_button"
                                                                            class="btn btn-warning btn-sm">
                                                                            <i style="font-size:12px"
                                                                                class="la la-warning"></i>
                                                                            اعادة ارسال رسالة
                                                                        </button>
                                                                        <div id="loadingMessage"
                                                                            class="spinner-border text-primary"
                                                                            role="status" style="display: none;">
                                                                            <span class="sr-only">جاري اعادة ارسال
                                                                                الرسالة...</span>
                                                                        </div>
                                                                    </form>
                                                                    <script>
                                                                        document.getElementById('send_message_form').addEventListener('submit', function(e) {
                                                                            e.preventDefault();
                                                                            document.getElementById('send_message_button').style.display = 'none';
                                                                            document.getElementById('loadingMessage').style.display = 'block';
                                                                            this.submit();
                                                                        });
                                                                    </script>
                                                                @endif
                                                                @if ($invoice->delivery_status == 0)
                                                                    <a href="{{ route('dashboard.invoices.delivery', $invoice->id) }}"
                                                                        class="btn btn-success btn-sm">
                                                                        <i style="font-size:12px" class="la la-check"></i>
                                                                        تسليم الجهاز
                                                                    </a>
                                                                @elseif ($invoice->delivery_status == 1)
                                                                    <a href="{{ route('dashboard.invoices.undelivery', $invoice->id) }}"
                                                                        class="btn btn-danger btn-sm">
                                                                        <i style="font-size:12px" class="la la-undo"></i>
                                                                        عودة الجهاز
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <div class="form-group">
                                                    </div>

                                                @empty
                                                    <td colspan="4"> لا يوجد بيانات </td>
                                                @endforelse
                                            </tbody>
                                            <tfoot>

                                            </tfoot>
                                        </table>
                                        <div class="alert alert-danger" id="alert_no_invoices" style="display: none;">
                                            من فضلك اختر فواتير لحذفها.
                                        </div>
                                        <div class="alert alert-danger" id="alert_delete_invoices"
                                            style="display: none;">
                                            هل انت متاكد من حذف الفواتير المحددة؟
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="submitBulkDelete()">نعم</button>
                                        </div>
                                        @can('delete_invoice')
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="submitBulkDelete()">
                                            حذف المحدد
                                        </button>
                                        @endcan
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
    <script>
        // function submitBulkDelete() {
        //     let selected = [];
        //     document.querySelectorAll('.select_item:checked').forEach(cb => {
        //         selected.push(cb.value);
        //     });

        //     if (selected.length === 0) {
        //         document.getElementById('alert_no_invoices').style.display = 'block';
        //         return;
        //     }


        //     if (selected.length > 0) {
        //         document.getElementById('alert_delete_invoices').style.display = 'block';
        //         return;
        //     }

        //     document.getElementById('invoice_ids').value = selected.join(',');
        //     document.getElementById('bulkDeleteForm').submit();
        // }

        // // اختيار الكل
        // document.getElementById('select_all').addEventListener('click', function() {
        //     let checkboxes = document.querySelectorAll('.select_item');
        //     checkboxes.forEach(cb => cb.checked = this.checked);
        // });
    </script>

    <script>
        function submitBulkDelete() {
            // جمع جميع مربعات الاختيار المحددة
            let selectedInvoices = [];
            $('input[name="invoice_select"]:checked').each(function() {
                selectedInvoices.push($(this).val());
            });

            if (selectedInvoices.length === 0) {
                document.getElementById('alert_no_invoices').style.display = 'block';
                return;
            }

            // تخزين معرفات الفواتير في الحقل المخفي
            $('#invoice_ids').val(selectedInvoices.join(','));

            // إعادة التوجيه إلى صفحة التأكيد مع تمرير معرفات الفواتير كمعلمات
            window.location.href = "{{ route('dashboard.invoices.bulk_delete_confirm') }}?invoice_ids=" + selectedInvoices
                .join(',');
        }
        $(document).ready(function() {
            $('#select_all').on('click', function() {
                $('input[name="invoice_select"]').prop('checked', this.checked);
            });

            $('input[name="invoice_select"]').on('click', function() {
                if ($('input[name="invoice_select"]').length === $('input[name="invoice_select"]:checked')
                    .length) {
                    $('#select_all').prop('checked', true);
                } else {
                    $('#select_all').prop('checked', false);
                }
            });
        });
    </script>
@endsection

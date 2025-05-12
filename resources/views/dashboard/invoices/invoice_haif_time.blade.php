@extends('dashboard.layouts.app')
@section('title', ' فواتير مر عليها نصف الوقت او اكثر ')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/') }}/vendors/css/tables/datatable/datatables.min.css">
@endsection
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="mb-2 content-header-left col-md-6 col-12 breadcrumb-new">
                    <h3 class="mb-0 content-header-title d-inline-block"> فواتير مر عليها نصف الوقت او اكثر ومازالت في رف
                        الاستلام</h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item active"> فواتير مر عليها نصف الوقت او اكثر ومازالت في رف الاستلام
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
                                                            {{ $invoice->date_delivery }} - {{ $invoice->time_delivery }}
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
                                                                        <a href="{{ route('dashboard.invoices.destroy', $invoice->id) }}"
                                                                            class="dropdown-item" type="button"> حذف </a>

                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <div class="form-group">
                                                    </div>
                                                @empty
                                                    <td colspan="4"> لا يوجد بيانات </td>
                                                @endforelse
                                            </tbody>
                                        </table>

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
                    language: {
                        processing: "جاري المعالجة...",
                        search: "بحث:",
                        lengthMenu: "عرض _MENU_ سجل لكل صفحة",
                        info: "عرض _START_ إلى _END_ من أصل _TOTAL_ سجل",
                        infoEmpty: "عرض 0 إلى 0 من أصل 0 سجل",
                        infoFiltered: "(تمت تصفيته من إجمالي _MAX_ سجلات)",
                        loadingRecords: "جاري التحميل...",
                        zeroRecords: "لا توجد سجلات مطابقة",
                        emptyTable: "لا توجد بيانات متاحة في الجدول",
                        paginate: {
                            first: "الأول",
                            previous: "السابق",
                            next: "التالي",
                            last: "الأخير"
                        },
                        aria: {
                            sortAscending: ": تفعيل لترتيب العمود تصاعدياً",
                            sortDescending: ": تفعيل لترتيب العمود تنازلياً"
                        }
                    }
                });
            }
        });
    </script>
@endsection

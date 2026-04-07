@extends('dashboard.layouts.app')
@section('title', ' فواتير الفني ')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/') }}/vendors/css/tables/datatable/datatables.min.css">
@endsection
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="mb-2 content-header-left col-md-6 col-12 breadcrumb-new">
                    <h3 class="mb-0 content-header-title d-inline-block"> فواتير الفني </h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item active"> فواتير الفني
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
                                <div class="card-header">
                                    <h5>عدد الفواتير الكلي: {{ $invoices->total() }}</h5>
                                    @if(request()->has('start_from') && request()->has('end_to') && request()->start_from && request()->end_to)
                                        <h5>عدد الفواتير في الفترة المحددة: {{ $totalInvoices }}</h5>
                                    @endif
                                    <div class="mt-1 row">
                                        <div class="col-md-3">
                                            <div class="p-1 text-center text-white rounded bg-success">
                                                <span>ناجح (في الوقت):</span> <strong>{{ $successCount }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="p-1 text-center text-white rounded bg-danger">
                                                <span>تجاوز الوقت:</span> <strong>{{ $failCount }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <!-- ################# Search WithMonth ##################### !-->
                                    <div class="form-group">
                                        <form action="{{ route('dashboard.admins.tech_invoices', $id) }}" method="get">
                                            @csrf
                                            <div class="row d-flex align-items-center">
                                                <div class="col-4">
                                                    <label>بداية التاريخ</label>
                                                    <input type="date" name="start_from" class="form-control" value="{{ request('start_from') }}">
                                                </div>
                                                <div class="col-4">
                                                    <label>نهاية التاريخ</label>
                                                    <input type="date" name="end_to" class="form-control" value="{{ request('end_to') }}">
                                                </div>
                                                <div class="col-2">
                                                    <label>حالة العداد</label>
                                                    <select name="timer_status" class="form-control">
                                                        <option value="">الكل</option>
                                                        <option value="success" {{ request('timer_status') == 'success' ? 'selected' : '' }}>ناجح</option>
                                                        <option value="fail" {{ request('timer_status') == 'fail' ? 'selected' : '' }}>تجاوز الوقت</option>
                                                    </select>
                                                </div>
                                                <div class="col-2">
                                                    <button style="margin-top: 30px" type="submit" class="btn btn-primary btn-sm">بحث <i class="la la-search"></i></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <!-- ################# End  Search WithMonth ##################### !-->
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration dataTable"
                                        id="DataTables_Table_0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th> رقم الفاتورة </th>
                                                <th> العنوان </th>
                                                <th> المشاكل </th>
                                                <th> الحالة </th>
                                                <th> التزام بالوقت </th>
                                                <th> تاريخ ووقت البدء </th>
                                                <th> تاريخ ووقت التسليم </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($invoices as $invoice)
                                                <tr>
                                                    <th scope="row">{{ $loop->iteration }}</th>
                                                    <td> {{ $invoice->id }} </td>

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
                                                        @if ($invoice->repair_timer_status == 'نجاح')
                                                        <span class="badge badge-success">
                                                            {{ $invoice->repair_timer_status }}
                                                        </span>
                                                        @elseif ($invoice->repair_timer_status == 'تجاوز الوقت')
                                                        <span class="badge badge-danger">
                                                            {{ $invoice->repair_timer_status }}
                                                        </span>
                                                        @else
                                                        <span class="badge badge-info">
                                                            {{ $invoice->repair_timer_status }}
                                                        </span>
                                                        @endif
                                                    </td>
                                                    <td> {{ $invoice->checkout_time }} </td>
                                                    <td> {{ $invoice->date_delivery }} // {{ $invoice->time_delivery }}
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

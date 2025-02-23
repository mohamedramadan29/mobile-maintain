@extends('dashboard.layouts.app')
@section('title', 'ادارة الرسائل ')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/') }}/vendors/css/tables/datatable/datatables.min.css">
@endsection
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="mb-2 content-header-left col-md-6 col-12 breadcrumb-new">
                    <h3 class="mb-0 content-header-title d-inline-block"> ادارة الرسائل </h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item active"> ادارة الرسائل
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
                            <div class="card-header">
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered zero-configuration dataTable"
                                            id="DataTables_Table_0">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th> نوع الرسالة </th>
                                                    <th> محتوي الرسالة </th>
                                                    <th> العمليات </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($messages as $message)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>

                                                        <td> {{ $message->message_type }} </td>
                                                        <td>
                                                            {{ $message->template_text }}
                                                        </td>

                                                        <td>
                                                            <a  class="btn btn-info btn-sm"  href="{{ route('dashboard.messages.update', $message->id)  }}">
                                                                تعديل <i class="la la-edit"></i>
                                                            </a>
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

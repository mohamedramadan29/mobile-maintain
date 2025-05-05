@extends('dashboard.layouts.app')
@section('title')
    الرئيسية
@endsection
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- eCommerce statistic -->
                <div class="row">
                    <div class="col-xl-3 col-lg-6 col-12">
                        <div class="card pull-up">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="media d-flex">
                                        <div class="text-left media-body">
                                            <h3 class="info"> {{ $invoices_count }} </h3>
                                            <h6> جميع الفواتير </h6>
                                        </div>
                                        <div>
                                            <i class="float-right icon-basket-loaded info font-large-2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @can('admins')
                        <div class="col-xl-3 col-lg-6 col-12">
                            <div class="card pull-up">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="text-left media-body">
                                                <h3 class="warning"> {{ $AdminCount }} </h3>
                                                <h6> الموظفين </h6>
                                            </div>
                                            <div>
                                                <i class="float-right icon-pie-chart warning font-large-2"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-12">
                            <div class="card pull-up">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="text-left media-body">
                                                <h3 class="success"> {{ $roles }} </h3>
                                                <h6> الصلاحيات </h6>
                                            </div>
                                            <div>
                                                <i class="float-right icon-user-follow success font-large-2"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-12">
                            <div class="card pull-up">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="text-left media-body">
                                                <h3 class="danger"> {{ $problems }}</h3>
                                                <h6> اقسام الاعطال </h6>
                                            </div>
                                            <div>
                                                <i class="float-right icon-heart danger font-large-2"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan

                </div>
                <!--/ eCommerce statistic -->
                <!-- Recent Transactions -->
                @can('admins')
                    <div class="row">
                        <div id="recent-transactions" class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"> احدث الفواتير </h4>
                                    <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                    <div class="heading-elements">
                                        <ul class="mb-0 list-inline">
                                            <li><a class="btn btn-sm btn-danger box-shadow-2 round btn-min-width pull-right"
                                                    href="{{ route('dashboard.invoices.index') }}"> جميع الفواتير </a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-content">
                                    <div class="table-responsive">
                                        <table id="recent-orders" class="table mb-0 table-hover table-xl">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th> الاسم </th>
                                                    <th> رقم الهاتف </th>
                                                    <th> العنوان </th>
                                                    <th> المشاكل </th>
                                                    <th> الحالة </th>
                                                    <th> الاستقبال </th>
                                                    <th> الفني </th>
                                                    <th> تاريخ الاستلام  </th>
                                                    <th> تاريخ التسليم   </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($availableInvoices as $invoice)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
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
                                                            {{ $invoice->Technical->name ?? ' لا يوجد ' }}
                                                        </td>
                                                        <td>
                                                            {{ date('Y-m-d h:i A', strtotime($invoice->created_at)) }}
                                                        </td>
                                                        <td>
                                                            {{ $invoice->date_delivery }} {{ date('h:i A', strtotime($invoice->time_delivery)) }}
                                                        </td>
                                                    </tr>

                                                    @include('dashboard.invoices.delete')
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
                @endcan
                @if (Auth::guard('admin')->user()->type == 'فني')
                    @can('tech_invoices')
                        <div class="row">
                            <div id="recent-transactions" class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title"> احدث الفواتير المتاحة</h4>
                                        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                        <div class="heading-elements">
                                            <ul class="mb-0 list-inline">
                                                <li><a class="btn btn-sm btn-danger box-shadow-2 round btn-min-width pull-right"
                                                        href="{{ route('dashboard.tech_invoices.available') }}"> جميع الفواتير
                                                        المتاحة </a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-content">
                                        <div class="table-responsive">
                                            <table id="recent-orders" class="table mb-0 table-hover table-xl">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th> الاسم </th>
                                                        <th> رقم الهاتف </th>
                                                        <th> العنوان </th>
                                                        <th> المشاكل </th>
                                                        <th> الحالة </th>
                                                        <th> الاستقبال </th>
                                                        <th> الفني </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $technicianProblems =
                                                            json_decode(Auth::user()->problems, true) ?? [];
                                                    @endphp
                                                    @forelse ($availableInvoices as $invoice)
                                                        @php
                                                            // تحويل مشاكل الفاتورة إلى مصفوفة
                                                            $invoiceProblems =
                                                                json_decode($invoice->problems, true) ?? [];
                                                            // التحقق من أن جميع مشاكل الفاتورة ضمن اختصاصات الفني
                                                            $allMatch = empty(
                                                                array_diff($invoiceProblems, $technicianProblems)
                                                            );
                                                        @endphp
                                                        @if ($allMatch)
                                                            <tr>
                                                                <th scope="row">{{ $loop->iteration }}</th>
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
                                                                    {{ $invoice->Technical->name ?? ' لا يوجد ' }}
                                                                </td>
                                                            </tr>
                                                        @endif
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
                    @endcan
                @endif
                <!--/ Recent Transactions -->
            </div>
        </div>
    </div>
@endsection

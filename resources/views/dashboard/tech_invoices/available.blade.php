@extends('dashboard.layouts.app')
@section('title', ' الفواتير المتاحة للعمل عليها ')
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="mb-2 content-header-left col-md-6 col-12 breadcrumb-new">
                    <h3 class="mb-0 content-header-title d-inline-block"> الفواتير المتاحة للعمل عليها </h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item active"> الفواتير المتاحة للعمل عليها
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
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th> رقم الفاتورة </th>
                                                    <th> اسم العميل </th>
                                                    <th>  رقم الهاتف </th>
                                                    <th> العنوان </th>
                                                    <th> المشاكل </th>
                                                    <th> الحالة </th>
                                                    
                                                    <th> تاريخ ووقت التسليم </th>
                                                    <th> العمليات </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $technicianProblems =
                                                        json_decode(Auth::user()->problems, true) ?? [];
                                                @endphp
                                                @forelse ($invoices as $invoice)
                                                    @php
                                                        // تحويل مشاكل الفاتورة إلى مصفوفة
                                                        $invoiceProblems = json_decode($invoice->problems, true) ?? [];
                                                        // التحقق من أن جميع مشاكل الفاتورة ضمن اختصاصات الفني
                                                        $allMatch = empty(
                                                            array_diff($invoiceProblems, $technicianProblems)
                                                        );
                                                    @endphp
                                                    @if ($allMatch)
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
                                                            <td> {{ $invoice->date_delivery }} //
                                                                {{ $invoice->time_delivery }}
                                                            </td>
                                                            <td>
                                                                <a class="btn btn-info btn-sm"
                                                                    href="{{ route('dashboard.tech_invoices.show', $invoice->id) }}"><i
                                                                        class="la la-eye"></i> تفاصيل الجهاز </a>

                                                                <a class="btn btn-warning btn-sm"
                                                                    href="{{ route('dashboard.tech_invoices.checkout', $invoice->id) }}"><i
                                                                        class="la la-eye"></i> استلام الجهاز </a>
                                                                {{-- <button type="button" class="btn btn-warning btn-sm"
                                                                    data-toggle="modal"
                                                                    data-target="#checkout_invoice_{{ $invoice->id }}">
                                                                    استلام الجهاز <i class="la la-check"></i>
                                                                </button> --}}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    {{-- @include('dashboard.tech_invoices.checkout') --}}
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

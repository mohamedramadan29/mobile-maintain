@extends('dashboard.layouts.app')
@section('title', ' تأكيد حذف الفواتير ')
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
                    <h3 class="mb-0 content-header-title d-inline-block"> تأكيد حذف الفواتير </h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.invoices.index') }}"> الفواتير </a>
                                </li>
                                <li class="breadcrumb-item active"> تأكيد حذف الفواتير
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

                                        <p>لقد اخترت الفواتير التالية للحذف:</p>
                                        <ul class="mb-4 list-group">
                                            @foreach ($invoices as $invoice)
                                                <li class="list-group-item">الفاتورة رقم ::  {{ $invoice->id }}</li>
                                            @endforeach
                                        </ul>

                                        <form id="bulkDeleteForm" action="{{ route('dashboard.invoices.bulk_delete') }}"
                                            method="POST">
                                            @csrf
                                            <input type="hidden" name="invoice_ids"
                                                value="{{ implode(',', $invoiceIds) }}">
                                            <button type="submit" class="btn btn-danger">تأكيد الحذف</button>
                                            <a href="{{ route('dashboard.invoices.index') }}"
                                                class="btn btn-secondary">إلغاء</a>
                                        </form>
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

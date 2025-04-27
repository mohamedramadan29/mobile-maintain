@extends('dashboard.layouts.app')

@section('title', ' استلام الجهاز ')
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
                    <h3 class="mb-0 content-header-title d-inline-block"> استلام الجهاز </h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.tech_invoices.index') }}"> فواتيري
                                    </a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#"> استلام الجهاز </a>
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
                                    <h4 class="card-title" id="basic-layout-form"> استلام الجهاز </h4>
                                    <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                </div>
                                <div class="card-content collapse show">
                                    <div class="card-body">

                                        <form id='invoice-form'
                                            action="{{ route('dashboard.tech_invoices.checkout', $invoice->id) }}"
                                            method="POST">
                                            @csrf

                                            <div class="form-group">
                                                <label> تاريخ التسليم </label>
                                                <input type="text" disabled class="form-control" name="name"
                                                    value="{{ $invoice->date_delivery }}">
                                            </div>
                                            <div class="form-group">
                                                <label> وقت التسليم </label>
                                                <input type="text" disabled class="form-control" name="name"
                                                    value="{{ $invoice->time_delivery }}">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn grey btn-outline-secondary"
                                                    data-dismiss="modal">رجوع
                                                </button>
                                                <button type="submit" class="btn btn-info"> بدء العمل </button>
                                                <br>
                                                <p id="loadingMessage" class="mt-2 text-info" style="display: none;">⏳
                                                    جاري رفع البيانات، الرجاء الانتظار...</p>
                                            </div>
                                        </form>
                                        <script>
                                            document.getElementById("invoice-form").addEventListener("submit", function(e) {
                                                e.preventDefault();

                                                let submitBtn = this.querySelector('button[type="submit"]');
                                                let loadingMessage = document.getElementById('loadingMessage');

                                                submitBtn.disabled = true;
                                                submitBtn.innerHTML = '<i class="la la-spinner la-spin"></i> جاري الحفظ...';

                                                loadingMessage.style.display = 'block';
                                                this.submit();

                                            });
                                        </script>
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

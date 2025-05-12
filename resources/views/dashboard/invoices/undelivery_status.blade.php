@extends('dashboard.layouts.app')
@section('title', ' عودة الجهاز ')
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="mb-2 content-header-left col-md-6 col-12 breadcrumb-new">
                    <h3 class="mb-0 content-header-title d-inline-block"> عودة الجهاز </h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.invoices.index') }}"> الفواتير </a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#"> عودة الجهاز </a>
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
                                    <h4 class="card-title" id="basic-layout-form"> عودة الجهاز </h4>
                                    <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i> </a>
                                </div>
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <form id="delivery_form"
                                            action="{{ route('dashboard.invoices.undelivery', $invoice->id) }}"
                                            method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label> رقم الفاتورة  </label>
                                                <input type="text" name="delivery_date" class="form-control"
                                                    value="{{ $invoice->id }}" disabled>
                                            </div>
                                            <div class="card-footer">
                                                <button type="submit" class="btn btn-primary btn-sm" id="delivery_button">
                                                    عودة الجهاز
                                                </button>
                                                <div id="loadingMessage" class="spinner-border text-primary" role="status"
                                                    style="display: none;">
                                                    <span class="sr-only"> جاري عودة الجهاز </span>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>
                            document.getElementById("delivery_form").addEventListener("submit", function(e) {
                                e.preventDefault();
                                let submitBtn = this.querySelector('button[type="submit"]');
                                let loadingMessage = document.getElementById('loadingMessage');

                                submitBtn.disabled = true;
                                submitBtn.innerHTML = '<i class="la la-spinner la-spin"></i> جاري العودة...';

                                loadingMessage.style.display = 'block';
                                this.submit();

                            });
                        </script>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

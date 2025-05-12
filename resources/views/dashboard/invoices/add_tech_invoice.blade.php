@extends('dashboard.layouts.app')
@section('title', ' تعين فني الي الفاتورة ')
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="mb-2 content-header-left col-md-6 col-12 breadcrumb-new">
                    <h3 class="mb-0 content-header-title d-inline-block"> تعين فني الي الفاتورة </h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.welcome') }}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.invoices.index') }}"> الفواتير </a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#"> تعين فني الي الفاتورة </a>
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
                                    <h4 class="card-title" id="basic-layout-form"> تعين فني الي الفاتورة </h4>
                                    <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i> </a>
                                </div>
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <form id="add_tech_form" action="{{ route('dashboard.invoices.add_tech', $invoice->id) }}"
                                            method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label> رقم الفاتورة </label>
                                                <input type="text" class="form-control" value="{{ $invoice->id }}"
                                                    readonly>
                                            </div>
                                            <div class="form-group">
                                                <label> حدد الفني </label>
                                                <select name="admin_repair_id" id="" class="form-control">
                                                    <option value="" selected disabled> -- حدد الفني -- </option>
                                                    @foreach ($techs as $tech)
                                                        <option @selected($invoice->admin_repair_id == $tech->id) value="{{ $tech->id }}"> {{ $tech->name }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn grey btn-outline-secondary btn-sm"
                                                    data-dismiss="modal">رجوع
                                                </button>
                                                <button id="add_tech_button" type="submit" class="btn btn-primary btn-sm"> تعين الفني </button>
                                                <div id="loadingMessage" class="spinner-border text-primary" role="status" style="display: none;">
                                                    <span class="sr-only">جاري تعين الفني</span>
                                                </div>
                                            </div>
                                        </form>
                                        <script>
                                            document.getElementById('add_tech_form').addEventListener('submit', function(e) {
                                                e.preventDefault();
                                                document.getElementById('add_tech_button').style.display = 'none';
                                                document.getElementById('loadingMessage').style.display = 'block';
                                                this.submit();
                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

        </div>
    </div>
@endsection
